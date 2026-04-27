<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body>
    <p>Dear {{ $user->name }},</p>

    <p>{!! nl2br(e($messageBody)) !!}</p>

    <p>Best regards,<br>
    Alumni Management Team</p>
</body>
</html>
