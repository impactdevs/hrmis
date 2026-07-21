<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Received</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; color: #333; }
        .wrap { max-width: 580px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        .header { background: #1a56db; padding: 28px 36px; text-align: center; }
        .header img { max-height: 60px; }
        .body { padding: 32px 36px; }
        .body p { line-height: 1.7; margin-bottom: 16px; }
        .detail-box { background: #f8f9fa; border-radius: 6px; padding: 14px 18px; margin: 20px 0; }
        .detail-box table { width: 100%; font-size: .9rem; border-collapse: collapse; }
        .detail-box td { padding: 5px 0; }
        .detail-box td:first-child { color: #6c757d; width: 40%; }
        .btn { display: inline-block; padding: 13px 30px; background: #1a56db; color: #fff !important;
               text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 1rem; }
        .fallback { font-size: .82rem; color: #6c757d; word-break: break-all; }
        .footer { padding: 18px 36px; background: #f8f9fa; text-align: center; font-size: .78rem; color: #6c757d; border-top: 1px solid #e9ecef; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <img src="{{ asset('assets/img/logo.png') }}" alt="UNCST">
    </div>
    <div class="body">
        <p>Dear <strong>{{ $application->full_name }}</strong>,</p>
        <p>
            Thank you for submitting your application for the position of
            <strong>{{ $application->post_applied }}</strong>.
            We have received it successfully.
        </p>

        <div class="detail-box">
            <table>
                <tr><td>Reference Number</td><td><strong>{{ $application->reference_number }}</strong></td></tr>
                <tr><td>Position</td><td>{{ $application->post_applied }}</td></tr>
                <tr><td>Submitted</td><td>{{ $application->created_at->format('d M Y, H:i') }}</td></tr>
            </table>
        </div>

        <p>
            Your application is now final and cannot be edited. Please keep this email and your
            reference number for your records.
        </p>
    </div>
    <div class="footer">
        Uganda National Council for Science &amp; Technology &bull; Kampala, Uganda<br>
        This is an automated message. Please do not reply to this email.
    </div>
</div>
</body>
</html>