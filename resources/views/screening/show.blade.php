<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $application->full_name }} — Screening</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f1f5f9; }
        @media print { .btn, form, .col-lg-4 { display: none !important; } }
    </style>
</head>
<body>
@php
    $statusBadge = [
        'pending'     => 'secondary',
        'shortlisted' => 'warning',
        'interviewed' => 'info',
        'offered'     => 'primary',
        'hired'       => 'success',
        'rejected'    => 'danger',
    ];
@endphp

<div class="container-fluid py-4">

    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $cls)
        @if (session($key))
            <div class="alert alert-{{ $cls }} alert-dismissible fade show mb-3">
                {{ session($key) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h5 class="mb-0">{{ $application->full_name }}</h5>
                <small class="opacity-75">
                    Ref: {{ $application->reference_number }} &bull;
                    {{ $job->job_title }} &bull;
                    Submitted {{ $application->created_at->format('d M Y') }}
                </small>
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="badge bg-{{ $statusBadge[$application->status] ?? 'secondary' }} fs-6">
                    {{ ucfirst($application->status) }}
                </span>
                <button onclick="window.print()" class="btn btn-light btn-sm">🖨 Print</button>
                <a href="{{ route('screening.show', $token) }}" class="btn btn-light btn-sm">← Back to List</a>
            </div>
        </div>

        <div class="card-body">
            <div class="row g-4">

                {{-- LEFT: application data --}}
                <div class="col-lg-8">

                    @if (!is_null($application->score) || !is_null($application->meets_criteria))
                        @php
                            $pass = $application->meets_criteria;
                            $sc   = $application->score ?? 0;
                            $scColor = $sc >= 70 ? 'success' : ($sc >= 40 ? 'warning' : 'danger');
                        @endphp
                        <div class="p-3 mb-4 rounded border
                            {{ $pass ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10' }}">
                            <div class="d-flex align-items-center gap-3 flex-wrap mb-2">
                                <div>
                                    <span class="fs-3 fw-bold text-{{ $scColor }}">{{ $sc }}</span>
                                    <span class="text-muted">/100</span>
                                </div>
                                <span class="badge bg-{{ $pass ? 'success' : 'danger' }} fs-6">
                                    {{ $pass ? '✓ Passes Criteria' : '✗ Fails Criteria' }}
                                </span>
                            </div>

                            @if (!empty($application->criteria_failures))
                                <div class="mb-2">
                                    <strong class="small text-danger">Disqualification reason(s):</strong>
                                    <ul class="mb-0 mt-1 small text-danger">
                                        @foreach ($application->criteria_failures as $f)
                                            <li>{{ $f }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if (!empty($application->score_breakdown))
                                <div class="row g-2 mt-1">
                                    @foreach ($application->score_breakdown as $factor => $data)
                                        <div class="col-6 col-md-3">
                                            <div class="bg-white rounded p-2 border text-center small">
                                                <div class="text-muted text-capitalize" style="font-size:.72rem">
                                                    {{ str_replace('_', ' ', $factor) }}
                                                </div>
                                                <div class="fw-bold">{{ $data['score'] }}<span class="text-muted fw-normal">/100</span></div>
                                                <div class="text-muted" style="font-size:.7rem">weight: {{ $data['weight'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <h6 class="fw-bold text-primary border-bottom border-primary pb-1 mb-3">Personal Details</h6>
                    <div class="row mb-4">
                        <div class="col-md-6"><dl class="row small mb-0">
                            <dt class="col-5 text-muted">Full Name</dt><dd class="col-7">{{ $application->full_name ?? '—' }}</dd>
                            <dt class="col-5 text-muted">Date of Birth</dt>
                            <dd class="col-7">{{ $application->date_of_birth?->format('d/m/Y') ?? '—' }}
                                {{ $application->date_of_birth ? '(Age '.$application->date_of_birth->age.')' : '' }}</dd>
                            <dt class="col-5 text-muted">Email</dt><dd class="col-7">{{ $application->email ?? '—' }}</dd>
                            <dt class="col-5 text-muted">Telephone</dt><dd class="col-7">{{ $application->telephone ?? '—' }}</dd>
                        </dl></div>
                        <div class="col-md-6"><dl class="row small mb-0">
                            <dt class="col-5 text-muted">Nationality</dt><dd class="col-7">{{ $application->nationality ?? '—' }}</dd>
                            <dt class="col-5 text-muted">Residency</dt><dd class="col-7">{{ $application->residency_type ?? '—' }}</dd>
                            <dt class="col-5 text-muted">District</dt>
                            <dd class="col-7">{{ implode(', ', array_filter([$application->home_district, $application->sub_county, $application->village])) ?: '—' }}</dd>
                        </dl></div>
                    </div>

                    <h6 class="fw-bold text-primary border-bottom border-primary pb-1 mb-3">Work Background</h6>
                    <div class="row mb-4">
                        <div class="col-md-6"><dl class="row small mb-0">
                            <dt class="col-5 text-muted">Dept / Employer</dt><dd class="col-7">{{ $application->present_department ?? '—' }}</dd>
                            <dt class="col-5 text-muted">Present Post</dt><dd class="col-7">{{ $application->present_post ?? '—' }}</dd>
                        </dl></div>
                        <div class="col-md-6"><dl class="row small mb-0">
                            <dt class="col-5 text-muted">Date Appointed</dt><dd class="col-7">{{ $application->date_of_appointment_present_post?->format('d/m/Y') ?? '—' }}</dd>
                            <dt class="col-5 text-muted">Terms</dt><dd class="col-7">{{ $application->terms_of_employment ?? '—' }}</dd>
                        </dl></div>
                    </div>

                    <h6 class="fw-bold text-primary border-bottom border-primary pb-1 mb-3">Education &amp; Training</h6>
                    @if (!empty($application->education_training))
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light small"><tr><th>Qualification</th><th>Institution</th><th>Year</th></tr></thead>
                                <tbody class="small">
                                    @foreach ($application->education_training as $r)
                                        @if (!empty(array_filter($r)))
                                            <tr>
                                                <td>{{ $r['qualification'] ?? '—' }}</td>
                                                <td>{{ $r['institution'] ?? '—' }}</td>
                                                <td>{{ $r['year'] ?? '—' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small mb-4">No education records provided.</p>
                    @endif

                    <h6 class="fw-bold text-primary border-bottom border-primary pb-1 mb-3">Employment Record</h6>
                    @if (!empty($application->employment_record))
                        <div class="table-responsive mb-4">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light small"><tr><th>Period</th><th>Position</th><th>Employer</th></tr></thead>
                                <tbody class="small">
                                    @foreach ($application->employment_record as $r)
                                        @if (!empty(array_filter($r)))
                                            <tr>
                                                <td>{{ $r['period'] ?? '—' }}</td>
                                                <td>{{ $r['position'] ?? '—' }}</td>
                                                <td>{{ $r['details'] ?? '—' }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted small mb-4">No employment records provided.</p>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary border-bottom border-primary pb-1 mb-2">Availability &amp; Salary</h6>
                            <dl class="row small mb-0">
                                <dt class="col-5 text-muted">Available</dt><dd class="col-7">{{ $application->availability ?? '—' }}</dd>
                                <dt class="col-5 text-muted">Min Salary</dt>
                                <dd class="col-7">{{ $application->salary_expectation ? 'UGX '.number_format($application->salary_expectation) : '—' }}</dd>
                            </dl>
                        </div>
                    </div>

                    <h6 class="fw-bold text-primary border-bottom border-primary pb-1 mb-3">References</h6>
                    <div class="row g-2 mb-4">
                        @forelse ($application->references ?? [] as $ref)
                            @if ($ref)
                                <div class="col-md-4">
                                    <div class="card border-primary h-100">
                                        <div class="card-body small py-2">{{ $ref }}</div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <div class="col"><p class="text-muted small">None provided.</p></div>
                        @endforelse
                    </div>
                    @if ($application->recommender_name)
                        <p class="small mb-4">
                            <strong>Recommender:</strong> {{ $application->recommender_name }}
                            @if ($application->recommender_title) — <em>{{ $application->recommender_title }}</em> @endif
                        </p>
                    @endif

                    <h6 class="fw-bold text-primary border-bottom border-primary pb-1 mb-3">Documents</h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="fw-semibold small mb-1">CV / Resume</div>
                            @if ($application->cv)
                                <a href="{{ asset('storage/'.$application->cv) }}" target="_blank"
                                    class="btn btn-outline-primary btn-sm">📄 View CV</a>
                            @else
                                <span class="text-muted small">Not uploaded.</span>
                            @endif
                        </div>
                        <div class="col-md-4">
                            <div class="fw-semibold small mb-1">Academic Documents</div>
                            @forelse ($application->academic_documents ?? [] as $doc)
                                <a href="{{ asset('storage/'.$doc) }}" target="_blank"
                                    class="d-block small text-primary mb-1">📄 {{ basename($doc) }}</a>
                            @empty
                                <span class="text-muted small">None.</span>
                            @endforelse
                        </div>
                        <div class="col-md-4">
                            <div class="fw-semibold small mb-1">Supporting Documents</div>
                            @forelse ($application->other_documents ?? [] as $doc)
                                <a href="{{ asset('storage/'.$doc) }}" target="_blank"
                                    class="d-block small text-primary mb-1">📄 {{ basename($doc) }}</a>
                            @empty
                                <span class="text-muted small">None.</span>
                            @endforelse
                        </div>
                    </div>

                </div>

                {{-- RIGHT: shortlist/reject panel --}}
                <div class="col-lg-4">
                    <div class="card border-0 bg-light">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Screening Decision</h6>

                            <form method="POST"
                                action="{{ route('screening.applications.status', [$token, $application->id]) }}">
                                @csrf
                                <button type="submit" name="status" value="shortlisted"
                                    class="btn btn-warning w-100 mb-2 {{ $application->status === 'shortlisted' ? 'active' : '' }}">
                                    ⭐ Shortlist Candidate
                                </button>
                            </form>

                            <form method="POST"
                                action="{{ route('screening.applications.status', [$token, $application->id]) }}"
                                id="rejectForm">
                                @csrf
                                <input type="hidden" name="status" value="rejected">
                                <label class="form-label small fw-semibold mt-2">Rejection Reason</label>
                                <textarea name="rejection_reason" class="form-control form-control-sm mb-2" rows="2"
                                    placeholder="Required when rejecting">{{ $application->status === 'rejected' ? $application->rejection_reason : '' }}</textarea>
                                <button type="submit" class="btn btn-outline-danger w-100">✕ Reject Candidate</button>
                            </form>

                            <div class="text-muted small mt-3 text-center">
                                The candidate is notified by email once a decision is recorded
                                (rejections wait until the application deadline passes).
                            </div>

                            <hr>
                            <dl class="row small mb-0">
                                <dt class="col-6 text-muted">Marital Status</dt>
                                <dd class="col-6">{{ $application->marital_status ?? '—' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
