<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Party;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_staff_user_is_denied_staff_area(): void
    {
        $user = User::factory()->create(['is_staff' => false, 'email_verified_at' => now()]);

        $this->actingAs($user)->get('/staff')->assertForbidden();
    }

    public function test_staff_user_can_view_account_list(): void
    {
        Account::query()->create([
            'name' => 'Acme',
            'slug' => 'acme',
            'status' => Account::STATUS_ACTIVE,
        ]);

        $staff = User::factory()->create(['is_staff' => true, 'email_verified_at' => now()]);

        $this->actingAs($staff)->get('/staff/accounts')
            ->assertOk()
            ->assertSee('Acme');
    }

    public function test_staff_user_can_update_account_access_status_and_notes(): void
    {
        $account = Account::query()->create([
            'name' => 'Nova',
            'slug' => 'nova',
            'status' => Account::STATUS_DRAFT,
            'access_status' => 'active',
        ]);

        $staff = User::factory()->create(['is_staff' => true, 'email_verified_at' => now()]);

        $this->actingAs($staff)->put("/staff/accounts/{$account->id}", [
            'access_status' => 'suspended',
            'status' => 'gifted',
            'internal_notes' => 'Support hold requested.',
        ])->assertRedirect();

        $this->assertDatabaseHas('accounts', [
            'id' => $account->id,
            'access_status' => 'suspended',
            'status' => 'gifted',
            'internal_notes' => 'Support hold requested.',
        ]);

        $this->assertDatabaseHas('staff_audit_logs', [
            'staff_user_id' => $staff->id,
            'account_id' => $account->id,
            'action' => 'staff.account.updated',
        ]);
    }

    public function test_staff_user_cannot_bypass_customer_tenant_scope_in_customer_endpoints(): void
    {
        [$customerA, $siteA] = $this->makeTenant('acc-a', 'site-a');
        [, $siteB] = $this->makeTenant('acc-b', 'site-b');

        $partyB = Party::query()->create([
            'site_id' => $siteB->id,
            'display_name' => 'Hidden Party',
            'code' => 'HIDE',
            'max_guests' => 2,
        ]);

        $staff = User::factory()->create([
            'is_staff' => true,
            'account_id' => $customerA->account_id,
            'email_verified_at' => now(),
        ]);

        $this->actingAs($staff)
            ->withSession(['current_site_id' => $siteA->id])
            ->getJson("/app/admin/api/parties/{$partyB->id}")
            ->assertNotFound();
    }

    private function makeTenant(string $accountSlug, string $siteSlug): array
    {
        $account = Account::query()->create([
            'name' => strtoupper($accountSlug),
            'slug' => $accountSlug,
            'status' => Account::STATUS_ACTIVE,
            'access_status' => 'active',
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
