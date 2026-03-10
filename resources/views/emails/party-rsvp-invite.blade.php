<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wedding RSVP Invitation</title>
</head>
<body style="font-family: Inter, Arial, sans-serif; background: #F7F5F2; color: #1E1E1E; margin: 0; padding: 24px;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 620px; margin: 0 auto; background: #FFFFFF; border: 1px solid rgba(0,0,0,0.12);">
        <tr>
            <td style="padding: 28px;">
                <p style="margin: 0 0 10px; letter-spacing: 0.12em; font-size: 11px; text-transform: uppercase; color: #6B6B6B;">Wedding RSVP</p>
                <h1 style="margin: 0 0 14px; font-size: 28px; line-height: 1.2;">You're Invited</h1>
                <p style="margin: 0 0 14px; font-size: 15px; line-height: 1.6;">
                    Hello {{ $partyName }},
                </p>
                <p style="margin: 0 0 14px; font-size: 15px; line-height: 1.6;">
                    Please submit your RSVP using your party code:
                    <strong style="letter-spacing: 0.08em;">{{ strtoupper($rsvpCode) }}</strong>
                </p>
                <p style="margin: 0 0 18px; font-size: 15px; line-height: 1.6;">
                    Website: <a href="{{ $websiteUrl }}" style="color: #22363A;">{{ $websiteUrl }}</a><br>
                    RSVP link: <a href="{{ $rsvpUrl }}" style="color: #22363A;">{{ $rsvpUrl }}</a>
                </p>
                <p style="margin: 0; font-size: 14px; color: #6B6B6B;">
                    Thank you and we look forward to celebrating with you.
                </p>
            </td>
        </tr>
    </table>
</body>
</html>

