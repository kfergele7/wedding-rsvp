<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\CustomVerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CustomerOnboardingTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_account_site_and_owner_link(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Taylor Green',
            'email' => 'taylor@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('verification.notice'));
        $this->assertAuthenticated();

        $user = User::query()->where('email', 'taylor@example.com')->firstOrFail();
        $this->assertSame('owner', $user->role);
        $this->assertNotNull($user->account_id);

        $account = Account::query()->findOrFail($user->account_id);
        $this->assertSame(Account::STATUS_DRAFT, $account->status);
        $site = Site::query()->where('account_id', $account->id)->first();

        $this->assertNotNull($site);
        $this->assertSame(false, (bool) $site->is_published);
        $this->assertMatchesRegularExpression('/^w-[a-z0-9]{10}$/', $site->public_slug);
        $this->assertStringNotContainsString('taylor', $site->public_slug);
        $this->assertDatabaseHas('site_settings', ['site_id' => $site->id, 'key' => 'homepage_content']);
        $this->assertDatabaseHas('site_settings', ['site_id' => $site->id, 'key' => 'rsvp_settings']);

        Notification::assertSentTo($user, CustomVerifyEmailNotification::class);
    }

    public function test_user_cannot_view_another_accounts_dashboard_context(): void
    {
        [$userA, $siteA] = $this->makeCustomer('alpha@example.com', 'Alpha Account', 'alpha-site');
        [$userB, $siteB] = $this->makeCustomer('bravo@example.com', 'Bravo Account', 'bravo-site');

        $this->actingAs($userA)
            ->withSession(['current_site_id' => $siteB->id])
            ->get('/app')
            ->assertOk()
            ->assertSee($siteA->public_slug)
            ->assertDontSee($siteB->public_slug)
            ->assertDontSee($userB->account->name);
    }

    private function makeCustomer(string $email, string $accountName, string $siteSlug): array
    {
        $account = Account::query()->create([
            'name' => $accountName,
            'slug' => str_replace(' ', '-', strtolower($accountName)),
            'status' => Account::STATUS_ACTIVE,
        ]);

        $site = Site::query()->create([
            'account_id' => $account->id,
            'title' => $accountName.' Site',
            'public_slug' => $siteSlug,
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
            'email' => $email,
            'account_id' => $account->id,
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);

        return [$user, $site];
    }
}
