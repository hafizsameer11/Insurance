<!DOCTYPE html>
<html>
<head>
    <title>Reminder</title>
</head>
<body>
    <p>Dear {{ $data->user->name ?? 'User' }},</p>
    <p>This is a reminder to please update or confirm your asset records.</p>
    <p>Thank you,<br>SuaTech Insurance System</p>
</body>
</html>
