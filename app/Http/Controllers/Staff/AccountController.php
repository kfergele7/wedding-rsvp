<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Guest;
use App\Models\Party;
use App\Models\Rsvp;
use App\Models\StaffAuditLog;
use App\Models\StripeWebhookEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $statusFilter = trim((string) $request->query('status', ''));

        $accounts = Account::query()
            ->with(['users', 'sites'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($inner) use ($search) {
                    $inner->where('name', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('users', fn ($userQuery) => $userQuery->where('email', 'like', "%{$search}%"));
                });
            })
            ->when($statusFilter !== '', fn ($query) => $query->where('status', $statusFilter))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('staff.accounts.index', [
            'accounts' => $accounts,
            'filters' => [
                'q' => $search,
                'status' => $statusFilter,
            ],
        ]);
    }

    public function show(Account $account): View
    {
        $account->load(['users', 'sites']);

        $siteIds = $account->sites->pluck('id');
        $owner = $account->users->firstWhere('role', 'owner') ?? $account->users->first();

        return view('staff.accounts.show', [
            'account' => $account,
            'owner' => $owner,
            'metrics' => [
                'households' => Party::query()->whereIn('site_id', $siteIds)->count(),
                'guests' => Guest::query()->whereIn('site_id', $siteIds)->count(),
                'rsvp_responses' => Rsvp::query()->whereIn('site_id', $siteIds)->count(),
                'last_rsvp_at' => Rsvp::query()->whereIn('site_id', $siteIds)->max('updated_at'),
            ],
            'webhookEvents' => StripeWebhookEvent::query()
                ->where('account_id', $account->id)
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function update(Request $request, Account $account): RedirectResponse
    {
        $validated = $request->validate([
            'access_status' => ['required', 'in:active,suspended'],
            'status' => ['required', 'in:draft,active,gifted,past_due,cancelled,suspended'],
            'internal_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $previous = [
            'access_status' => $account->access_status,
            'status' => $account->status,
            'internal_notes' => $account->internal_notes,
        ];

        $account->update([
            'access_status' => $validated['access_status'],
            'status' => $validated['status'],
            'internal_notes' => $validated['internal_notes'] ?? null,
        ]);

        StaffAuditLog::query()->create([
            'staff_user_id' => $request->user()->id,
            'account_id' => $account->id,
            'action' => 'staff.account.updated',
            'payload' => [
                'before' => $previous,
                'after' => [
                    'access_status' => $account->access_status,
                    'internal_notes' => $account->internal_notes,
                ],
            ],
        ]);

        return back()->with('status', 'Account updated.');
    }
}
