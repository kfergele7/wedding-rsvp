<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use Illuminate\Http\Request;

class PublicSiteController extends Controller
{
    public function home(Request $request)
    {
        if (! $this->currentSite()->is_published) {
            return response()->view('site-coming-soon', ['site' => $this->currentSite()]);
        }

        if (! $this->currentSite()->account->hasActivePaidAccess()) {
            return response()->view('site-unavailable', ['site' => $this->currentSite()]);
        }

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
                    'publicSlug' => $this->currentSite()->public_slug,
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function rsvp(Request $request, ?string $code = null)
    {
        if (! $this->currentSite()->is_published) {
            return response()->view('site-coming-soon', ['site' => $this->currentSite()]);
        }

        if (! $this->currentSite()->account->hasActivePaidAccess()) {
            return response()->view('site-unavailable', ['site' => $this->currentSite()]);
        }

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
                    'publicSlug' => $this->currentSite()->public_slug,
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    public function showBySlug(Request $request, string $public_slug)
    {
        if ($this->currentSite()->public_slug !== $public_slug) {
            abort(404);
        }

        if (! $this->currentSite()->is_published) {
            return view('site-coming-soon', ['site' => $this->currentSite()]);
        }

        if (! $this->currentSite()->account->hasActivePaidAccess()) {
            return view('site-unavailable', ['site' => $this->currentSite()]);
        }

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
                    'publicSlug' => $this->currentSite()->public_slug,
                ],
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    private function homepageContent(): array
    {
        $fallback = config('wedding.homepage_content', []);
        $saved = SiteSetting::query()
            ->forSite($this->currentSiteId())
            ->where('key', 'homepage_content')
            ->first();

        if (! $saved || ! is_array($saved->value)) {
            return $fallback;
        }

        return array_replace_recursive($fallback, $saved->value);
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
