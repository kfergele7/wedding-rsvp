<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsletterSignupTest extends TestCase
{
    use RefreshDatabase;

    public function test_coming_soon_newsletter_signup_sends_email_to_mailchimp(): void
    {
        Config::set('services.mailchimp.key', 'test-key-us5');
        Config::set('services.mailchimp.audience_id', 'audience123');
        Config::set('services.mailchimp.server', null);

        Http::fake([
            'https://us5.api.mailchimp.com/3.0/lists/audience123/members/*' => Http::response(['id' => 'member-id'], 200),
        ]);

        $this->post(route('newsletter.signup'), [
            'email' => 'Kyle@example.com',
        ])->assertRedirect()
            ->assertSessionHas('newsletter_status');

        Http::assertSent(function ($request): bool {
            return $request->method() === 'PUT'
                && str_contains($request->url(), md5('kyle@example.com'))
                && $request['email_address'] === 'kyle@example.com'
                && $request['status_if_new'] === 'pending';
        });
    }

    public function test_newsletter_signup_requires_mailchimp_configuration(): void
    {
        Config::set('services.mailchimp.key', null);
        Config::set('services.mailchimp.audience_id', null);
        Config::set('services.mailchimp.server', null);

        $this->post(route('newsletter.signup'), [
            'email' => 'kyle@example.com',
        ])->assertRedirect()
            ->assertSessionHasErrors('email');

        Http::assertNothingSent();
    }
}
