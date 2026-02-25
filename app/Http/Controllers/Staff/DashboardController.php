<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Site;
use App\Models\StripeWebhookEvent;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('staff.dashboard', [
            'stats' => [
                'total_accounts' => Account::query()->count(),
                'active_subscriptions' => Account::query()->whereIn('status', [Account::STATUS_ACTIVE, Account::STATUS_GIFTED])->count(),
                'draft_or_unpaid' => Account::query()->whereIn('status', [Account::STATUS_DRAFT, Account::STATUS_PAST_DUE])->count(),
                'cancelled_or_expired' => Account::query()->whereIn('status', [Account::STATUS_CANCELLED, Account::STATUS_SUSPENDED])->count(),
                'total_sites' => Site::query()->count(),
                'recent_webhooks' => StripeWebhookEvent::query()->whereNotNull('processed_at')->count(),
            ],
        ]);
    }
}
