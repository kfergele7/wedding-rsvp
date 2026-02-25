<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Services\Billing\StripeBillingClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function __construct(private readonly StripeBillingClient $stripe)
    {
    }

    public function startCheckout(Request $request): RedirectResponse
    {
        $user = $request->user();
        $account = $user->account;

        $priceId = (string) config('services.stripe.price_monthly');
        if ($priceId === '') {
            return back()->withErrors(['billing' => 'Stripe monthly price is not configured.']);
        }

        if (! $account->stripe_customer_id) {
            $customer = $this->stripe->createCustomer($account->name, $user->email, $account->id);
            $account->update(['stripe_customer_id' => $customer['id'] ?? null]);
            $account->refresh();
        }

        $session = $this->stripe->createSubscriptionCheckoutSession(
            customerId: (string) $account->stripe_customer_id,
            priceId: $priceId,
            successUrl: route('billing.checkout.success'),
            cancelUrl: route('billing.checkout.cancel'),
            accountId: $account->id,
        );

        return redirect()->away((string) ($session['url'] ?? route('customer.dashboard')));
    }

    public function checkoutSuccess(): RedirectResponse
    {
        return redirect()->route('customer.dashboard')->with('status', 'Checkout complete. Subscription status will update shortly.');
    }

    public function checkoutCancel(): RedirectResponse
    {
        return redirect()->route('customer.dashboard')->with('status', 'Checkout cancelled.');
    }

    public function portal(Request $request): RedirectResponse
    {
        $account = $request->user()->account;

        if ($account->status === Account::STATUS_GIFTED) {
            return back()->withErrors(['billing' => 'This account is on a gifted subscription managed by support.']);
        }

        if (! $account->stripe_customer_id) {
            return back()->withErrors(['billing' => 'No Stripe customer exists for this account yet.']);
        }

        $portal = $this->stripe->createBillingPortalSession(
            customerId: $account->stripe_customer_id,
            returnUrl: route('customer.dashboard'),
        );

        return redirect()->away((string) ($portal['url'] ?? route('customer.dashboard')));
    }

    public function cancel(Request $request): RedirectResponse
    {
        $account = $request->user()->account;

        if ($account->status === Account::STATUS_GIFTED) {
            return back()->withErrors(['billing' => 'This account is on a gifted subscription managed by support.']);
        }

        if (! $account->stripe_subscription_id) {
            return back()->withErrors(['billing' => 'No active Stripe subscription found to cancel.']);
        }

        $subscription = $this->stripe->cancelAtPeriodEnd($account->stripe_subscription_id);
        $account->syncStripeSubscription($subscription);

        return redirect()->route('customer.dashboard')->with('status', 'Subscription set to cancel at period end.');
    }
}
