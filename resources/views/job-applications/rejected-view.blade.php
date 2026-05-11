<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Outcome</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f1f5f9; }</style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <img src="{{ asset('assets/img/logo.png') }}" alt="UNCST" class="d-block mx-auto img-fluid mb-4" style="max-height:70px;">
            <div class="card shadow p-5">
                <h4 class="fw-bold mb-1">Application: {{ $application->reference_number }}</h4>
                <p class="text-muted mb-4">{{ $application->post_applied }}</p>

                <div class="alert alert-danger">
                    <strong>Your application was not successful.</strong><br>
                    It did not meet the minimum requirements for this position and has been automatically declined.
                </div>

                @if (!empty($application->criteria_failures))
                    <h6 class="fw-semibold mt-3 mb-2">Reason(s):</h6>
                    <ul class="text-muted small">
                        @foreach ($application->criteria_failures as $failure)
                            <li>{{ $failure }}</li>
                        @endforeach
                    </ul>
                @endif

                <p class="text-muted small mt-3">
                    A notification email was sent to {{ $application->email }}.
                    We encourage you to apply for future positions that match your qualifications.
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
