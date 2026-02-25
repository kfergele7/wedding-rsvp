<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;

class AdminController extends Controller
{
    public function app(string $section = 'dashboard')
    {
        $site = $this->currentSite();
        $rsvpSettings = $this->rsvpSettings();

        return view('admin-app', [
            'page' => $section,
            'payload' => [
                'mealOptions' => $rsvpSettings['meal_options'] ?? [],
                'mealChoicesEnabled' => (($rsvpSettings['meal_mode'] ?? 'options') === 'options'),
                'logoutUrl' => route('logout'),
                'accountUrl' => route('customer.dashboard'),
                'adminBaseUrl' => '/app/admin',
                'adminApiBaseUrl' => '/app/admin/api',
                'previewUrl' => route('wedding.public', ['public_slug' => $site->public_slug]),
            ],
        ]);
    }

    private function rsvpSettings(): array
    {
        $fallback = config('wedding.rsvp_settings', []);
        $saved = SiteSetting::query()
            ->forSite($this->currentSiteId())
            ->where('key', 'rsvp_settings')
            ->first();

        if (! $saved || ! is_array($saved->value)) {
            return $fallback;
        }

        return array_replace_recursive($fallback, $saved->value);
    }
}
