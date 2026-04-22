<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New RSVP received</title>
</head>
<body style="margin:0;padding:0;background-color:#F7F5F2;font-family:Inter,Arial,sans-serif;color:#0F1B1D;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#F7F5F2;padding:28px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;">
                    <tr>
                        <td align="center" style="padding-bottom:18px;">
                            <img src="{{ asset('images/brand/logo-dark.svg') }}" alt="Magic Invitation" style="height:36px;width:auto;display:block;">
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#22363A;border-radius:26px 26px 0 0;padding:24px 28px;color:#FFFFFF;">
                            <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#F2ECE3;">New RSVP received</p>
                            <h1 style="margin:0;font-family:'Playfair Display',Georgia,serif;font-size:34px;line-height:1.1;font-weight:600;color:#FFFFFF;">
                                {{ $partyName }} has sent their RSVP
                            </h1>
                            <p style="margin:14px 0 0;font-size:15px;line-height:1.6;color:#F7F5F2;">
                                A {{ strtolower($guestTypeLabel) }} has just responded to your wedding invitation for {{ $siteTitle }}.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="background-color:#FFFFFF;border:1px solid #E5DED4;border-top:0;border-radius:0 0 26px 26px;padding:28px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:separate;border-spacing:0 12px;">
                                <tr>
                                    <td style="background-color:#F2ECE3;border-radius:18px;padding:16px 18px;">
                                        <p style="margin:0 0 6px;font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#848484;">Party</p>
                                        <p style="margin:0;font-size:18px;font-weight:600;color:#0F1B1D;">{{ $partyName }}</p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="background-color:#F2ECE3;border-radius:18px;padding:16px 18px;">
                                        <p style="margin:0 0 6px;font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#848484;">Response</p>
                                        <p style="margin:0;font-size:18px;font-weight:600;color:#0F1B1D;">{{ $statusLabel }}</p>
                                        <p style="margin:8px 0 0;font-size:14px;line-height:1.6;color:#466369;">
                                            Attending count: {{ $attendingCount }} of {{ $maxGuests }}
                                        </p>
                                        @if(count($attendingGuestNames))
                                            <p style="margin:8px 0 0;font-size:14px;line-height:1.6;color:#0F1B1D;">
                                                Attending guests: {{ implode(', ', $attendingGuestNames) }}
                                            </p>
                                        @endif
                                    </td>
                                </tr>
                                @if(!empty($dietaryRestrictions))
                                    <tr>
                                        <td style="background-color:#F7F7F7;border-radius:18px;padding:16px 18px;">
                                            <p style="margin:0 0 6px;font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#848484;">Dietary requirements</p>
                                            <p style="margin:0;font-size:15px;line-height:1.6;color:#0F1B1D;">{{ $dietaryRestrictions }}</p>
                                        </td>
                                    </tr>
                                @endif
                                @if(!empty($messageFromGuest))
                                    <tr>
                                        <td style="background-color:#F7F7F7;border-radius:18px;padding:16px 18px;">
                                            <p style="margin:0 0 6px;font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#848484;">Message from guest</p>
                                            <p style="margin:0;font-size:15px;line-height:1.6;color:#0F1B1D;">{{ $messageFromGuest }}</p>
                                        </td>
                                    </tr>
                                @endif
                                @if(count($mealChoices))
                                    <tr>
                                        <td style="background-color:#F7F7F7;border-radius:18px;padding:16px 18px;">
                                            <p style="margin:0 0 10px;font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:#848484;">Meal choices</p>
                                            @foreach($mealChoices as $choice)
                                                <p style="margin:0 0 8px;font-size:14px;line-height:1.6;color:#0F1B1D;">
                                                    <strong>{{ $choice['guest_name'] ?? 'Guest' }}:</strong>
                                                    {{ $choice['meal'] ?? '' }}
                                                </p>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                            </table>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-top:18px;">
                                <tr>
                                    <td align="center" style="padding-bottom:12px;">
                                        <a
                                            href="{{ $responsesUrl }}"
                                            style="display:inline-block;background-color:#22363A;border:1px solid #22363A;color:#FFFFFF;text-decoration:none;padding:14px 24px;border-radius:999px;font-size:12px;letter-spacing:0.16em;text-transform:uppercase;font-weight:600;"
                                        >
                                            View RSVP Responses
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td align="center">
                                        <p style="margin:0;font-size:13px;line-height:1.6;color:#848484;">
                                            You can review and manage this response from your RSVP Requests page.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding-top:18px;">
                            <a href="https://magicinvitation.com" style="display:inline-flex;align-items:center;gap:8px;text-decoration:none;color:#466369;font-size:12px;letter-spacing:0.1em;text-transform:uppercase;">
                                <img src="{{ asset('images/brand/icon-dark.svg') }}" alt="" style="height:18px;width:18px;vertical-align:middle;">
                                <span>Powered by Magic Invitation</span>
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
