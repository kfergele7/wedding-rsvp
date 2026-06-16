<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportPartiesRequest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\StorePartyRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Mail\PartyRsvpInviteMail;
use App\Mail\PartyRsvpReminderMail;
use App\Models\Guest;
use App\Models\Party;
use App\Models\RsvpEmailLog;
use App\Models\SiteSetting;
use App\Support\InvitationTiming;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class PartyController extends Controller
{
    public function generateCode(): JsonResponse
    {
        $siteId = $this->currentSiteId();

        return response()->json([
            'code' => Party::generateCode($siteId, 4),
        ]);
    }

    public function index(): JsonResponse
    {
        $parties = Party::query()
            ->forSite($this->currentSiteId())
            ->with(['guests', 'rsvp'])
            ->withCount('rsvpEmailLogs')
            ->withMax('rsvpEmailLogs', 'sent_at')
            ->orderBy('display_name')
            ->get();

        return response()->json([
            'parties' => $parties->map(fn (Party $party) => $this->partyPayload($party))->values(),
        ]);
    }

    public function store(StorePartyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $siteId = $this->currentSiteId();
        $guests = collect($data['guests'] ?? [])
            ->filter(function ($guest) {
                $firstName = trim((string) ($guest['first_name'] ?? ''));
                $lastName = trim((string) ($guest['last_name'] ?? ''));

                return $firstName !== '' && $lastName !== '';
            })
            ->map(function ($guest) use ($siteId) {
                return [
                    'site_id' => $siteId,
                    'first_name' => trim((string) ($guest['first_name'] ?? '')),
                    'last_name' => trim((string) ($guest['last_name'] ?? '')),
                    'is_child' => (bool) ($guest['is_child'] ?? false),
                    'allow_plus_one' => (bool) ($guest['allow_plus_one'] ?? false),
                ];
            })
            ->values();

        $computedInvitedSeats = $guests->count() + $guests->where('allow_plus_one', true)->count();
        $maxGuests = max(
            (int) ($data['max_guests'] ?? 1),
            $computedInvitedSeats > 0 ? $computedInvitedSeats : 1
        );
        $code = isset($data['code']) && $data['code'] !== ''
            ? strtoupper(trim($data['code']))
            : Party::generateCode($siteId, 4);

        $party = Party::query()->create([
            'site_id' => $siteId,
            'display_name' => $data['display_name'],
            'guest_type' => $data['guest_type'] ?? Party::GUEST_TYPE_DAY,
            'email' => $data['email'] ?? null,
            'code' => $code,
            'max_guests' => $maxGuests,
            'notes' => $data['notes'] ?? null,
        ]);

        if ($guests->isNotEmpty()) {
            $party->guests()->createMany($guests->all());
        }

        $party->load(['guests', 'rsvp'])
            ->loadCount('rsvpEmailLogs')
            ->loadMax('rsvpEmailLogs', 'sent_at');

        return response()->json([
            'message' => 'Party created.',
            'party' => $this->partyPayload($party),
        ], 201);
    }

    public function show(Party $party): JsonResponse
    {
        $party->load(['guests', 'rsvp'])
            ->loadCount('rsvpEmailLogs')
            ->loadMax('rsvpEmailLogs', 'sent_at');

        return response()->json(['party' => $this->partyPayload($party)]);
    }

    public function update(UpdatePartyRequest $request, Party $party): JsonResponse
    {
        $data = $request->validated();

        $party->update([
            'display_name' => $data['display_name'],
            'guest_type' => $data['guest_type'],
            'email' => $data['email'] ?? null,
            'code' => strtoupper(trim($data['code'])),
            'max_guests' => max($data['max_guests'], $this->minimumSeatsForParty($party)),
            'notes' => $data['notes'] ?? null,
        ]);

        $party->load(['guests', 'rsvp'])
            ->loadCount('rsvpEmailLogs')
            ->loadMax('rsvpEmailLogs', 'sent_at');

        return response()->json([
            'message' => 'Party updated.',
            'party' => $this->partyPayload($party),
        ]);
    }

    public function destroy(Party $party): JsonResponse
    {
        $party->delete();

        return response()->json(['message' => 'Party deleted.']);
    }

    public function storeGuest(StoreGuestRequest $request, Party $party): JsonResponse
    {
        $guest = $party->guests()->create([
            ...$request->validated(),
            'site_id' => $party->site_id,
        ]);
        $this->syncPartySeatLimit($party);

        return response()->json([
            'message' => 'Guest added.',
            'guest' => $guest,
        ], 201);
    }

    public function updateGuest(UpdateGuestRequest $request, Guest $guest): JsonResponse
    {
        $guest->update($request->validated());
        $this->syncPartySeatLimit($guest->party);

        return response()->json([
            'message' => 'Guest updated.',
            'guest' => $guest,
        ]);
    }

    public function destroyGuest(Guest $guest): JsonResponse
    {
        $party = $guest->party;
        $guest->delete();
        $this->syncPartySeatLimit($party);

        return response()->json(['message' => 'Guest removed.']);
    }

    public function import(ImportPartiesRequest $request): JsonResponse
    {
        $rows = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));
        $siteId = $this->currentSiteId();

        if (count($rows) < 2) {
            return response()->json(['message' => 'CSV must include a header row and at least one data row.'], 422);
        }

        $headers = array_map(fn ($header) => strtolower(trim((string) $header)), $rows[0]);

        $required = ['party_display_name', 'max_guests', 'first_name', 'last_name'];
        foreach ($required as $field) {
            if (! in_array($field, $headers, true)) {
                return response()->json(['message' => "Missing required CSV header: {$field}"], 422);
            }
        }

        $createdParties = 0;
        $createdGuests = 0;

        DB::transaction(function () use ($siteId, $rows, $headers, &$createdParties, &$createdGuests) {
            foreach (array_slice($rows, 1) as $row) {
                if (count(array_filter($row, fn ($value) => trim((string) $value) !== '')) === 0) {
                    continue;
                }

                $payload = [];
                foreach ($headers as $index => $header) {
                    $payload[$header] = trim((string) ($row[$index] ?? ''));
                }

                $code = $payload['code'] !== ''
                    ? strtoupper($payload['code'])
                    : Party::generateCode($siteId, 4);

                $party = Party::query()->firstOrCreate(
                    ['site_id' => $siteId, 'code' => $code],
                    [
                        'site_id' => $siteId,
                        'display_name' => $payload['party_display_name'],
                        'guest_type' => in_array(strtolower((string) ($payload['guest_type'] ?? 'day')), Party::guestTypes(), true)
                            ? strtolower((string) $payload['guest_type'])
                            : Party::GUEST_TYPE_DAY,
                        'email' => $payload['email'] !== '' ? strtolower($payload['email']) : null,
                        'max_guests' => max(1, (int) ($payload['max_guests'] ?: 1)),
                        'notes' => $payload['notes'] ?: null,
                    ]
                );

                if ($party->wasRecentlyCreated) {
                    $createdParties++;
                }

                if (($payload['first_name'] ?? '') !== '' && ($payload['last_name'] ?? '') !== '') {
                    $party->guests()->create([
                        'site_id' => $siteId,
                        'first_name' => $payload['first_name'],
                        'last_name' => $payload['last_name'],
                        'is_child' => in_array(strtolower((string) ($payload['is_child'] ?? '')), ['1', 'true', 'yes', 'y'], true),
                        'allow_plus_one' => in_array(strtolower((string) ($payload['allow_plus_one'] ?? '')), ['1', 'true', 'yes', 'y'], true),
                    ]);
                    $createdGuests++;
                }
            }
        });

        return response()->json([
            'message' => 'CSV import complete.',
            'created_parties' => $createdParties,
            'created_guests' => $createdGuests,
        ]);
    }

    public function export(Request $request)
    {
        $filename = 'households_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['party_display_name', 'guest_type', 'email', 'code', 'max_guests', 'notes', 'first_name', 'last_name', 'is_child', 'allow_plus_one']);

            Party::query()
                ->forSite($this->currentSiteId())
                ->with('guests')
                ->orderBy('display_name')
                ->chunk(200, function ($parties) use ($handle) {
                foreach ($parties as $party) {
                    if ($party->guests->isEmpty()) {
                        fputcsv($handle, [$party->display_name, $party->guest_type, $party->email, $party->code, $party->max_guests, $party->notes, '', '', '', '']);
                        continue;
                    }

                    foreach ($party->guests as $guest) {
                        fputcsv($handle, [
                            $party->display_name,
                            $party->guest_type,
                            $party->email,
                            $party->code,
                            $party->max_guests,
                            $party->notes,
                            $guest->first_name,
                            $guest->last_name,
                            $guest->is_child ? 'yes' : 'no',
                            $guest->allow_plus_one ? 'yes' : 'no',
                        ]);
                    }
                }
                });

            fclose($handle);
        }, 200, $headers);
    }

    public function sendRsvpEmails(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'party_ids' => ['required', 'array', 'min:1'],
            'party_ids.*' => ['required', 'integer'],
        ]);

        $site = $this->currentSite();
        if ($blockedResponse = $this->blockedRsvpEmailResponse($site)) {
            return $blockedResponse;
        }

        $siteUrl = route('wedding.public', ['public_slug' => $site->public_slug]);
        $content = $this->resolvedHomepageContent();
        $formattedResponseDeadline = $this->formatResponseDeadline(
            (string) data_get($content, 'guest_list.responseDeadline', '')
        );
        $eveningArrivalTime = data_get($content, 'guest_list.evening_arrival_time');

        $parties = Party::query()
            ->forSite($this->currentSiteId())
            ->whereIn('id', $validated['party_ids'])
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->get();

        if ($parties->isEmpty()) {
            return response()->json(['message' => 'No parties with valid email addresses were selected.'], 422);
        }

        foreach ($parties as $party) {
            $rsvpUrl = route('wedding.public', ['public_slug' => $site->public_slug]).'?code='.$party->code;

            Mail::to($party->email)->send(new PartyRsvpInviteMail(
                siteTitle: $site->title,
                partyName: $party->display_name,
                guestTypeLabel: $party->guestTypeLabel(),
                rsvpCode: $party->code,
                websiteUrl: $siteUrl,
                rsvpUrl: $rsvpUrl,
                responseDeadline: $formattedResponseDeadline,
                eveningArrivalTime: InvitationTiming::eveningArrivalTimeForGuestType($party->guest_type, $eveningArrivalTime),
            ));

            RsvpEmailLog::query()->create([
                'site_id' => $party->site_id,
                'party_id' => $party->id,
                'sent_by_user_id' => auth()->id(),
                'sent_to_email' => $party->email,
                'type' => RsvpEmailLog::TYPE_INVITE,
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'RSVP request emails sent.',
            'sent' => $parties->count(),
        ]);
    }

    public function sendRsvpReminderEmails(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'party_ids' => ['required', 'array', 'min:1'],
            'party_ids.*' => ['required', 'integer'],
        ]);

        $site = $this->currentSite();
        if ($blockedResponse = $this->blockedRsvpEmailResponse($site)) {
            return $blockedResponse;
        }

        $siteUrl = route('wedding.public', ['public_slug' => $site->public_slug]);
        $content = $this->resolvedHomepageContent();
        $formattedResponseDeadline = $this->formatResponseDeadline(
            (string) data_get($content, 'guest_list.responseDeadline', '')
        );
        $eveningArrivalTime = data_get($content, 'guest_list.evening_arrival_time');
        if (! $formattedResponseDeadline) {
            return response()->json([
                'message' => 'Set an RSVP response deadline before sending reminders.',
            ], 422);
        }

        $parties = Party::query()
            ->forSite($this->currentSiteId())
            ->whereIn('id', $validated['party_ids'])
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->where(function ($query) {
                $query->whereDoesntHave('rsvp')
                    ->orWhereHas('rsvp', function ($rsvpQuery) {
                        $rsvpQuery->whereNull('status')
                            ->orWhere('status', '');
                    });
            })
            ->get();

        if ($parties->isEmpty()) {
            return response()->json([
                'message' => 'No parties with email addresses are waiting for an RSVP response.',
            ], 422);
        }

        foreach ($parties as $party) {
            $rsvpUrl = route('wedding.public', ['public_slug' => $site->public_slug]).'?code='.$party->code;

            Mail::to($party->email)->send(new PartyRsvpReminderMail(
                siteTitle: $site->title,
                partyName: $party->display_name,
                guestTypeLabel: $party->guestTypeLabel(),
                rsvpCode: $party->code,
                websiteUrl: $siteUrl,
                rsvpUrl: $rsvpUrl,
                responseDeadline: $formattedResponseDeadline,
                eveningArrivalTime: InvitationTiming::eveningArrivalTimeForGuestType($party->guest_type, $eveningArrivalTime),
            ));

            RsvpEmailLog::query()->create([
                'site_id' => $party->site_id,
                'party_id' => $party->id,
                'sent_by_user_id' => auth()->id(),
                'sent_to_email' => $party->email,
                'type' => RsvpEmailLog::TYPE_REMINDER,
                'sent_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'RSVP reminder emails sent.',
            'sent' => $parties->count(),
        ]);
    }

    public function previewRsvpEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'guest_type' => ['nullable', Rule::in(Party::guestTypes())],
            'response_deadline' => ['nullable', 'date'],
            'evening_arrival_time' => ['nullable', 'date_format:H:i'],
        ]);

        return response()->json([
            'html' => view('emails.party-rsvp-invite', $this->previewRsvpEmailData($validated))->render(),
        ]);
    }

    public function sendTestRsvpEmail(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['nullable', 'email', 'max:255'],
            'guest_type' => ['nullable', Rule::in(Party::guestTypes())],
            'response_deadline' => ['nullable', 'date'],
            'evening_arrival_time' => ['nullable', 'date_format:H:i'],
        ]);

        $recipient = trim((string) ($validated['email'] ?? ''));
        if ($recipient === '') {
            $recipient = (string) $this->defaultTestEmailAddress();
        }

        if ($recipient === '') {
            return response()->json([
                'message' => 'Enter a test email address before sending a preview.',
            ], 422);
        }

        $emailData = $this->previewRsvpEmailData($validated);

        Mail::to($recipient)->send(new PartyRsvpInviteMail(
            siteTitle: $emailData['siteTitle'],
            partyName: $emailData['partyName'],
            guestTypeLabel: $emailData['guestTypeLabel'],
            rsvpCode: $emailData['rsvpCode'],
            websiteUrl: $emailData['websiteUrl'],
            rsvpUrl: $emailData['rsvpUrl'],
            responseDeadline: $emailData['responseDeadline'],
            eveningArrivalTime: $emailData['eveningArrivalTime'],
        ));

        return response()->json([
            'message' => "Test RSVP email sent to {$recipient}.",
        ]);
    }

    public function emailHistory(Party $party): JsonResponse
    {
        $logs = $party->rsvpEmailLogs()
            ->orderByDesc('sent_at')
            ->limit(100)
            ->get()
            ->map(fn (RsvpEmailLog $log) => [
                'id' => $log->id,
                'sent_to_email' => $log->sent_to_email,
                'type' => $log->type ?: RsvpEmailLog::TYPE_INVITE,
                'type_label' => $log->type === RsvpEmailLog::TYPE_REMINDER ? 'Reminder' : 'Invite',
                'sent_at' => $log->sent_at?->toDateTimeString(),
            ])
            ->values();

        return response()->json([
            'party_id' => $party->id,
            'party_name' => $party->display_name,
            'history' => $logs,
        ]);
    }

    private function partyPayload(Party $party): array
    {
        return [
            'id' => $party->id,
            'display_name' => $party->display_name,
            'guest_type' => $party->guest_type ?: Party::GUEST_TYPE_DAY,
            'guest_type_label' => $party->guestTypeLabel(),
            'email' => $party->email,
            'code' => $party->code,
            'max_guests' => $party->max_guests,
            'notes' => $party->notes,
            'guests' => $party->guests->map(fn ($guest) => [
                'id' => $guest->id,
                'first_name' => $guest->first_name,
                'last_name' => $guest->last_name,
                'is_child' => $guest->is_child,
                'allow_plus_one' => $guest->allow_plus_one,
            ])->values(),
            'rsvp' => $party->rsvp ? [
                'status' => $party->rsvp->status,
                'attending_count' => $party->rsvp->attending_count,
                'meal_choices' => $party->rsvp->meal_choices ?? [],
                'dietary_restrictions' => $party->rsvp->dietary_restrictions,
                'message' => $party->rsvp->message,
                'updated_at' => $party->rsvp->updated_at?->toDateTimeString(),
            ] : null,
            'rsvp_email_sent' => ((int) ($party->rsvp_email_logs_count ?? 0)) > 0,
            'rsvp_email_sent_at' => $party->rsvp_email_logs_max_sent_at,
            'rsvp_email_sent_count' => (int) ($party->rsvp_email_logs_count ?? 0),
        ];
    }

    private function syncPartySeatLimit(?Party $party): void
    {
        if (! $party) {
            return;
        }

        $minimumSeats = $this->minimumSeatsForParty($party);
        if ($party->max_guests < $minimumSeats) {
            $party->update([
                'max_guests' => $minimumSeats,
            ]);
        }
    }

    private function minimumSeatsForParty(Party $party): int
    {
        $party->loadMissing('guests');
        $guestCount = $party->guests->count();
        $plusOneCount = $party->guests->where('allow_plus_one', true)->count();

        return max(1, $guestCount + $plusOneCount);
    }

    private function previewRsvpEmailData(array $overrides = []): array
    {
        $site = $this->currentSite();
        $content = $this->resolvedHomepageContent();
        $guestType = (string) ($overrides['guest_type'] ?? Party::GUEST_TYPE_DAY);
        $responseDeadline = array_key_exists('response_deadline', $overrides)
            ? (string) ($overrides['response_deadline'] ?? '')
            : (string) data_get($content, 'guest_list.responseDeadline', '');
        $eveningArrivalTime = array_key_exists('evening_arrival_time', $overrides)
            ? (string) ($overrides['evening_arrival_time'] ?? '')
            : data_get($content, 'guest_list.evening_arrival_time');
        $websiteUrl = route('wedding.public', ['public_slug' => $site->public_slug]);

        return [
            'siteTitle' => $site->title ?: 'Your wedding website',
            'partyName' => 'Example Guest Party',
            'guestTypeLabel' => $guestType === Party::GUEST_TYPE_EVENING ? 'Evening Guest' : 'All Day Guest',
            'rsvpCode' => 'DEMO',
            'websiteUrl' => $websiteUrl,
            'rsvpUrl' => $websiteUrl.'?code=DEMO',
            'responseDeadline' => $this->formatResponseDeadline($responseDeadline),
            'eveningArrivalTime' => InvitationTiming::eveningArrivalTimeForGuestType($guestType, $eveningArrivalTime),
        ];
    }

    private function defaultTestEmailAddress(): ?string
    {
        if (auth()->user()?->email) {
            return auth()->user()->email;
        }

        $site = $this->currentSite()->loadMissing('account.users');
        $owner = $site->account?->users?->firstWhere('role', 'owner')
            ?? $site->account?->users?->first();

        return $owner?->email;
    }

    private function blockedRsvpEmailResponse($site): ?JsonResponse
    {
        if (! $site->is_published) {
            return response()->json([
                'message' => 'Publish your website before sending RSVP emails.',
            ], 422);
        }

        if (! $site->account?->hasActivePaidAccess()) {
            return response()->json([
                'message' => 'An active subscription is required before sending RSVP emails.',
            ], 422);
        }

        return null;
    }

    private function resolvedHomepageContent(): array
    {
        $fallback = config('wedding.homepage_content', []);
        $saved = SiteSetting::query()
            ->forSite($this->currentSiteId())
            ->where('key', 'homepage_content')
            ->first();

        return is_array($saved?->value) ? array_replace_recursive($fallback, $saved->value) : $fallback;
    }

    private function formatResponseDeadline(string $value): ?string
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        try {
            return Carbon::parse($trimmed)->translatedFormat('j F Y');
        } catch (\Throwable) {
            return null;
        }
    }
}
