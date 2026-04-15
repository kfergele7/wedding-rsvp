<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class NewsletterSignupController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $apiKey = (string) config('services.mailchimp.key');
        $audienceId = (string) config('services.mailchimp.audience_id');
        $server = (string) config('services.mailchimp.server');
        $server = $server !== '' ? $server : Str::afterLast($apiKey, '-');

        if ($apiKey === '' || $audienceId === '' || $server === '' || $server === $apiKey) {
            report(new \RuntimeException('Mailchimp newsletter signup is not configured.'));

            return back()
                ->withInput()
                ->withErrors(['email' => 'Newsletter signup is not available just yet. Please try again soon.']);
        }

        $email = Str::lower($validated['email']);
        $subscriberHash = md5($email);

        try {
            Http::withBasicAuth('magic-invitation', $apiKey)
                ->acceptJson()
                ->asJson()
                ->timeout(8)
                ->put("https://{$server}.api.mailchimp.com/3.0/lists/{$audienceId}/members/{$subscriberHash}", [
                    'email_address' => $email,
                    'status_if_new' => 'pending',
                    'merge_fields' => new \stdClass(),
                ])
                ->throw();
        } catch (RequestException $exception) {
            report($exception);

            return back()
                ->withInput()
                ->withErrors(['email' => 'We could not add you to the mailing list right now. Please try again shortly.']);
        }

        return back()->with('newsletter_status', 'Thank you. Please check your inbox to confirm your email address.');
    }
}
