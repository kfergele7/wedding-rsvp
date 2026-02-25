<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Party;
use App\Models\Rsvp;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function app(string $section = 'dashboard')
    {
        $rsvpSettings = $this->rsvpSettings();

        return view('admin-app', [
            'page' => $section,
            'payload' => [
                'mealOptions' => $rsvpSettings['meal_options'] ?? [],
                'mealChoicesEnabled' => (($rsvpSettings['meal_mode'] ?? 'options') === 'options'),
            ],
        ]);
    }

    public function stats(): JsonResponse
    {
        $siteId = $this->currentSiteId();
        $invitedGuests = Guest::query()->forSite($siteId)->count();
        $attendingGuests = Rsvp::query()->forSite($siteId)->where('status', Rsvp::STATUS_ATTENDING)->sum('attending_count');
        $notAttendingParties = Rsvp::query()->forSite($siteId)->where('status', Rsvp::STATUS_NOT_ATTENDING)->count();
        $respondedPartyIds = Rsvp::query()->forSite($siteId)->pluck('party_id');

        return response()->json([
            'total_households' => Party::query()->forSite($siteId)->count(),
            'invited_guests' => $invitedGuests,
            'attending' => (int) $attendingGuests,
            'not_attending' => $notAttendingParties,
            'no_response' => Party::query()->forSite($siteId)->whereNotIn('id', $respondedPartyIds)->count(),
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
