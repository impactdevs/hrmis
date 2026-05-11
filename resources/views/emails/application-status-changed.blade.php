<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Update</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f6fb; margin: 0; padding: 0; color: #333; }
        .wrap { max-width: 580px; margin: 40px auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,.08); }
        .header { background: #1a56db; padding: 28px 36px; text-align: center; }
        .header img { max-height: 60px; }
        .banner { padding: 16px 36px; text-align: center; }
        .banner.shortlisted { background: #fef3c7; color: #92400e; }
        .banner.interviewed { background: #e0f2fe; color: #075985; }
        .banner.offered     { background: #dbeafe; color: #1e3a8a; }
        .banner.hired       { background: #d1fae5; color: #064e3b; }
        .banner.rejected    { background: #fee2e2; color: #7f1d1d; }
        .banner h2 { margin: 0; font-size: 1.25rem; }
        .body { padding: 28px 36px; }
        .body p { line-height: 1.7; margin-bottom: 14px; }
        .detail-box { background: #f8f9fa; border-radius: 6px; padding: 12px 16px; margin: 18px 0; font-size: .9rem; }
        .detail-box table { width: 100%; border-collapse: collapse; }
        .detail-box td { padding: 4px 0; }
        .detail-box td:first-child { color: #6c757d; width: 40%; }
        .rejection-note { background: #fff5f5; border-left: 4px solid #ef4444; padding: 12px 16px; border-radius: 4px; margin: 16px 0; font-size: .92rem; }
        .footer { padding: 16px 36px; background: #f8f9fa; text-align: center; font-size: .78rem; color: #6c757d; border-top: 1px solid #e9ecef; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="header">
        <img src="{{ asset('assets/img/logo.png') }}" alt="UNCST">
    </div>

    <div class="banner {{ $application->status }}">
        <h2>
            @switch($application->status)
                @case('shortlisted') 🌟 You Have Been Shortlisted @break
                @case('interviewed') 📋 Interview Stage Update @break
                @case('offered')     🎉 Job Offer @break
                @case('hired')       ✅ Appointment Confirmed @break
                @case('rejected')    📩 Application Outcome @break
                @default             📬 Application Update
            @endswitch
        </h2>
    </div>

    <div class="body">
        <p>Dear <strong>{{ $application->full_name }}</strong>,</p>

        @switch($application->status)
            @case('shortlisted')
                <p>We are pleased to inform you that your application for <strong>{{ $application->post_applied }}</strong> has been reviewed and you have been <strong>shortlisted</strong> for the next stage of our selection process. We will be in touch with further details.</p>
                @break

            @case('interviewed')
                <p>Thank you for your continued interest in the position of <strong>{{ $application->post_applied }}</strong>. Your application is now at the interview review stage. We will notify you of the outcome as soon as a decision is reached.</p>
                @break

            @case('offered')
                <p>We are delighted to inform you that following our review, we would like to extend a <strong>formal job offer</strong> for the position of <strong>{{ $application->post_applied }}</strong>. A member of our HR team will contact you with the full offer details shortly.</p>
                @break

            @case('hired')
                <p>Congratulations! We are thrilled to confirm your appointment to the position of <strong>{{ $application->post_applied }}</strong>. <strong>Welcome to the team.</strong> Our HR department will be in touch with your onboarding information.</p>
                @break

            @case('rejected')
                <p>Thank you for your interest in the position of <strong>{{ $application->post_applied }}</strong> and for the time you invested in your application. After careful review, we regret to inform you that your application has been <strong>unsuccessful</strong> at this time.</p>
                @if ($application->rejection_reason)
                    <div class="rejection-note">
                        <strong>Reason:</strong><br>{{ $application->rejection_reason }}
                    </div>
                @endif
                <p>We encourage you to apply for future opportunities that match your qualifications.</p>
                @break

            @default
                <p>The status of your application for <strong>{{ $application->post_applied }}</strong> has been updated to <strong>{{ ucfirst($application->status) }}</strong>.</p>
        @endswitch

        <div class="detail-box">
            <table>
                <tr><td>Reference</td><td><strong>{{ $application->reference_number }}</strong></td></tr>
                <tr><td>Position</td><td>{{ $application->post_applied }}</td></tr>
                <tr><td>Status</td><td><strong>{{ ucfirst($application->status) }}</strong></td></tr>
            </table>
        </div>
    </div>

    <div class="footer">
        Uganda National Council for Science &amp; Technology &bull; Kampala, Uganda<br>
        This is an automated message. Please do not reply to this email.
    </div>
</div>
</body>
</html>