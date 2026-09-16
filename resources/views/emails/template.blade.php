<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $emailSubject }}</title>
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
                            <h1 style="margin:0 0 20px 0; font-size:20px; color:#123524; font-weight:bold;">
                                {{ $emailSubject }}
                            </h1>
                            <div style="font-size:15px; line-height:1.7; color:#333333;">
                                {!! nl2br(e($bodyHtml)) !!}
                            </div>

                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin-top:32px;">
                                <tr>
                                    <td style="background-color:#123524; border-radius:10px;">
                                        <a href="{{ config('app.url') }}" style="display:inline-block; padding:12px 28px; color:#ffffff; font-size:14px; font-weight:bold; text-decoration:none;">
                                            Visit Alumni Portal
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding:24px 40px; background-color:#F7F5EF; border-top:1px solid #E5E2D8;">
                            <p style="margin:0; font-size:12px; color:#8a897f; line-height:1.6;">
                                Osmeña Ave., Victorias City, Negros Occidental<br>
                                You're receiving this because you're part of the CSAV Alumni network.
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
