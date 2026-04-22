<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $guestTypeLabel }} RSVP Invitation</title>
</head>
<body style="margin:0; padding:0; background-color:#F7F5F2; font-family: Inter, Arial, sans-serif; color:#0F1B1D;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F7F5F2; margin:0; padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:680px; margin:0 auto;">
                <tr>
                    <td align="center" style="padding-bottom:18px;">
                        <img
                            src="{{ asset('images/brand/logo-dark.svg') }}"
                            alt="Magic Invitation"
                            style="display:block; width:210px; max-width:100%; height:auto;"
                        >
                    </td>
                </tr>
                <tr>
                    <td style="border:1px solid rgba(34,54,58,0.12); background-color:#FFFFFF; box-shadow:0 20px 50px rgba(15,27,29,0.08);">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td style="background-color:#22363A; padding:16px 24px; text-align:center;">
                                    <p style="margin:0; color:#FFFFFF; font-size:12px; letter-spacing:0.18em; text-transform:uppercase;">
                                        {{ $siteTitle }}
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:36px 32px 18px;">
                                    <p style="margin:0 0 12px; color:#848484; font-size:12px; letter-spacing:0.18em; text-transform:uppercase;">
                                        RSVP Invitation
                                    </p>
                                    <h1 style="margin:0; font-family: Georgia, 'Times New Roman', serif; font-size:34px; line-height:1.15; color:#0F1B1D;">
                                        You're invited as a {{ $guestTypeLabel }}
                                    </h1>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:0 32px 8px;">
                                    <p style="margin:0 0 16px; font-size:16px; line-height:1.7; color:#0F1B1D;">
                                        Hello {{ $partyName }},
                                    </p>
                                    <p style="margin:0 0 16px; font-size:16px; line-height:1.7; color:#0F1B1D;">
                                        We would love for you to join us. Your invitation is marked as a
                                        <strong>{{ $guestTypeLabel }}</strong>, and you can submit your RSVP using the details below.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:12px 32px 0;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid rgba(34,54,58,0.12); background-color:#F2ECE3;">
                                        <tr>
                                            <td style="padding:22px 24px;">
                                                <p style="margin:0 0 10px; font-size:12px; letter-spacing:0.18em; text-transform:uppercase; color:#466369;">
                                                    Your RSVP Code
                                                </p>
                                                <p style="margin:0; font-family: Georgia, 'Times New Roman', serif; font-size:30px; letter-spacing:0.12em; color:#22363A;">
                                                    {{ strtoupper($rsvpCode) }}
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            @if ($responseDeadline)
                                <tr>
                                    <td style="padding:18px 32px 0;">
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid rgba(33,193,119,0.24); background-color:#22363A;">
                                            <tr>
                                                <td style="padding:22px 24px; text-align:center;">
                                                    <p style="margin:0 0 8px; color:#FFFFFF; font-size:12px; letter-spacing:0.18em; text-transform:uppercase;">
                                                        RSVP Deadline
                                                    </p>
                                                    <p style="margin:0; color:#FFFFFF; font-family: Georgia, 'Times New Roman', serif; font-size:32px; line-height:1.2;">
                                                        {{ $responseDeadline }}
                                                    </p>
                                                    <p style="margin:10px 0 0; color:#F7F7F7; font-size:14px; line-height:1.6;">
                                                        Please reply by this date so plans can be finalised.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <td style="padding:24px 32px 0;">
                                    <p style="margin:0 0 18px; font-size:15px; line-height:1.7; color:#0F1B1D;">
                                        The best place to begin is the wedding website, where you can explore all the details and then use your RSVP code when you are ready to respond.
                                    </p>
                                </td>
                            </tr>
                            <tr>
                                <td align="center" style="padding:8px 32px 0;">
                                    <a
                                        href="{{ $websiteUrl }}"
                                        style="display:inline-block; background-color:#22363A; color:#FFFFFF; text-decoration:none; padding:14px 28px; font-size:13px; letter-spacing:0.16em; text-transform:uppercase;"
                                    >
                                        Visit Wedding Website
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:28px 32px 0;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid rgba(34,54,58,0.12);">
                                        <tr>
                                            <td style="padding-top:20px;">
                                                <p style="margin:0 0 8px; font-size:12px; letter-spacing:0.14em; text-transform:uppercase; color:#848484;">
                                                    Wedding Website
                                                </p>
                                                <p style="margin:0 0 14px; font-size:15px; line-height:1.7;">
                                                    <a href="{{ $websiteUrl }}" style="color:#22363A; text-decoration:underline;">{{ $websiteUrl }}</a>
                                                </p>
                                                <p style="margin:0; font-size:15px; line-height:1.7; color:#0F1B1D;">
                                                    If you have any trouble responding, you can use your RSVP code
                                                    <strong>{{ strtoupper($rsvpCode) }}</strong>
                                                    on the website.
                                                </p>
                                                <p style="margin:16px 0 0; font-size:15px; line-height:1.7; color:#0F1B1D;">
                                                    If you would prefer to go straight to the RSVP form, you can use this direct link:
                                                    <a href="{{ $rsvpUrl }}" style="color:#22363A; text-decoration:underline;">RSVP now</a>.
                                                </p>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:28px 32px 34px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid rgba(34,54,58,0.12);">
                                        <tr>
                                            <td align="center" style="padding-top:22px;">
                                                <a href="https://magicinvitation.com" style="text-decoration:none; color:#848484;">
                                                <table role="presentation" cellpadding="0" cellspacing="0" style="margin:0 auto;">
                                                    <tr>
                                                        <td style="vertical-align:middle; padding-right:10px;">
                                                            <img
                                                                src="{{ asset('images/brand/icon-dark.svg') }}"
                                                                alt="Magic Invitation"
                                                                style="display:block; width:22px; height:22px;"
                                                            >
                                                        </td>
                                                        <td style="vertical-align:middle;">
                                                            <p style="margin:0; font-size:12px; letter-spacing:0.14em; text-transform:uppercase; color:#848484;">
                                                                Powered by Magic Invitation
                                                            </p>
                                                        </td>
                                                    </tr>
                                                </table>
                                                </a>
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
