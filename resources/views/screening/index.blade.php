<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Screening — {{ $job->job_title }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>body { background: #f1f5f9; }</style>
</head>
<body>
<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <div class="d-flex align-items-center gap-2">
            <img src="{{ asset('assets/img/logo.png') }}" alt="UNCST" style="max-height:44px;">
            <div>
                <h5 class="mb-0">Screening Board</h5>
                <small class="text-muted">{{ $job->job_title }} &bull; {{ $applications->count() }} applicant(s)</small>
            </div>
        </div>
        <form method="POST" action="{{ route('screening.exit', $job->screening_token) }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary btn-sm">Exit Session</button>
        </form>
    </div>

    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $cls)
        @if (session($key))
            <div class="alert alert-{{ $cls }} alert-dismissible fade show">
                {{ session($key) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

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

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ref #</th>
                            <th>Applicant</th>
                            <th>Age</th>
                            <th>Status</th>
                            <th>Score <span class="text-muted small fw-normal">/100</span></th>
                            <th>Criteria</th>
                            <th>Details</th>
                            <th style="min-width:220px;">Decision</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $app)
                            <tr class="{{ $app->meets_criteria === false ? 'table-danger bg-opacity-25' : '' }}">
                                <td><code class="small">{{ $app->reference_number }}</code></td>
                                <td>{{ $app->full_name }}</td>
                                <td>{{ $app->date_of_birth?->age ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-{{ $statusBadge[$app->status] ?? 'secondary' }}">
                                        {{ ucfirst($app->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if (!is_null($app->score))
                                        @php $sc = $app->score; $scColor = $sc >= 70 ? 'success' : ($sc >= 40 ? 'warning' : 'danger'); @endphp
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="progress flex-fill" style="height:6px; min-width:60px">
                                                <div class="progress-bar bg-{{ $scColor }}" style="width:{{ $sc }}%"></div>
                                            </div>
                                            <span class="fw-bold text-{{ $scColor }} small">{{ $sc }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">Not scored</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($app->meets_criteria === true)
                                        <span class="text-success small">✓ Pass</span>
                                    @elseif ($app->meets_criteria === false)
                                        <span class="text-danger small">✗ Fail</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('screening.applications.show', [$job->screening_token, $app->id]) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                                <td>
                                    <form method="POST"
                                        action="{{ route('screening.applications.status', [$job->screening_token, $app->id]) }}"
                                        class="d-flex gap-1 reject-form">
                                        @csrf
                                        <button type="submit" name="status" value="shortlisted"
                                            class="btn btn-sm btn-warning {{ $app->status === 'shortlisted' ? 'active' : '' }}">
                                            ⭐ Shortlist
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger reject-btn">
                                            ✕ Reject
                                        </button>
                                        <input type="hidden" name="status" class="reject-status-input" disabled>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">No applications for this posting yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Reject reason modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reject Candidate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label small fw-semibold">Reason (shown internally, not to the candidate)</label>
                <textarea id="rejectReasonText" class="form-control" rows="3" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger btn-sm" id="confirmRejectBtn">Confirm Reject</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    let activeRejectForm = null;
    const rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));

    document.querySelectorAll('.reject-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            activeRejectForm = this.closest('form');
            document.getElementById('rejectReasonText').value = '';
            rejectModal.show();
        });
    });

    document.getElementById('confirmRejectBtn').addEventListener('click', function () {
        const reason = document.getElementById('rejectReasonText').value.trim();
        if (!reason) {
            alert('Please provide a reason for rejection.');
            return;
        }
        if (activeRejectForm) {
            const statusInput = activeRejectForm.querySelector('.reject-status-input');
            statusInput.disabled = false;
            statusInput.value = 'rejected';

            const reasonInput = document.createElement('input');
            reasonInput.type = 'hidden';
            reasonInput.name = 'rejection_reason';
            reasonInput.value = reason;
            activeRejectForm.appendChild(reasonInput);

            activeRejectForm.submit();
        }
    });
</script>
</body>
</html>
