<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply — {{ $job->job_title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    @include('job-applications.form', [
        'formAction'  => route('apply.store', $job->public_token),
        'formMethod'  => 'POST',
        'job'         => $job,
        'application' => null,
        'encodedId'   => null,
    ])
</body>
</html>