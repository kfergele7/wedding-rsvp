<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SitePublishingController extends Controller
{
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'is_published' => ['required', 'boolean'],
        ]);

        $site = $this->currentSite();
        $account = $site->account;
        $publish = (bool) $validated['is_published'];

        if ($publish && ! $account->hasActivePaidAccess()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'An active subscription is required before publishing your site.',
                ], 422);
            }

            return back()->withErrors(['publish' => 'An active subscription is required before publishing your site.']);
        }

        $site->update(['is_published' => $publish]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $publish ? 'Site published.' : 'Site moved to draft.',
                'is_published' => (bool) $site->is_published,
            ]);
        }

        return back()->with('status', $publish ? 'Site published.' : 'Site moved to draft.');
    }
}
