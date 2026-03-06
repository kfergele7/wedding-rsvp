<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_update_site_title(): void
    {
        [$user, $site] = $this->makeCustomerAndSite();

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->put(route('customer.site.settings.update'), [
                'title' => 'Kyle and Nicole Wedding',
            ])
            ->assertRedirect(route('customer.dashboard'));

        $this->assertDatabaseHas('sites', [
            'id' => $site->id,
            'title' => 'Kyle and Nicole Wedding',
        ]);
    }

    private function makeCustomerAndSite(): array
    {
        $account = Account::query()->create([
            'name' => 'Site Settings Account',
            'slug' => 'site-settings-account',
            'status' => Account::STATUS_ACTIVE,
        ]);

        $site = Site::query()->create([
            'account_id' => $account->id,
            'title' => 'Original Wedding Site',
            'public_slug' => 'w-abc123',
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
            'account_id' => $account->id,
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);

        return [$user, $site];
    }
}
