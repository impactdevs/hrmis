<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Birthday Reminder</title>
    <style>
        .header { background: #1a56db; padding: 20px; text-align: center; }
        .header img { max-height: 60px; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('assets/img/logo.png') }}" alt="UNCST">
    </div>
    <h1>Reminder: Employee Birthday Tomorrow</h1>
    <p>Dear Admin,</p>
    <p>This is a reminder that the employee <strong>{{ $employee->name }}</strong> has their birthday tomorrow!</p>
    <p>Best regards,</p>
    <p>UNCST HRMIS</p>
</body>
</html>
