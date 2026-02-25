<?php

namespace Tests\Feature;

use App\Models\Account;
use App\Models\Site;
use App\Models\SiteSetting;
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
}
