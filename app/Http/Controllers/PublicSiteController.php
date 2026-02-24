<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    public function home(Request $request)
    {
        $lookupCode = $request->query('code');
        $rsvpSettings = $this->rsvpSettings();

        return response()
            ->view('app', [
                'page' => 'home',
                'payload' => [
                    'content' => $this->homepageContent(),
                    'rsvpSettings' => $rsvpSettings,
                    'rsvpCode' => $lookupCode,
                    'openRsvpModal' => (bool) $lookupCode,
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function rsvp(Request $request, ?string $code = null)
    {
        $lookupCode = $code ?: $request->query('code');
        $rsvpSettings = $this->rsvpSettings();

        return response()
            ->view('app', [
                'page' => 'home',
                'payload' => [
                    'content' => $this->homepageContent(),
                    'rsvpSettings' => $rsvpSettings,
                    'rsvpCode' => $lookupCode,
                    'openRsvpModal' => true,
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function homepageContent(): array
    {
        $fallback = config('wedding.homepage_content', []);
        $saved = SiteSetting::query()->where('key', 'homepage_content')->first();

        if (! $saved || ! is_array($saved->value)) {
            return $fallback;
        }

        return array_replace_recursive($fallback, $saved->value);
    }

    private function rsvpSettings(): array
    {
        $fallback = config('wedding.rsvp_settings', []);
        $saved = SiteSetting::query()->where('key', 'rsvp_settings')->first();

        if (! $saved || ! is_array($saved->value)) {
            return $fallback;
        }

        return array_replace_recursive($fallback, $saved->value);
    }

}
