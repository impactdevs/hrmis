<x-app-layout>
    @php
        $status = $companyJob->statusLabel();
        $color  = match($status) { 'active' => 'success', 'upcoming' => 'warning', default => 'secondary' };
        $statusBadge = ['pending'=>'secondary','shortlisted'=>'warning','interviewed'=>'info','offered'=>'primary','hired'=>'success','rejected'=>'danger'];
    @endphp

    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $cls)
        @if (session($key))
            <div class="alert alert-{{ $cls }} alert-dismissible fade show mb-3">
                {{ session($key) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="row g-4">

        {{-- Job details + link --}}
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-start">
                    <div>
                        <h5 class="mb-0">{{ $companyJob->job_title }}</h5>
                        <small class="opacity-75">{{ $companyJob->job_code }}</small>
                    </div>
                    <a href="{{ route('hr.company-jobs.edit', $companyJob->company_job_id) }}"
                        class="btn btn-light btn-sm">Edit</a>
                </div>
                <div class="card-body">

                    {{-- Status + dates --}}
                    <div class="row g-3 mb-4">
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Status</div>
                                <span class="badge fs-6 bg-{{ $color }}">{{ ucfirst($status) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Opens</div>
                                <strong>{{ $companyJob->will_become_active_at?->format('d M Y, H:i') ?? '—' }}</strong>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="p-3 bg-light rounded">
                                <div class="text-muted small mb-1">Closes</div>
                                <strong class="{{ $companyJob->will_become_inactive_at?->isPast() ? 'text-danger' : '' }}">
                                    {{ $companyJob->will_become_inactive_at?->format('d M Y, H:i') ?? '—' }}
                                </strong>
                            </div>
                        </div>
                    </div>

                    {{-- Criteria summary --}}
                    @if ($companyJob->hasCriteria())
                        <div class="mb-4 p-3 bg-info bg-opacity-10 border border-info rounded">
                            <h6 class="fw-bold mb-2"> Screening Criteria (Auto-filter)</h6>
                            <div class="row g-2 small">
                                @if ($companyJob->criteria_min_qualification)
                                    <div class="col-md-6">
                                        <span class="text-muted">Min. Qualification:</span>
                                        <strong>{{ $companyJob->criteria_min_qualification }}</strong>
                                    </div>
                                @endif
                                @if (!is_null($companyJob->criteria_min_experience_years))
                                    <div class="col-md-6">
                                        <span class="text-muted">Min. Experience:</span>
                                        <strong>{{ $companyJob->criteria_min_experience_years }} year(s)</strong>
                                    </div>
                                @endif
                                @if (!is_null($companyJob->criteria_min_age) || !is_null($companyJob->criteria_max_age))
                                    <div class="col-md-6">
                                        <span class="text-muted">Age Range:</span>
                                        <strong>
                                            {{ $companyJob->criteria_min_age ?? '—' }} – {{ $companyJob->criteria_max_age ?? '—' }}
                                        </strong>
                                    </div>
                                @endif
                                @if (!empty($companyJob->criteria_required_keywords))
                                    <div class="col-12">
                                        <span class="text-muted">Required Keywords:</span>
                                        @foreach ($companyJob->criteria_required_keywords as $kw)
                                            <span class="badge bg-secondary ms-1">{{ $kw }}</span>
                                        @endforeach
                                    </div>
                                @endif
                                <div class="col-12 mt-1 pt-1 border-top">
                                    <span class="text-muted">Score weights:</span>
                                    Qualification: {{ $companyJob->weight_qualification }} &bull;
                                    Experience: {{ $companyJob->weight_experience }} &bull;
                                    Keywords: {{ $companyJob->weight_keyword_match }} &bull;
                                    Age fit: {{ $companyJob->weight_age_fit }}
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($companyJob->job_description)
                        <div class="mb-4">
                            <h6 class="fw-semibold">Job Description</h6>
                            <div class="text-muted small" style="white-space:pre-line">{{ $companyJob->job_description }}</div>
                        </div>
                    @endif

                    {{-- The shareable link --}}
                    <div class="p-4 rounded border {{ $status === 'active' ? 'border-success bg-success bg-opacity-10' : 'bg-light' }}">
                        <h6 class="fw-bold mb-1">
                            🔗 Application Link
                            <span class="badge bg-{{ $color }} ms-1">{{ ucfirst($status) }}</span>
                        </h6>
                        <p class="text-muted small mb-2">
                            Share this with candidates. It opens the form directly for this posting.
                            The form automatically blocks submissions outside the active window.
                        </p>
                        <div class="input-group mb-2">
                            <input type="text" class="form-control font-monospace"
                                id="appLink" value="{{ $companyJob->applicationLink() }}" readonly>
                            <button class="btn btn-success" onclick="copyAppLink()">📋 Copy</button>
                        </div>
                        <form method="POST"
                            action="{{ route('hr.company-jobs.regenerate-link', $companyJob->company_job_id) }}"
                            onsubmit="return confirm('The current link will stop working immediately. Continue?')"
                            class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger btn-sm">🔄 Regenerate Link</button>
                        </form>
                        <span class="text-muted small ms-2">Use this if the link was shared by mistake.</span>
                    </div>

                </div>
            </div>
        </div>

        {{-- Application stats --}}
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-light fw-semibold">Applications</div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="display-5 fw-bold">{{ $companyJob->job_applications_count }}</span>
                        <div class="text-muted small">Total Received</div>
                    </div>
                    @foreach (['pending'=>'secondary','shortlisted'=>'warning','interviewed'=>'info','offered'=>'primary','hired'=>'success','rejected'=>'danger'] as $s => $c)
                        <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                            <span class="badge bg-{{ $c }}">{{ ucfirst($s) }}</span>
                            <strong>{{ $statusCounts[$s] ?? 0 }}</strong>
                        </div>
                    @endforeach
                    <div class="d-grid gap-2 mt-3">
                        <a href="{{ route('hr.job-applications.index', ['company_job_id' => $companyJob->company_job_id]) }}"
                            class="btn btn-outline-primary btn-sm">📋 List View</a>
                        <a href="{{ route('hr.job-applications.pipeline', ['company_job_id' => $companyJob->company_job_id]) }}"
                            class="btn btn-outline-secondary btn-sm">🗂 Pipeline Board</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function copyAppLink() {
            navigator.clipboard.writeText(document.getElementById('appLink').value).then(() => {
                const btn = document.querySelector('.btn-success');
                btn.textContent = '✅ Copied!';
                setTimeout(() => btn.textContent = '📋 Copy', 2000);
            });
        }
    </script>
</x-app-layout>