<?php

namespace App\Http\Controllers;

use App\Http\Requests\RsvpLookupRequest;
use App\Http\Requests\SaveRsvpRequest;
use App\Mail\HostRsvpNotificationMail;
use App\Models\Party;
use App\Models\Rsvp;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RsvpController extends Controller
{
    public function lookup(RsvpLookupRequest $request): JsonResponse
    {
        $this->ensureRsvpIsAccessible($request);

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
            'mealCourses' => $this->mealCoursesFromSettings($rsvpSettings),
            'kidsMenuItems' => $this->kidsMenuItemsFromSettings($rsvpSettings),
            'mealChoicesEnabled' => $this->partyUsesMealChoices($party, $rsvpSettings),
            'rsvpSettings' => $rsvpSettings,
            'saveUrl' => $this->saveUrlForParty($party),
        ]);
    }

    public function save(SaveRsvpRequest $request): JsonResponse
    {
        $this->ensureRsvpIsAccessible($request);

        $code = (string) $request->route('code');
        $party = $this->findParty($code);

        if (! $party) {
            return response()->json(['message' => 'Invalid RSVP code.'], 404);
        }

        $data = $request->validated();
        $rsvpSettings = $this->rsvpSettings();
        $mealChoicesEnabled = $this->partyUsesMealChoices($party, $rsvpSettings);

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

        $rsvp = $party->rsvp()->updateOrCreate(
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
        $this->sendHostNotification($request, $party, $rsvp);

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
            'guest_type' => $party->guest_type ?: Party::GUEST_TYPE_DAY,
            'guest_type_label' => $party->guestTypeLabel(),
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

    private function partyUsesMealChoices(Party $party, array $rsvpSettings): bool
    {
        if (($party->guest_type ?: Party::GUEST_TYPE_DAY) === Party::GUEST_TYPE_EVENING) {
            return false;
        }

        return (($rsvpSettings['meal_mode'] ?? 'options') === 'options');
    }

    private function mealOptionsFromSettings(array $rsvpSettings): array
    {
        return collect($this->mealCoursesFromSettings($rsvpSettings))
            ->pluck('items')
            ->flatten(1)
            ->merge($this->kidsMenuItemsFromSettings($rsvpSettings))
            ->pluck('title')
            ->map(fn ($title) => trim((string) $title))
            ->filter(fn ($title) => $title !== '')
            ->values()
            ->all();
    }

    private function mealCoursesFromSettings(array $rsvpSettings): array
    {
        $courseSections = $rsvpSettings['menu_courses'] ?? [];

        if (! is_array($courseSections)) {
            return [];
        }

        if (! array_is_list($courseSections)) {
            $courseSections = collect($courseSections)->map(function ($items, $name) {
                return [
                    'id' => strtolower(trim((string) $name)),
                    'name' => ucfirst((string) $name),
                    'items' => is_array($items) ? $items : [],
                ];
            })->values()->all();
        }

        return collect($courseSections)
            ->map(function ($section, $index) {
                return [
                    'id' => trim((string) ($section['id'] ?? 'course-'.($index + 1))),
                    'name' => trim((string) ($section['name'] ?? 'Course')),
                    'items' => collect($section['items'] ?? [])
                        ->map(fn ($item) => [
                            'title' => trim((string) ($item['title'] ?? '')),
                            'description' => trim((string) ($item['description'] ?? '')),
                        ])
                        ->filter(fn ($item) => $item['title'] !== '')
                        ->values()
                        ->all(),
                ];
            })
            ->filter(fn ($section) => count($section['items']) > 0)
            ->values()
            ->all();
    }

    private function kidsMenuItemsFromSettings(array $rsvpSettings): array
    {
        if (! ($rsvpSettings['kids_menu_enabled'] ?? false)) {
            return [];
        }

        return collect($rsvpSettings['kids_menu_items'] ?? [])
            ->map(fn ($item) => [
                'title' => trim((string) ($item['title'] ?? '')),
                'description' => trim((string) ($item['description'] ?? '')),
            ])
            ->filter(fn ($item) => $item['title'] !== '')
            ->values()
            ->all();
    }

    private function saveUrlForParty(Party $party): string
    {
        $site = $this->currentSite();

        if ($site?->public_slug) {
            return route('rsvp.save.slug', [
                'public_slug' => $site->public_slug,
                'code' => $party->code,
            ], false);
        }

        return route('rsvp.save', [
            'code' => $party->code,
        ], false);
    }

    private function ensureRsvpIsAccessible(Request $request): void
    {
        if ($this->currentSite()->is_published) {
            return;
        }

        $user = $request->user();

        if (! $user) {
            abort(404);
        }

        if ((bool) $user->is_staff) {
            return;
        }

        if ((int) $user->account_id !== (int) $this->currentSite()->account_id) {
            abort(404);
        }
    }

    private function sendHostNotification(Request $request, Party $party, Rsvp $rsvp): void
    {
        $site = $this->currentSite()->loadMissing(['account.users']);
        $owner = $site->account?->users
            ?->where('is_staff', false)
            ->sortByDesc(fn ($user) => ($user->role ?? '') === 'owner')
            ->first();

        if (! $owner?->email) {
            $previewUser = $request->user();
            if (
                $previewUser
                && ! $previewUser->is_staff
                && (int) $previewUser->account_id === (int) $site->account_id
                && $previewUser->email
            ) {
                $owner = $previewUser;
            }
        }

        if (! $owner?->email) {
            Log::info('Skipping host RSVP notification because no customer owner email was found.', [
                'party_id' => $party->id,
                'site_id' => $party->site_id,
                'account_id' => $site->account_id,
            ]);
            return;
        }

        try {
            Mail::to($owner->email)->send(new HostRsvpNotificationMail(
                siteTitle: $site->title ?: 'Your wedding website',
                partyName: $party->display_name,
                guestTypeLabel: $party->guestTypeLabel(),
                statusLabel: $rsvp->status === Rsvp::STATUS_ATTENDING ? 'Attending' : 'Not attending',
                attendingCount: (int) $rsvp->attending_count,
                maxGuests: (int) $party->max_guests,
                dietaryRestrictions: $rsvp->dietary_restrictions,
                messageFromGuest: $rsvp->message,
                mealChoices: is_array($rsvp->meal_choices) ? $rsvp->meal_choices : [],
                responsesUrl: route('customer.admin.rsvps.page'),
            ));
        } catch (\Throwable $exception) {
            Log::warning('Failed to send host RSVP notification email.', [
                'party_id' => $party->id,
                'site_id' => $party->site_id,
                'owner_email' => $owner->email,
                'error' => $exception->getMessage(),
            ]);
        }
    }
}
