<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\PlatformSetting;
use App\Models\SiteSetting;
use Illuminate\Support\Facades\Schema;

class AdminController extends Controller
{
    public function app(string $section = 'dashboard')
    {
        $site = $this->currentSite();
        $rsvpSettings = $this->rsvpSettings();
        $fieldHelpTexts = $this->fieldHelpTexts();

        return view('admin-app', [
            'page' => $section,
            'payload' => [
                'mealOptions' => $rsvpSettings['meal_options'] ?? [],
                'mealChoicesEnabled' => (($rsvpSettings['meal_mode'] ?? 'options') === 'options'),
                'fieldHelpTexts' => $fieldHelpTexts,
                'logoutUrl' => route('logout'),
                'accountUrl' => route('customer.dashboard'),
                'currentUserEmail' => request()->user()?->email,
                'adminBaseUrl' => '/app/admin',
                'adminApiBaseUrl' => '/app/admin/api',
                'previewUrl' => route('wedding.public', ['public_slug' => $site->public_slug]),
                'siteTitle' => $site->title,
                'siteSettingsUpdateUrl' => route('customer.site.settings.update'),
                'hasPaidAccess' => (bool) $site->account?->hasActivePaidAccess(),
                'billingUrl' => route('customer.dashboard', ['tab' => 'account']),
                'sitePublished' => (bool) $site->is_published,
                'sitePublishUrl' => route('customer.site.publish'),
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

    private function fieldHelpTexts(): array
    {
        $defaults = collect(config('wedding.admin_field_help_texts', []))
            ->map(fn (mixed $definition) => (string) ($definition['default'] ?? ''))
            ->all();

        if (! Schema::hasTable('platform_settings')) {
            return $defaults;
        }

        $saved = PlatformSetting::query()
            ->where('key', 'admin_field_help_texts')
            ->first();

        if (! $saved || ! is_array($saved->value)) {
            return $defaults;
        }

        $overrides = collect($saved->value)
            ->mapWithKeys(function (mixed $value, mixed $key) {
                return [(string) $key => is_string($value) ? trim($value) : ''];
            })
            ->filter(fn (string $value) => $value !== '')
            ->all();

        return array_replace($defaults, $overrides);
    }
}
