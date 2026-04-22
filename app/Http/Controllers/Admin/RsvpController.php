<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePartyRsvpRequest;
use App\Models\Party;
use App\Models\Rsvp;
use Illuminate\Http\JsonResponse;

class RsvpController extends Controller
{
    public function index(): JsonResponse
    {
        $parties = Party::query()
            ->forSite($this->currentSiteId())
            ->with(['rsvp', 'guests'])
            ->withCount('rsvpEmailLogs')
            ->withMax('rsvpEmailLogs', 'sent_at')
            ->orderBy('display_name')
            ->get();

        $rows = $parties->map(function (Party $party) {
            return [
                'party_id' => $party->id,
                'party_name' => $party->display_name,
                'guest_type' => $party->guest_type ?: Party::GUEST_TYPE_DAY,
                'guest_type_label' => $party->guestTypeLabel(),
                'email' => $party->email,
                'code' => $party->code,
                'max_guests' => $party->max_guests,
                'guest_count' => $party->guests->count(),
                'rsvp_email_sent' => ((int) ($party->rsvp_email_logs_count ?? 0)) > 0,
                'rsvp_email_sent_at' => $party->rsvp_email_logs_max_sent_at,
                'rsvp_email_sent_count' => (int) ($party->rsvp_email_logs_count ?? 0),
                'rsvp' => $party->rsvp ? [
                    'status' => $party->rsvp->status,
                    'attending_count' => $party->rsvp->attending_count,
                    'meal_choices' => $party->rsvp->meal_choices ?? [],
                    'dietary_restrictions' => $party->rsvp->dietary_restrictions,
                    'message' => $party->rsvp->message,
                    'updated_at' => $party->rsvp->updated_at?->toDateTimeString(),
                ] : null,
            ];
        })->values();

        return response()->json(['rsvps' => $rows]);
    }

    public function update(UpdatePartyRsvpRequest $request, Party $party): JsonResponse
    {
        $data = $request->validated();

        $attendingCount = (int) $data['attending_count'];

        if ($attendingCount > $party->max_guests) {
            return response()->json([
                'message' => 'Attending count cannot exceed max guests for this party.',
            ], 422);
        }

        if ($data['status'] === Rsvp::STATUS_NOT_ATTENDING) {
            $attendingCount = 0;
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

        return response()->json(['message' => 'RSVP updated.']);
    }

    public function export()
    {
        $filename = 'rsvp_export.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ];

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['party_name', 'code', 'status', 'attending_count', 'dietary_restrictions', 'message', 'meal_choices']);

            Party::query()
                ->forSite($this->currentSiteId())
                ->with('rsvp')
                ->orderBy('display_name')
                ->chunk(200, function ($parties) use ($handle) {
                foreach ($parties as $party) {
                    $rsvp = $party->rsvp;

                    fputcsv($handle, [
                        $party->display_name,
                        $party->code,
                        $rsvp?->status ?? 'no_response',
                        $rsvp?->attending_count ?? 0,
                        $rsvp?->dietary_restrictions,
                        $rsvp?->message,
                        $rsvp ? json_encode($rsvp->meal_choices ?? []) : '',
                    ]);
                }
                });

            fclose($handle);
        }, 200, $headers);
    }
}
