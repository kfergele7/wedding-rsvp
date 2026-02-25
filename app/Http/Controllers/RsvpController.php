<?php

namespace App\Http\Controllers;

use App\Http\Requests\RsvpLookupRequest;
use App\Http\Requests\SaveRsvpRequest;
use App\Models\Party;
use App\Models\Rsvp;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;

class RsvpController extends Controller
{
    public function lookup(RsvpLookupRequest $request): JsonResponse
    {
        $party = $this->findParty($request->validated('code'));
        $rsvpSettings = $this->rsvpSettings();

        if (! $party) {
            return response()->json([
                'message' => 'That RSVP code was not found. Please check and try again.',
            ], 422);
        }

        return response()->json([
            'party' => $this->partyPayload($party),
            'mealOptions' => $this->mealOptionsFromSettings($rsvpSettings),
            'mealChoicesEnabled' => (($rsvpSettings['meal_mode'] ?? 'options') === 'options'),
            'rsvpSettings' => $rsvpSettings,
        ]);
    }

    public function save(SaveRsvpRequest $request, string $code): JsonResponse
    {
        $party = $this->findParty($code);

        if (! $party) {
            return response()->json(['message' => 'Invalid RSVP code.'], 404);
        }

        $data = $request->validated();
        $rsvpSettings = $this->rsvpSettings();
        $mealChoicesEnabled = (($rsvpSettings['meal_mode'] ?? 'options') === 'options');

        $attendingCount = (int) $data['attending_count'];

        if ($attendingCount > $party->max_guests) {
            return response()->json([
                'message' => 'Attending count cannot exceed the maximum guests for your party.',
            ], 422);
        }

        if ($data['status'] === Rsvp::STATUS_NOT_ATTENDING) {
            $attendingCount = 0;
            $data['meal_choices'] = [];
        }

        if ($mealChoicesEnabled && $data['status'] === Rsvp::STATUS_ATTENDING && $attendingCount > 0) {
            $mealChoices = $data['meal_choices'] ?? [];
            if (count($mealChoices) !== $attendingCount) {
                return response()->json([
                    'message' => 'Please provide one meal selection for each attending guest.',
                ], 422);
            }
        }

        if (! $mealChoicesEnabled) {
            $data['meal_choices'] = [];
        }

        $party->rsvp()->updateOrCreate(
            ['party_id' => $party->id, 'site_id' => $party->site_id],
            [
                'site_id' => $party->site_id,
                'status' => $data['status'],
                'attending_count' => $attendingCount,
                'meal_choices' => $data['meal_choices'] ?? [],
                'dietary_restrictions' => $data['dietary_restrictions'] ?? null,
                'message' => $data['message'] ?? null,
            ]
        );

        $party->load(['guests', 'rsvp']);

        return response()->json([
            'message' => 'Your RSVP has been saved.',
            'party' => $this->partyPayload($party),
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

    private function findParty(string $code): ?Party
    {
        $normalizedCode = strtoupper(trim($code));

        return Party::query()
            ->forSite($this->currentSiteId())
            ->with(['guests', 'rsvp'])
            ->whereRaw('UPPER(code) = ?', [$normalizedCode])
            ->first();
    }

    private function partyPayload(Party $party): array
    {
        return [
            'id' => $party->id,
            'code' => $party->code,
            'display_name' => $party->display_name,
            'max_guests' => $party->max_guests,
            'notes' => $party->notes,
            'guests' => $party->guests->map(fn ($guest) => [
                'id' => $guest->id,
                'first_name' => $guest->first_name,
                'last_name' => $guest->last_name,
                'is_child' => $guest->is_child,
            ])->values(),
            'rsvp' => $party->rsvp ? [
                'status' => $party->rsvp->status,
                'attending_count' => $party->rsvp->attending_count,
                'meal_choices' => $party->rsvp->meal_choices ?? [],
                'dietary_restrictions' => $party->rsvp->dietary_restrictions,
                'message' => $party->rsvp->message,
                'updated_at' => $party->rsvp->updated_at?->toDateTimeString(),
            ] : null,
        ];
    }

    private function mealOptionsFromSettings(array $rsvpSettings): array
    {
        $mainCourseTitles = collect($rsvpSettings['menu_courses']['main'] ?? [])
            ->pluck('title')
            ->map(fn ($title) => trim((string) $title))
            ->filter(fn ($title) => $title !== '')
            ->values()
            ->all();

        if (count($mainCourseTitles) > 0) {
            return $mainCourseTitles;
        }

        return collect($rsvpSettings['meal_options'] ?? [])
            ->map(fn ($option) => trim((string) $option))
            ->filter(fn ($option) => $option !== '')
            ->values()
            ->all();
    }
}
