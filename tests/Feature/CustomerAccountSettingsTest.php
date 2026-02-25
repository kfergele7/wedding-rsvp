<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\CustomVerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CustomerAccountSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_update_profile_and_triggers_reverification_when_email_changes(): void
    {
        Notification::fake();

        [$user, $site] = $this->makeCustomer();

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->put(route('customer.account.profile.update'), [
                'name' => 'Updated Name',
                'email' => 'updated@example.com',
            ])
            ->assertRedirect(route('customer.dashboard', ['tab' => 'account']));

        $user->refresh();

        $this->assertSame('Updated Name', $user->name);
        $this->assertSame('updated@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, CustomVerifyEmailNotification::class);
    }

    public function test_customer_can_update_password_with_current_password(): void
    {
        [$user, $site] = $this->makeCustomer();

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->put(route('customer.account.password.update'), [
                'current_password' => 'OldPassword123!',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ])
            ->assertRedirect(route('customer.dashboard', ['tab' => 'account']));

        $this->assertTrue(Hash::check('NewPassword123!', $user->fresh()->password));
    }

    private function makeCustomer(): array
    {
        $account = Account::query()->create([
            'name' => 'Settings Account',
            'slug' => 'settings-account',
            'status' => Account::STATUS_ACTIVE,
        ]);

        $site = Site::query()->create([
            'account_id' => $account->id,
            'title' => 'Settings Wedding',
            'public_slug' => 'settings-'.str()->lower(str()->random(6)),
            'is_published' => true,
        ]);

        SiteSetting::query()->create([
            'site_id' => $site->id,
            'key' => 'homepage_content',
            'value' => config('wedding.homepage_content'),
        ]);
        SiteSetting::query()->create([
            'site_id' => $site->id,
            'key' => 'rsvp_settings',
            'value' => config('wedding.rsvp_settings'),
        ]);

        $user = User::factory()->create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('OldPassword123!'),
            'account_id' => $account->id,
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);

        return [$user, $site];
    }
}

