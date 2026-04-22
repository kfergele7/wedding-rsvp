<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HostRsvpNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $siteTitle,
        public string $partyName,
        public string $guestTypeLabel,
        public string $statusLabel,
        public int $attendingCount,
        public int $maxGuests,
        public ?string $dietaryRestrictions,
        public ?string $messageFromGuest,
        public array $mealChoices,
        public string $responsesUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New RSVP received from '.$this->partyName
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.host-rsvp-notification',
        );
    }
}
