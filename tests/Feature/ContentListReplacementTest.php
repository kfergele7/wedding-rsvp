<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Site;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentListReplacementTest extends TestCase
{
    use RefreshDatabase;

    public function test_deleted_list_items_do_not_reappear_after_content_save(): void
    {
        [$user, $site] = $this->createTenant();

        $payload = [
            'content' => [
                'timeline' => [
                    'items' => [
                        ['time' => '1:00 PM', 'title' => 'Ceremony', 'description' => 'Only timeline item'],
                    ],
                ],
                'details' => [
                    'faqs' => [
                        ['question' => 'Is parking available?', 'answer' => 'Yes'],
                    ],
                ],
            ],
            'rsvp_settings' => [
                'meal_mode' => 'options',
                'menu_courses' => [
                    [
                        'id' => 'starter',
                        'name' => 'Starter',
                        'items' => [
                            ['title' => 'Tomato Tart', 'description' => 'Single starter'],
                        ],
                    ],
                    [
                        'id' => 'main',
                        'name' => 'Main',
                        'items' => [
                            ['title' => 'Seabass', 'description' => 'Single main option'],
                        ],
                    ],
                    [
                        'id' => 'dessert',
                        'name' => 'Dessert',
                        'items' => [
                            ['title' => 'Lemon Posset', 'description' => 'Single dessert'],
                        ],
                    ],
                ],
            ],
        ];

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->putJson('/app/admin/api/content', $payload)
            ->assertOk()
            ->assertJsonCount(1, 'content.timeline.items')
            ->assertJsonCount(1, 'content.details.faqs')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.0.items')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.1.items')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.2.items');

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->getJson('/app/admin/api/content')
            ->assertOk()
            ->assertJsonCount(1, 'content.timeline.items')
            ->assertJsonCount(1, 'content.details.faqs')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.0.items')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.1.items')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.2.items');
    }

    private function createTenant(): array
    {
        $account = Account::query()->create([
            'name' => 'Content Tenant',
            'slug' => 'content-tenant',
            'status' => Account::STATUS_ACTIVE,
        ]);

        $site = Site::query()->create([
            'account_id' => $account->id,
            'title' => 'Content Site',
            'public_slug' => 'content-site',
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
