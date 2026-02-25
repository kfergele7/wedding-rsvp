<?php

namespace App\Services\Billing;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class StripeBillingClient
{
    private function request(): PendingRequest
    {
        $secret = (string) config('services.stripe.secret');

        if ($secret === '') {
            throw new RuntimeException('Stripe secret key is not configured.');
        }

        return Http::asForm()
            ->withBasicAuth($secret, '')
            ->baseUrl('https://api.stripe.com/v1')
            ->acceptJson();
    }

    public function createCustomer(string $name, string $email, int $accountId): array
    {
        $response = $this->request()->post('/customers', [
            'name' => $name,
            'email' => $email,
            'metadata[account_id]' => (string) $accountId,
        ]);

        $response->throw();

        return $response->json();
    }

    public function createSubscriptionCheckoutSession(string $customerId, string $priceId, string $successUrl, string $cancelUrl, int $accountId): array
    {
        $response = $this->request()->post('/checkout/sessions', [
            'customer' => $customerId,
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'line_items[0][price]' => $priceId,
            'line_items[0][quantity]' => 1,
            'client_reference_id' => (string) $accountId,
            'metadata[account_id]' => (string) $accountId,
        ]);

        $response->throw();

        return $response->json();
    }

    public function createBillingPortalSession(string $customerId, string $returnUrl): array
    {
        $response = $this->request()->post('/billing_portal/sessions', [
            'customer' => $customerId,
            'return_url' => $returnUrl,
        ]);

        $response->throw();

        return $response->json();
    }

    public function cancelAtPeriodEnd(string $subscriptionId): array
    {
        $response = $this->request()->post('/subscriptions/'.$subscriptionId, [
            'cancel_at_period_end' => 'true',
        ]);

        $response->throw();

        return $response->json();
    }

    public function retrieveSubscription(string $subscriptionId): array
    {
        $response = $this->request()->get('/subscriptions/'.$subscriptionId);

        $response->throw();

        return $response->json();
    }
}
