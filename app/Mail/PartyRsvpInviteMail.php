<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PartyRsvpInviteMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $partyName,
        public string $rsvpCode,
        public string $websiteUrl,
        public string $rsvpUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Wedding RSVP Invitation'
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.party-rsvp-invite',
        );
    }
}

