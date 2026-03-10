<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AccountSettingsController extends Controller
{
    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $emailChanged = strtolower($validated['email']) !== strtolower((string) $user->email);

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ])->save();

        if ($emailChanged) {
            $user->sendEmailVerificationNotification();

            return redirect()
                ->route('customer.dashboard', ['tab' => 'account'])
                ->with('status', 'Profile updated. Please verify your new email address.');
        }

        return redirect()
            ->route('customer.dashboard', ['tab' => 'account'])
            ->with('status', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return redirect()
            ->route('customer.dashboard', ['tab' => 'account'])
            ->with('status', 'Password updated successfully.');
    }

    /**
     * @throws AuthorizationException
     */
    public function destroyAccount(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'current_password_confirmation' => ['required', 'same:current_password'],
            'confirm_data_deletion' => ['accepted'],
        ]);

        $user = $request->user();
        if (($user->role ?? 'owner') !== 'owner') {
            throw new AuthorizationException('Only the account owner can delete this account.');
        }

        DB::transaction(function () use ($user): void {
            $account = $user->account;

            if ($account) {
                User::query()
                    ->where('account_id', $account->id)
                    ->where('is_staff', false)
                    ->delete();

                $account->delete();

                return;
            }

            $user->delete();
        });

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('marketing.home')
            ->with('status', 'Your account and site data have been permanently deleted.');
    }
}
