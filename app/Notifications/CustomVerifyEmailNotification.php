<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\URL;

class CustomVerifyEmailNotification extends VerifyEmail
{
    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );

        return (new MailMessage)
            ->subject('Verify your Wedding RSVP SaaS account')
            ->greeting('Welcome to Wedding RSVP SaaS')
            ->line('Your account and wedding site are ready. Verify your email to continue setup.')
            ->action('Verify Email', $verificationUrl)
            ->line('After verification you can subscribe, publish, and start collecting RSVPs.');
    }
}
