<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportPartiesRequest;
use App\Http\Requests\StoreGuestRequest;
use App\Http\Requests\StorePartyRequest;
use App\Http\Requests\UpdateGuestRequest;
use App\Http\Requests\UpdatePartyRequest;
use App\Models\Guest;
use App\Models\Party;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartyController extends Controller
{
    public function generateCode(): JsonResponse
    {
        return response()->json([
            'code' => Party::generateCode(4),
        ]);
    }

    public function index(): JsonResponse
    {
        $parties = Party::query()
            ->with(['guests', 'rsvp'])
            ->orderBy('display_name')
            ->get();

        return response()->json([
            'parties' => $parties->map(fn (Party $party) => $this->partyPayload($party))->values(),
        ]);
    }

    public function store(StorePartyRequest $request): JsonResponse
    {
        $data = $request->validated();
        $code = isset($data['code']) && $data['code'] !== ''
            ? strtoupper(trim($data['code']))
            : Party::generateCode(4);

        $party = Party::query()->create([
            'display_name' => $data['display_name'],
            'code' => $code,
            'max_guests' => $data['max_guests'],
            'notes' => $data['notes'] ?? null,
        ]);

        $party->load(['guests', 'rsvp']);

        return response()->json([
            'message' => 'Party created.',
            'party' => $this->partyPayload($party),
        ], 201);
    }

    public function show(Party $party): JsonResponse
    {
        $party->load(['guests', 'rsvp']);

        return response()->json(['party' => $this->partyPayload($party)]);
    }

    public function update(UpdatePartyRequest $request, Party $party): JsonResponse
    {
        $data = $request->validated();

        $party->update([
            'display_name' => $data['display_name'],
            'code' => strtoupper(trim($data['code'])),
            'max_guests' => $data['max_guests'],
            'notes' => $data['notes'] ?? null,
        ]);

        $party->load(['guests', 'rsvp']);

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
        $guest = $party->guests()->create($request->validated());

        return response()->json([
            'message' => 'Guest added.',
            'guest' => $guest,
        ], 201);
    }

    public function updateGuest(UpdateGuestRequest $request, Guest $guest): JsonResponse
    {
        $guest->update($request->validated());

        return response()->json([
            'message' => 'Guest updated.',
            'guest' => $guest,
        ]);
    }

    public function destroyGuest(Guest $guest): JsonResponse
    {
        $guest->delete();

        return response()->json(['message' => 'Guest removed.']);
    }

    public function import(ImportPartiesRequest $request): JsonResponse
    {
        $rows = array_map('str_getcsv', file($request->file('csv_file')->getRealPath()));

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

        DB::transaction(function () use ($rows, $headers, &$createdParties, &$createdGuests) {
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
                    : Party::generateCode(4);

                $party = Party::query()->firstOrCreate(
                    ['code' => $code],
                    [
                        'display_name' => $payload['party_display_name'],
                        'max_guests' => max(1, (int) ($payload['max_guests'] ?: 1)),
                        'notes' => $payload['notes'] ?: null,
                    ]
                );

                if ($party->wasRecentlyCreated) {
                    $createdParties++;
                }

                if (($payload['first_name'] ?? '') !== '' && ($payload['last_name'] ?? '') !== '') {
                    $party->guests()->create([
                        'first_name' => $payload['first_name'],
                        'last_name' => $payload['last_name'],
                        'is_child' => in_array(strtolower((string) ($payload['is_child'] ?? '')), ['1', 'true', 'yes', 'y'], true),
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
            fputcsv($handle, ['party_display_name', 'code', 'max_guests', 'notes', 'first_name', 'last_name', 'is_child']);

            Party::query()->with('guests')->orderBy('display_name')->chunk(200, function ($parties) use ($handle) {
                foreach ($parties as $party) {
                    if ($party->guests->isEmpty()) {
                        fputcsv($handle, [$party->display_name, $party->code, $party->max_guests, $party->notes, '', '', '']);
                        continue;
                    }

                    foreach ($party->guests as $guest) {
                        fputcsv($handle, [
                            $party->display_name,
                            $party->code,
                            $party->max_guests,
                            $party->notes,
                            $guest->first_name,
                            $guest->last_name,
                            $guest->is_child ? 'yes' : 'no',
                        ]);
                    }
                }
            });

            fclose($handle);
        }, 200, $headers);
    }

    private function partyPayload(Party $party): array
    {
        return [
            'id' => $party->id,
            'display_name' => $party->display_name,
            'code' => $party->code,
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
}
