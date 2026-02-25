<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Party;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAdminTenantScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_cannot_view_or_edit_another_tenants_party(): void
    {
        [$userA, $siteA] = $this->createTenant('tenant-a', 'site-a');
        [, $siteB] = $this->createTenant('tenant-b', 'site-b');

        $partyA = Party::query()->create([
            'site_id' => $siteA->id,
            'display_name' => 'A Household',
            'code' => 'TEST',
            'max_guests' => 3,
        ]);
        $partyB = Party::query()->create([
            'site_id' => $siteB->id,
            'display_name' => 'B Household',
            'code' => 'TEST',
            'max_guests' => 3,
        ]);

        $this->actingAs($userA)
            ->withSession(['current_site_id' => $siteA->id])
            ->getJson("/app/admin/api/parties/{$partyA->id}")
            ->assertOk();

        $this->actingAs($userA)
            ->withSession(['current_site_id' => $siteA->id])
            ->getJson("/app/admin/api/parties/{$partyB->id}")
            ->assertNotFound();
    }

    public function test_manual_rsvp_entry_updates_current_tenant_record_only(): void
    {
        [$userA, $siteA] = $this->createTenant('tenant-c', 'site-c');
        $partyA = Party::query()->create([
            'site_id' => $siteA->id,
            'display_name' => 'Phone RSVP Household',
            'code' => 'CALL',
            'max_guests' => 4,
        ]);

        $this->actingAs($userA)
            ->withSession(['current_site_id' => $siteA->id])
            ->putJson("/app/admin/api/rsvps/{$partyA->id}", [
                'status' => 'attending',
                'attending_count' => 2,
                'meal_choices' => [],
                'dietary_restrictions' => 'No dairy',
                'message' => 'Entered by admin over phone',
            ])
            ->assertOk();

        $this->assertDatabaseHas('rsvps', [
            'party_id' => $partyA->id,
            'site_id' => $siteA->id,
            'status' => 'attending',
            'attending_count' => 2,
        ]);
    }

    public function test_rsvp_csv_export_contains_only_current_tenant_data(): void
    {
        [$userA, $siteA] = $this->createTenant('tenant-d', 'site-d');
        [, $siteB] = $this->createTenant('tenant-e', 'site-e');

        $partyA = Party::query()->create([
            'site_id' => $siteA->id,
            'display_name' => 'Alpha Household',
            'code' => 'ALPH',
            'max_guests' => 2,
        ]);
        $partyA->rsvp()->create([
            'site_id' => $siteA->id,
            'status' => 'attending',
            'attending_count' => 1,
        ]);

        $partyB = Party::query()->create([
            'site_id' => $siteB->id,
            'display_name' => 'Bravo Household',
            'code' => 'BRAV',
            'max_guests' => 2,
        ]);
        $partyB->rsvp()->create([
            'site_id' => $siteB->id,
            'status' => 'attending',
            'attending_count' => 2,
        ]);

        $response = $this->actingAs($userA)
            ->withSession(['current_site_id' => $siteA->id])
            ->get('/app/admin/api/rsvps/export')
            ->assertOk();

        $csv = $response->streamedContent();
        $this->assertStringContainsString('Alpha Household', $csv);
        $this->assertStringNotContainsString('Bravo Household', $csv);
    }

    public function test_public_slug_rsvp_lookup_is_scoped_to_slug_site(): void
    {
        [, $siteA] = $this->createTenant('tenant-f', 'site-f');
        [, $siteB] = $this->createTenant('tenant-g', 'site-g');

        Party::query()->create([
            'site_id' => $siteA->id,
            'display_name' => 'Site A Household',
            'code' => 'SAME',
            'max_guests' => 3,
        ]);

        Party::query()->create([
            'site_id' => $siteB->id,
            'display_name' => 'Site B Household',
            'code' => 'SAME',
            'max_guests' => 3,
        ]);

        $this->postJson("/w/{$siteA->public_slug}/rsvp/lookup", ['code' => 'SAME'])
            ->assertOk()
            ->assertJsonPath('party.display_name', 'Site A Household');

        $this->postJson("/w/{$siteB->public_slug}/rsvp/lookup", ['code' => 'SAME'])
            ->assertOk()
            ->assertJsonPath('party.display_name', 'Site B Household');
    }

    private function createTenant(string $accountSlug, string $siteSlug): array
    {
        $account = Account::query()->create([
            'name' => strtoupper($accountSlug),
            'slug' => $accountSlug,
            'status' => Account::STATUS_ACTIVE,
        ]);

        $site = Site::query()->create([
            'account_id' => $account->id,
            'title' => strtoupper($siteSlug),
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
            'account_id' => $account->id,
            'role' => 'owner',
            'email_verified_at' => now(),
        ]);

        return [$user, $site];
    }
}
