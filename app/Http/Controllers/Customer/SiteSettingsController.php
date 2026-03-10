<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    public function update(Request $request): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $site = $this->currentSite();
        $site->update([
            'title' => $validated['title'],
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Site title updated.',
                'title' => $site->title,
            ]);
        }

        return redirect()
            ->route('customer.dashboard')
            ->with('status', 'Site title updated.');
    }

}
