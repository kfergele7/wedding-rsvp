<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Guest;
use App\Models\Party;
use App\Models\Rsvp;
use App\Models\Site;
use App\Models\StaffAuditLog;
use App\Models\StripeWebhookEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function updateSite(Request $request, Account $account, Site $site): RedirectResponse
    {
        if ((int) $site->account_id !== (int) $account->id) {
            abort(404);
        }

        $validated = $request->validate([
            'public_slug' => [
                'required',
                'string',
                'min:4',
                'max:24',
                'regex:/^[a-z0-9-]+$/',
                Rule::notIn($this->reservedPublicSlugs()),
                Rule::unique('sites', 'public_slug')->ignore($site->id),
            ],
        ]);

        $previousSlug = $site->public_slug;

        $site->update([
            'public_slug' => strtolower($validated['public_slug']),
        ]);

        StaffAuditLog::query()->create([
            'staff_user_id' => $request->user()->id,
            'account_id' => $account->id,
            'action' => 'staff.site.slug.updated',
            'payload' => [
                'site_id' => $site->id,
                'before' => ['public_slug' => $previousSlug],
                'after' => ['public_slug' => $site->public_slug],
            ],
        ]);

        return back()->with('status', 'Public URL slug updated.');
    }

    private function reservedPublicSlugs(): array
    {
        return [
            'admin',
            'api',
            'app',
            'demo',
            'dev',
            'faq',
            'features',
            'forgot-password',
            'how-it-works',
            'login',
            'logout',
            'pricing',
            'register',
            'reset-password',
            'rsvp',
            'staff',
            'stripe',
            'verify-email',
            'w',
        ];
    }

    public function launchSiteAdmin(Request $request, Account $account, Site $site): RedirectResponse
    {
        if ((int) $site->account_id !== (int) $account->id) {
            abort(404);
        }

        $section = (string) $request->query('section', 'dashboard');

        $destinationRoute = match ($section) {
            'content' => 'customer.admin.content.page',
            'parties' => 'customer.admin.parties.page',
            'rsvps' => 'customer.admin.rsvps.page',
            default => 'customer.admin.dashboard',
        };

        $request->session()->put([
            'staff_site_id' => $site->id,
            'staff_account_id' => $account->id,
            'current_site_id' => $site->id,
        ]);

        return redirect()->route($destinationRoute);
    }
}
