<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartyRsvpReminderMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $siteTitle,
        public string $partyName,
        public string $guestTypeLabel,
        public string $rsvpCode,
        public string $websiteUrl,
        public string $rsvpUrl,
        public ?string $responseDeadline = null,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Please RSVP for '.$this->siteTitle
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.party-rsvp-reminder',
        );
    }
}
