<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Models\Site;
use Illuminate\Http\RedirectResponse;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('admin-login');
    }

    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $expectedPassword = (string) env('ADMIN_PASSWORD', '');
        $providedPassword = (string) $request->validated('password');

        if ($expectedPassword === '' || ! hash_equals($expectedPassword, $providedPassword)) {
            return back()->withErrors([
                'password' => 'Invalid admin password.',
            ])->withInput();
        }

        $request->session()->regenerate();
        $request->session()->put('admin_authenticated', true);

        $defaultSite = Site::query()->orderBy('id')->first();
        if ($defaultSite) {
            $request->session()->put('admin_site_id', $defaultSite->id);
            $request->session()->put('admin_account_id', $defaultSite->account_id);
        }

        return redirect()->route('admin.dashboard');
    }

    public function logout(): RedirectResponse
    {
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'Logged out.');
    }
}
