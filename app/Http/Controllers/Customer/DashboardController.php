<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\Party;
use App\Models\Rsvp;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $site = $this->currentSite();
        $account = $site->account;
        $activeTab = $request->query('tab') === 'account' ? 'account' : 'overview';

        return view('customer.dashboard', [
            'accountName' => $account->name,
            'siteTitle' => $site->title,
            'siteSlug' => $site->public_slug,
            'sitePublished' => $site->is_published,
            'activeTab' => $activeTab,
            'currentUser' => $request->user(),
            'billing' => [
                'status' => $account->status,
                'status_label' => $this->statusLabel($account->status),
                'has_paid_access' => $account->hasActivePaidAccess(),
                'cancel_at_period_end' => (bool) $account->subscription_cancel_at_period_end,
                'period_end' => $account->subscription_current_period_end?->toDateString(),
                'checkout_url' => route('billing.checkout.start'),
                'portal_url' => route('billing.portal'),
                'cancel_url' => route('billing.cancel'),
                'publish_url' => route('customer.site.publish'),
            ],
            'stats' => [
                'households' => Party::query()->forSite($site->id)->count(),
                'guests' => Guest::query()->forSite($site->id)->count(),
                'attending' => (int) Rsvp::query()->forSite($site->id)->where('status', Rsvp::STATUS_ATTENDING)->sum('attending_count'),
            ],
        ]);
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'active' => 'Active',
            'gifted' => 'Gifted',
            'past_due' => 'Past Due',
            'cancelled' => 'Cancelled (Period End)',
            'suspended' => 'Expired / Suspended',
            default => 'Draft',
        };
    }
}
