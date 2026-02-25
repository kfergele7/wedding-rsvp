<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\StripeWebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripeWebhookTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscription_webhook_updates_account_status(): void
    {
        $account = Account::query()->create([
            'name' => 'Webhook Account',
            'slug' => 'webhook-account',
            'status' => Account::STATUS_DRAFT,
            'stripe_customer_id' => 'cus_123',
        ]);

        config()->set('services.stripe.webhook_secret', 'whsec_test');

        $event = [
            'id' => 'evt_test_1',
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => 'sub_123',
                    'customer' => 'cus_123',
                    'status' => 'active',
                    'cancel_at_period_end' => false,
                    'current_period_end' => now()->addDays(30)->timestamp,
                    'items' => [
                        'data' => [
                            ['price' => ['id' => 'price_monthly_123']],
                        ],
                    ],
                ],
            ],
        ];

        $payload = json_encode($event, JSON_THROW_ON_ERROR);
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_test');

        $this->postJson('/stripe/webhook', $event, [
            'Stripe-Signature' => "t={$timestamp},v1={$signature}",
        ])->assertOk();

        $account->refresh();

        $this->assertSame(Account::STATUS_ACTIVE, $account->status);
        $this->assertSame('sub_123', $account->stripe_subscription_id);
        $this->assertSame('price_monthly_123', $account->stripe_price_id);
        $this->assertDatabaseHas('stripe_webhook_events', [
            'stripe_event_id' => 'evt_test_1',
            'type' => 'customer.subscription.updated',
        ]);
    }

    public function test_webhook_is_idempotent_for_duplicate_event_ids(): void
    {
        $account = Account::query()->create([
            'name' => 'Webhook Account',
            'slug' => 'webhook-account-2',
            'status' => Account::STATUS_DRAFT,
            'stripe_customer_id' => 'cus_abc',
        ]);

        config()->set('services.stripe.webhook_secret', 'whsec_test');

        $event = [
            'id' => 'evt_duplicate_1',
            'type' => 'customer.subscription.updated',
            'data' => [
                'object' => [
                    'id' => 'sub_abc',
                    'customer' => 'cus_abc',
                    'status' => 'past_due',
                    'cancel_at_period_end' => false,
                    'current_period_end' => now()->addDays(5)->timestamp,
                    'items' => [
                        'data' => [
                            ['price' => ['id' => 'price_x']],
                        ],
                    ],
                ],
            ],
        ];

        $payload = json_encode($event, JSON_THROW_ON_ERROR);
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', $timestamp.'.'.$payload, 'whsec_test');
        $headers = ['Stripe-Signature' => "t={$timestamp},v1={$signature}"];

        $this->postJson('/stripe/webhook', $event, $headers)->assertOk();
        $this->postJson('/stripe/webhook', $event, $headers)->assertOk();

        $this->assertSame(1, StripeWebhookEvent::query()->where('stripe_event_id', 'evt_duplicate_1')->count());

        $account->refresh();
        $this->assertSame(Account::STATUS_PAST_DUE, $account->status);
    }
}
