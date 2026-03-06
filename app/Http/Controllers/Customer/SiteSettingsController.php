<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteSettingsController extends Controller
{
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
        ]);

        $site = $this->currentSite();
        $site->update([
            'title' => $validated['title'],
        ]);

        return redirect()
            ->route('customer.dashboard')
            ->with('status', 'Site title updated.');
    }

}
