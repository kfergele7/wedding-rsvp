<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeCustomerNotification extends Notification
{
    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to Wedding RSVP SaaS')
            ->greeting('You are all set')
            ->line('Your email is verified and your wedding account is ready.')
            ->line('Next step: choose your plan, publish your site, and start collecting RSVPs.')
            ->action('Open Dashboard', route('customer.dashboard'));
    }
}
