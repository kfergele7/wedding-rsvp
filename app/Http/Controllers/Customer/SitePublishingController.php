<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SitePublishingController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'is_published' => ['required', 'boolean'],
        ]);

        $site = $this->currentSite();
        $account = $site->account;
        $publish = (bool) $validated['is_published'];

        if ($publish && ! $account->hasActivePaidAccess()) {
            return back()->withErrors(['publish' => 'An active subscription is required before publishing your site.']);
        }

        $site->update(['is_published' => $publish]);

        return back()->with('status', $publish ? 'Site published.' : 'Site moved to draft.');
    }
}
