<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class BillingPaywallTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_account_cannot_publish_site(): void
    {
        [$user, $site] = $this->makeTenant(Account::STATUS_DRAFT, false);

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->put('/app/site/publish', ['is_published' => 1])
            ->assertSessionHasErrors('publish');

        $this->assertFalse((bool) $site->fresh()->is_published);
    }

    public function test_active_subscription_allows_public_site_access(): void
    {
        [, $site] = $this->makeTenant(Account::STATUS_ACTIVE, true, 'Alex & Jordan');

        $this->get('/w/'.$site->public_slug)
            ->assertOk()
            ->assertSee('Alex \\u0026 Jordan', false);
    }

    public function test_cancelled_at_period_end_remains_public_until_end_date(): void
    {
        [, $site] = $this->makeTenant(
            status: Account::STATUS_CANCELLED,
            published: true,
            heroNames: 'Casey & Drew',
            periodEnd: now()->addDays(10)
        );

        $this->get('/w/'.$site->public_slug)
            ->assertOk()
            ->assertSee('Casey \\u0026 Drew', false);
    }

    public function test_gifted_subscription_allows_public_site_access(): void
    {
        [, $site] = $this->makeTenant(Account::STATUS_GIFTED, true, 'Taylor & Morgan');

        $this->get('/w/'.$site->public_slug)
            ->assertOk()
            ->assertSee('Taylor \\u0026 Morgan', false);
    }

    public function test_gifted_subscription_hides_customer_cancel_controls(): void
    {
        [$user, $site] = $this->makeTenant(Account::STATUS_GIFTED, true, 'Avery & Quinn');

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->get('/app')
            ->assertOk()
            ->assertSee('gifted subscription', false)
            ->assertDontSee('Cancel At Period End')
            ->assertDontSee('Manage Billing');
    }

    private function makeTenant(string $status, bool $published, string $heroNames = 'Names', ?Carbon $periodEnd = null): array
    {
        $account = Account::query()->create([
            'name' => 'Tenant Account',
            'slug' => 'tenant-account-'.str()->random(6),
            'status' => $status,
            'subscription_current_period_end' => $periodEnd,
            'subscription_cancel_at_period_end' => $status === Account::STATUS_CANCELLED,
        ]);

        $site = Site::query()->create([
            'account_id' => $account->id,
            'title' => 'Tenant Wedding',
            'public_slug' => 'tenant-'.str()->random(8),
            'is_published' => $published,
        ]);

        $content = config('wedding.homepage_content');
        $content['hero']['names'] = $heroNames;

        SiteSetting::query()->create([
            'site_id' => $site->id,
            'key' => 'homepage_content',
            'value' => $content,
        ]);
        SiteSetting::query()->create([
            'site_id' => $site->id,
            'key' => 'rsvp_settings',
            'value' => config('wedding.rsvp_settings'),
        ]);

        $user = User::factory()->create([
            'account_id' => $account->id,
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);

        return [$user, $site];
    }
}
