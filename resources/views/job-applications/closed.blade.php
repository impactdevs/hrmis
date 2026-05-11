<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Unavailable</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f1f5f9; }</style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 text-center">
            <img src="{{ asset('assets/img/logo.png') }}" alt="UNCST" class="img-fluid mb-4" style="max-height:70px;">
            <div class="card shadow p-5">
                <div style="font-size:2.8rem;margin-bottom:12px;">🔒</div>
                <h3 class="fw-bold mb-3">Application Unavailable</h3>
                <p class="text-muted">{{ $message }}</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
