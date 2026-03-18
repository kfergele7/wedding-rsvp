<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Party;
use App\Models\PlatformSetting;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWeddingSlugRouteTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_slug_route_renders_correct_site_content(): void
    {
        $siteA = $this->makeSite('alpha-account', 'alpha-wedding', true, 'Ava & Liam');
        $siteB = $this->makeSite('bravo-account', 'bravo-wedding', true, 'Mia & Noah');

        $this->get('/w/'.$siteA->public_slug)
            ->assertOk()
            ->assertSee('Ava \\u0026 Liam', false)
            ->assertDontSee('Mia \\u0026 Noah', false);
    }

    public function test_unpublished_site_shows_coming_soon(): void
    {
        $site = $this->makeSite('draft-account', 'draft-wedding', false, 'Draft Couple');

        $this->get('/w/'.$site->public_slug)
            ->assertOk()
            ->assertSee('Coming Soon');
    }

    public function test_unpublished_site_owner_can_preview_public_slug(): void
    {
        $site = $this->makeSite('owner-preview-account', 'owner-preview-site', false, 'Owner Preview Couple');
        $owner = $this->makeUserForAccount($site->account_id, 'owner-preview@example.test');

        $this->actingAs($owner)
            ->get('/w/'.$site->public_slug)
            ->assertOk()
            ->assertSee('Owner Preview Couple')
            ->assertSee('Preview mode')
            ->assertDontSee('Coming Soon');
    }

    public function test_unpublished_site_shows_coming_soon_for_other_logged_in_accounts(): void
    {
        $site = $this->makeSite('draft-account-2', 'draft-wedding-2', false, 'Private Couple');
        $otherAccount = Account::query()->create([
            'name' => 'Other Account',
            'slug' => 'other-account',
            'status' => Account::STATUS_ACTIVE,
        ]);
        $otherUser = $this->makeUserForAccount($otherAccount->id, 'other-user@example.test');

        $this->actingAs($otherUser)
            ->get('/w/'.$site->public_slug)
            ->assertOk()
            ->assertSee('Coming Soon')
            ->assertDontSee('Private Couple');
    }

    public function test_unpublished_site_rsvp_lookup_requires_same_account_user(): void
    {
        $site = $this->makeSite('draft-rsvp-account', 'draft-rsvp-site', false, 'Draft RSVP Couple');
        $owner = $this->makeUserForAccount($site->account_id, 'draft-owner@example.test');

        Party::query()->create([
            'site_id' => $site->id,
            'display_name' => 'Draft Household',
            'code' => 'DRFTA',
            'max_guests' => 2,
        ]);

        $this->postJson('/w/'.$site->public_slug.'/rsvp/lookup', ['code' => 'DRFTA'])
            ->assertNotFound();

        $this->actingAs($owner)
            ->postJson('/w/'.$site->public_slug.'/rsvp/lookup', ['code' => 'DRFTA'])
            ->assertOk()
            ->assertJsonPath('party.display_name', 'Draft Household');
    }

    public function test_public_slug_rsvp_save_uses_code_route_parameter_not_slug(): void
    {
        $site = $this->makeSite('save-route-account', 'save-route-site', true, 'Save Route Couple');

        Party::query()->create([
            'site_id' => $site->id,
            'display_name' => 'Kane',
            'code' => 'VQVA',
            'max_guests' => 2,
        ]);

        $this->postJson('/w/'.$site->public_slug.'/rsvp/VQVA', [
            'status' => 'attending',
            'attending_count' => 2,
            'meal_choices' => [
                ['guest_name' => 'Jonny Kane', 'meal' => 'Starter: Tart | Main: Beef | Dessert: Lemon', 'selections' => [
                    'starter' => 'Heirloom Tomato Tart',
                    'main' => 'Beef Fillet',
                    'dessert' => 'Lemon Posset',
                ]],
                ['guest_name' => 'Ellie Kane', 'meal' => 'Starter: Tart | Main: Seabass | Dessert: Lemon', 'selections' => [
                    'starter' => 'Heirloom Tomato Tart',
                    'main' => 'Seabass',
                    'dessert' => 'Lemon Posset',
                ]],
            ],
        ])->assertOk()
            ->assertJsonPath('party.code', 'VQVA')
            ->assertJsonPath('party.rsvp.status', 'attending')
            ->assertJsonPath('party.rsvp.attending_count', 2);
    }

    public function test_staff_user_can_preview_any_unpublished_site_and_use_rsvp_lookup(): void
    {
        $site = $this->makeSite('staff-preview-account', 'staff-preview-site', false, 'Staff Preview Couple');
        $staff = User::factory()->create([
            'account_id' => null,
            'email' => 'staff-preview@example.test',
        ]);
        $staff->forceFill(['is_staff' => true])->save();

        Party::query()->create([
            'site_id' => $site->id,
            'display_name' => 'Staff Household',
            'code' => 'STAFF',
            'max_guests' => 2,
        ]);

        $this->actingAs($staff)
            ->get('/w/'.$site->public_slug)
            ->assertOk()
            ->assertSee('Staff Preview Couple')
            ->assertSee('Staff preview mode')
            ->assertDontSee('Coming Soon');

        $this->actingAs($staff)
            ->postJson('/w/'.$site->public_slug.'/rsvp/lookup', ['code' => 'STAFF'])
            ->assertOk()
            ->assertJsonPath('party.display_name', 'Staff Household');
    }

    public function test_demo_route_uses_selected_demo_source_site_content(): void
    {
        $siteA = $this->makeSite('demo-account-a', 'demo-site-a', true, 'Demo A Couple');
        $siteB = $this->makeSite('demo-account-b', 'demo-site-b', true, 'Demo B Couple');

        PlatformSetting::query()->create([
            'key' => 'demo_template_source',
            'value' => ['site_id' => $siteB->id],
        ]);

        $this->get('/demo')
            ->assertOk()
            ->assertSee('Demo B Couple')
            ->assertDontSee('Demo A Couple');
    }

    private function makeSite(string $accountSlug, string $siteSlug, bool $published, string $names): Site
    {
        $account = Account::query()->create([
            'name' => ucfirst(str_replace('-', ' ', $accountSlug)),
            'slug' => $accountSlug,
            'status' => Account::STATUS_ACTIVE,
        ]);

        $site = Site::query()->create([
            'account_id' => $account->id,
            'title' => ucfirst(str_replace('-', ' ', $siteSlug)),
            'public_slug' => $siteSlug,
            'is_published' => $published,
        ]);

        $content = config('wedding.homepage_content');
        $content['hero']['names'] = $names;

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

        return $site;
    }

    private function makeUserForAccount(int $accountId, string $email): User
    {
        return User::factory()->create([
            'account_id' => $accountId,
            'role' => 'owner',
            'email' => $email,
            'email_verified_at' => now(),
        ]);
    }
}
