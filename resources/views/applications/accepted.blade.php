<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Congratulations! Your Application Has Been Accepted</title>
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
            background-color: #28a745; /* Changed to a green color for success */
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
            background-color: #28a745; /* Green color for success */
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header Section -->
        <div class="header">
            <h1>Congratulations! Your Application Has Been Accepted</h1>
        </div>

        <!-- Main Content Section -->
        <div class="content">
            <p>Dear {{ $name }},</p>

            <p>We are excited to inform you that your application for the role of <span class="highlight">{{ $application->job->title }}</span> has been successfully accepted!</p>

            <p>After reviewing your qualifications and experience, we are pleased to offer you the position at <span class="highlight">UNCST</span>. We believe that your skills and background will be a great addition to our team.</p>

            <p>We are looking forward to having you on board and working together to achieve great things. You will receive further details regarding your start date and the next steps in the hiring process soon.</p>

            <p>Once again, congratulations on your success! We can’t wait to welcome you to our team.</p>

            <p>If you have any questions or need further information, feel free to reach out to us.</p>

            <p>Best regards, <br> The UNCST Team</p>
        </div>
    </div>
</body>
</html>
