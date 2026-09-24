<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>New Contact Message</title>
</head>
<body style="margin:0; padding:0; background-color:#F7F5EF; font-family: Arial, Helvetica, sans-serif;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F7F5EF; padding:40px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">

                    <!-- Header -->
                    <tr>
                        <td style="background-color:#123524; padding:32px 40px;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width:48px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" style="width:48px; height:48px; background-color:#ffffff; border-radius:50%; border:2px solid #D4A537;">
                                            <tr>
                                                <td align="center" valign="middle">
                                                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/55/LogoCSAV.png" width="32" height="32" alt="CSAV" style="display:block;">
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="padding-left:14px; color:#ffffff; font-size:15px; font-weight:bold; vertical-align:middle;">
                                        Colegio de Sta. Ana de Victorias
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Gold accent bar -->
                    <tr>
                        <td style="background-color:#D4A537; height:4px; line-height:4px; font-size:0;">&nbsp;</td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:40px;">

                            <p style="margin:0 0 8px 0; font-size:11px; letter-spacing:1.5px; text-transform:uppercase; color:#a97f1f; font-weight:bold;">
                                New Message
                            </p>
                            <h1 style="margin:0 0 24px 0; font-size:20px; color:#123524; font-weight:bold; line-height:1.3;">
                                Someone reached out through the<br>Alumni Network contact form
                            </h1>

                            <!-- Sender card -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F7F5EF; border-radius:12px; border:1px solid #E5E2D8; margin-bottom:28px;">
                                <tr>
                                    <td style="padding:20px 22px;">

                                        <!-- Avatar + name row -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="width:44px; vertical-align:top;">
                                                    <table role="presentation" cellpadding="0" cellspacing="0" style="width:44px; height:44px; background-color:#123524; border-radius:50%;">
                                                        <tr>
                                                            <td align="center" valign="middle" style="color:#D4A537; font-size:18px; font-weight:bold; line-height:44px;">
                                                                {{ strtoupper(substr($senderName, 0, 1)) }}
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                                <td style="padding-left:14px; vertical-align:top;">
                                                    <p style="margin:0; font-size:15px; font-weight:bold; color:#123524; line-height:1.4;">
                                                        {{ $senderName }}
                                                    </p>
                                                    <p style="margin:2px 0 0 0; font-size:13px; color:#666666; line-height:1.4;">
                                                        <a href="mailto:{{ $senderEmail }}" style="color:#a97f1f; text-decoration:none;">
                                                            {{ $senderEmail }}
                                                        </a>
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- Divider -->
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:18px 0;">
                                            <tr>
                                                <td style="border-top:1px solid #E5E2D8; font-size:0; line-height:0;">&nbsp;</td>
                                            </tr>
                                        </table>

                                        <!-- Message label -->
                                        <p style="margin:0 0 8px 0; font-size:11px; letter-spacing:1px; text-transform:uppercase; color:#8a897f; font-weight:bold;">
                                            Message
                                        </p>
                                        <div style="font-size:15px; line-height:1.7; color:#333333; white-space:pre-wrap;">{{ $messageBody }}</div>

                                    </td>
                                </tr>
                            </table>

                            <!-- CTA -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:8px;">
                                <tr>
                                    <td style="background-color:#123524; border-radius:10px;">
                                        <a href="mailto:{{ $senderEmail }}?subject=Re%3A%20Your%20message%20to%20CSAV%20Alumni%20Network"
                                           style="display:inline-block; padding:12px 28px; color:#ffffff; font-size:14px; font-weight:bold; text-decoration:none;">
                                            Reply to {{ $senderName }}
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:24px 0 0 0; font-size:12px; color:#8a897f; line-height:1.6;">
                                Or reply directly to this email — it will reach the sender.
                            </p>

                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:24px 40px; background-color:#F7F5EF; border-top:1px solid #E5E2D8;">
                            <p style="margin:0; font-size:12px; color:#8a897f; line-height:1.6;">
                                Osmeña Ave., Victorias City, Negros Occidental<br>
                                Submitted through the CSAV Alumni Network contact form on {{ now()->format('F j, Y \a\t g:i A') }}.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>