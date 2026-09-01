<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>File Too Large — {{ config('app.name') }}</title>
    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            font-family: "Open Sans", "Segoe UI", sans-serif;
            background: #f6f9ff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .error-card {
            max-width: 480px;
            width: 100%;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(37, 99, 235, 0.08);
            padding: 40px 36px;
            text-align: center;
        }
        .error-card img.logo { height: 44px; margin-bottom: 20px; }
        .error-icon {
            width: 64px;
            height: 64px;
            border-radius: 50%;
            background: #fef2f2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 28px;
        }
        .error-card h1 { font-size: 20px; font-weight: 700; color: #1a1a1a; margin-bottom: 10px; }
        .error-card p { color: #555; font-size: 14px; line-height: 1.6; margin-bottom: 6px; }
        .limit-badge {
            display: inline-block;
            background: #eff6ff;
            color: #2563eb;
            font-weight: 600;
            font-size: 13px;
            padding: 4px 12px;
            border-radius: 999px;
            margin: 14px 0 22px;
        }
        .actions { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }
    </style>
</head>
<body>
    <div class="error-card">
        <img class="logo" src="{{ asset('assets/img/logo.png') }}" alt="UNCST">
        <div class="error-icon">&#9888;</div>
        <h1>That upload was too large</h1>
        <p>The file (or overall form data) you submitted is bigger than this server accepts in a single request.</p>
        <div class="limit-badge">Maximum upload size: {{ $maxUploadSize }}</div>
        <p>Please go back and try again with a smaller file — for handover notes, a PDF under 2&nbsp;MB works best. If your document is a scanned copy, try compressing it or splitting it into fewer pages.</p>
        <div class="actions mt-3">
            <a href="javascript:history.back()" class="btn btn-primary">
                Go Back
            </a>
            <a href="{{ url('/leaves') }}" class="btn btn-outline-secondary">
                Back to Leave Requests
            </a>
        </div>
    </div>
</body>
</html>
