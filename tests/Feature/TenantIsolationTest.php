<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Party;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantIsolationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_cannot_access_party_from_another_site_by_id(): void
    {
        [$siteA, $siteB] = $this->makeSites();

        $partyA = Party::query()->create([
            'site_id' => $siteA->id,
            'display_name' => 'Party A',
            'code' => 'ABCD',
            'max_guests' => 2,
        ]);

        $partyB = Party::query()->create([
            'site_id' => $siteB->id,
            'display_name' => 'Party B',
            'code' => 'WXYZ',
            'max_guests' => 2,
        ]);

        $this->withSession([
            'admin_authenticated' => true,
            'admin_site_id' => $siteA->id,
            'admin_account_id' => $siteA->account_id,
        ])->getJson("/admin/api/parties/{$partyA->id}")
            ->assertOk();

        $this->withSession([
            'admin_authenticated' => true,
            'admin_site_id' => $siteA->id,
            'admin_account_id' => $siteA->account_id,
        ])->getJson("/admin/api/parties/{$partyB->id}")
            ->assertNotFound();
    }

    public function test_admin_cannot_update_rsvp_for_party_from_another_site(): void
    {
        [$siteA, $siteB] = $this->makeSites();

        $partyB = Party::query()->create([
            'site_id' => $siteB->id,
            'display_name' => 'Party B',
            'code' => 'ZZZZ',
            'max_guests' => 3,
        ]);

        $this->withSession([
            'admin_authenticated' => true,
            'admin_site_id' => $siteA->id,
            'admin_account_id' => $siteA->account_id,
        ])->putJson("/admin/api/rsvps/{$partyB->id}", [
            'status' => 'attending',
            'attending_count' => 1,
            'meal_choices' => [],
            'dietary_restrictions' => null,
            'message' => null,
        ])->assertNotFound();
    }

    private function makeSites(): array
    {
        $accountA = Account::query()->create([
            'name' => 'Account A',
            'slug' => 'account-a',
            'status' => Account::STATUS_ACTIVE,
        ]);

        $accountB = Account::query()->create([
            'name' => 'Account B',
            'slug' => 'account-b',
            'status' => Account::STATUS_ACTIVE,
        ]);

        $siteA = Site::query()->create([
            'account_id' => $accountA->id,
            'title' => 'Site A',
            'public_slug' => 'site-a',
            'is_published' => true,
        ]);

        $siteB = Site::query()->create([
            'account_id' => $accountB->id,
            'title' => 'Site B',
            'public_slug' => 'site-b',
            'is_published' => true,
        ]);

        return [$siteA, $siteB];
    }
}
