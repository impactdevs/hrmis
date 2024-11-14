<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Your Application Has Been Received</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f7fc;
            color: #333;
        }
        .email-container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: #fff;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            margin-top: 20px;
        }
        .content p {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
        }
        .content .highlight {
            color: #007bff;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-top: 40px;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }
        .btn {
            background-color: #007bff;
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <h1>Your Application Has Been Received</h1>
        </div>

        <!-- Main Content Section -->
        <div class="content">
            <p>Dear {{ $name }},</p>

            <p>We have successfully received your application for the role of <span class="highlight">{{ $application->job->title }}</span>.</p>

            <p>Thank you for your interest in joining our team at <span class="highlight">UNCST</span>. Our recruitment team will review your application, and we will get back to you as soon as possible with feedback.</p>

            <p>We appreciate the time and effort you’ve put into your application, and we look forward to connecting with you soon.</p>

            <p>Thank you once again for applying!</p>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>Best regards, <br> The UNCST Team</p>
        </div>
    </div>
</body>
</html>
