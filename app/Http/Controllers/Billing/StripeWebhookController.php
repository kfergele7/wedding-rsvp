<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\StripeWebhookEvent;
use App\Services\Billing\StripeBillingClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeWebhookController extends Controller
{
    public function __construct(private readonly StripeBillingClient $stripe)
    {
    }

    public function __invoke(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $signature = (string) $request->header('Stripe-Signature');

        if (! $this->isValidSignature($payload, $signature)) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        $event = json_decode($payload, true);
        if (! is_array($event) || ! isset($event['id'], $event['type'])) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $log = StripeWebhookEvent::query()->firstOrCreate(
            ['stripe_event_id' => (string) $event['id']],
            [
                'type' => (string) $event['type'],
                'payload' => $event,
            ]
        );

        if ($log->processed_at) {
            return response()->json(['received' => true, 'duplicate' => true]);
        }

        $account = $this->handleEvent($event);

        $log->update([
            'account_id' => $account?->id,
            'processed_at' => now(),
        ]);

        return response()->json(['received' => true]);
    }

    private function handleEvent(array $event): ?Account
    {
        $type = (string) $event['type'];
        $object = (array) ($event['data']['object'] ?? []);
        $resolvedAccount = null;

        if ($type === 'checkout.session.completed') {
            $accountId = (int) ($object['metadata']['account_id'] ?? $object['client_reference_id'] ?? 0);
            $account = $accountId > 0 ? Account::query()->find($accountId) : null;

            if (! $account && isset($object['customer'])) {
                $account = Account::query()->where('stripe_customer_id', (string) $object['customer'])->first();
            }

            if ($account) {
                $resolvedAccount = $account;
                $account->update([
                    'stripe_customer_id' => (string) ($object['customer'] ?? $account->stripe_customer_id),
                    'stripe_subscription_id' => (string) ($object['subscription'] ?? $account->stripe_subscription_id),
                ]);

                if ($account->stripe_subscription_id) {
                    $subscription = $this->stripe->retrieveSubscription($account->stripe_subscription_id);
                    $account->syncStripeSubscription($subscription);
                }
            }

            return $resolvedAccount;
        }

        if (in_array($type, ['customer.subscription.created', 'customer.subscription.updated', 'customer.subscription.deleted'], true)) {
            $customerId = (string) ($object['customer'] ?? '');

            if ($customerId === '') {
                return $resolvedAccount;
            }

            $account = Account::query()->where('stripe_customer_id', $customerId)->first();
            if (! $account) {
                return $resolvedAccount;
            }

            $resolvedAccount = $account;
            $account->syncStripeSubscription($object);
        }

        return $resolvedAccount;
    }

    private function isValidSignature(string $payload, string $signatureHeader): bool
    {
        $secret = (string) config('services.stripe.webhook_secret');
        if ($secret === '' || $signatureHeader === '') {
            return false;
        }

        $pairs = [];
        foreach (explode(',', $signatureHeader) as $segment) {
            [$key, $value] = array_pad(explode('=', trim($segment), 2), 2, null);
            if ($key && $value) {
                $pairs[$key][] = $value;
            }
        }

        $timestamp = $pairs['t'][0] ?? null;
        $signatures = $pairs['v1'] ?? [];

        if (! $timestamp || empty($signatures)) {
            return false;
        }

        $expected = hash_hmac('sha256', $timestamp.'.'.$payload, $secret);

        foreach ($signatures as $signature) {
            if (hash_equals($expected, $signature)) {
                return true;
            }
        }

        return false;
    }
}
