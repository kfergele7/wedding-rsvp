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

    protected function tearDown(): void
    {
        config(['app.marketing_coming_soon' => false]);

        parent::tearDown();
    }

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
                'theme' => [
                    'layout' => 'modern',
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
            ->assertJsonPath('content.theme.layout', 'modern')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.0.items')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.1.items')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.2.items');

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->getJson('/app/admin/api/content')
            ->assertOk()
            ->assertJsonCount(1, 'content.timeline.items')
            ->assertJsonCount(1, 'content.details.faqs')
            ->assertJsonPath('content.theme.layout', 'modern')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.0.items')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.1.items')
            ->assertJsonCount(1, 'rsvp_settings.menu_courses.2.items');
    }

    public function test_colour_palette_selection_is_stored_as_slug_and_rendered_from_palette(): void
    {
        [$user, $site] = $this->createTenant();

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->getJson('/app/admin/api/content')
            ->assertOk()
            ->assertJsonPath('content.theme.palette', 'magic_classic')
            ->assertJsonPath('content.theme.palette_colours.primary', '#22363A')
            ->assertJsonFragment(['name' => 'Rose Veil']);

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->putJson('/app/admin/api/content', [
                'content' => [
                    'theme' => [
                        'layout' => 'modern',
                        'palette' => 'rose_veil',
                        'palette_colours' => [
                            'primary' => '#000000',
                        ],
                    ],
                ],
                'rsvp_settings' => [],
            ])
            ->assertOk()
            ->assertJsonPath('content.theme.layout', 'modern')
            ->assertJsonPath('content.theme.palette', 'rose_veil')
            ->assertJsonPath('content.theme.palette_colours.primary', '#B9A3AA');

        $savedContent = SiteSetting::query()
            ->where('site_id', $site->id)
            ->where('key', 'homepage_content')
            ->firstOrFail()
            ->value;

        $this->assertSame('rose_veil', $savedContent['theme']['palette']);
        $this->assertArrayNotHasKey('palette_colours', $savedContent['theme']);

        $this->get('/'.$site->public_slug)
            ->assertOk()
            ->assertSee('"layout":"modern"', false)
            ->assertSee('#B9A3AA');
    }

    public function test_evening_arrival_time_can_be_saved_and_loaded(): void
    {
        [$user, $site] = $this->createTenant();

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->putJson('/app/admin/api/content', [
                'content' => [
                    'guest_list' => [
                        'responseDeadline' => '2026-08-15',
                        'evening_arrival_time' => '19:30',
                    ],
                ],
                'rsvp_settings' => [],
            ])
            ->assertOk()
            ->assertJsonPath('content.guest_list.evening_arrival_time', '19:30');

        $this->actingAs($user)
            ->withSession(['current_site_id' => $site->id])
            ->getJson('/app/admin/api/content')
            ->assertOk()
            ->assertJsonPath('content.guest_list.evening_arrival_time', '19:30');

        $savedContent = SiteSetting::query()
            ->where('site_id', $site->id)
            ->where('key', 'homepage_content')
            ->firstOrFail()
            ->value;

        $this->assertSame('19:30', $savedContent['guest_list']['evening_arrival_time']);
    }

    public function test_demo_route_stays_available_while_marketing_is_coming_soon_and_includes_gallery(): void
    {
        $this->createTenant();

        config(['app.marketing_coming_soon' => true]);

        $this->get('/demo?preview_layout=modern')
            ->assertOk()
            ->assertSee('window.__APP_PAYLOAD', false)
            ->assertDontSee('Magic Invitation is coming soon')
            ->assertSee('"gallery"', false)
            ->assertSee('Us across the years', false)
            ->assertSee('demo-gallery-1.svg', false)
            ->assertSee('Whipped Goats Cheese', false)
            ->assertSee('Summer Berry Pavlova', false)
            ->assertSee('What time should guests arrive?', false);
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
