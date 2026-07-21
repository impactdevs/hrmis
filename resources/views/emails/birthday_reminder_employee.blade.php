<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Happy Birthday!</title>
    <style>
        .header { background: #1a56db; padding: 20px; text-align: center; }
        .header img { max-height: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('assets/img/logo.png') }}" alt="UNCST">
    </div>
    <h1>Happy Birthday, {{ $employee->name }}!</h1>
    <p>Dear {{ $employee->name }},</p>
    <p>Wishing you a very Happy Birthday! We hope you have a fantastic day filled with joy and celebration!</p>
    <p>Best wishes,</p>
    <p>UNCST team</p>
</body>
</html>
