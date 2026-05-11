<x-app-layout>
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0">Job Applications</h5>
            <a href="{{ route('hr.job-applications.pipeline') }}" class="btn btn-light btn-sm">
                🗂 Pipeline Board
            </a>
        </div>
        <div class="card-body">

            @foreach (['success' => 'success', 'error' => 'danger'] as $key => $cls)
                @if (session($key))
                    <div class="alert alert-{{ $cls }} alert-dismissible fade show">
                        {{ session($key) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach

            {{-- Filters --}}
            <form method="GET" action="{{ route('hr.job-applications.index') }}" class="mb-4 p-3 bg-light rounded border">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Job Post</label>
                        <select class="form-select form-select-sm" name="company_job_id">
                            <option value="">All Posts</option>
                            @foreach ($companyJobs as $job)
                                <option value="{{ $job->company_job_id }}"
                                    {{ request('company_job_id') == $job->company_job_id ? 'selected' : '' }}>
                                    {{ $job->job_title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold mb-1">Status</label>
                        <select class="form-select form-select-sm" name="status">
                            <option value="">All Statuses</option>
                            @foreach (\App\Models\JobApplication::statuses() as $s)
                                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>
                                    {{ ucfirst($s) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small fw-semibold mb-1">Date Range</label>
                        <div class="input-group input-group-sm">
                            <input type="date" name="created_from" class="form-control" value="{{ request('created_from') }}">
                            <span class="input-group-text">to</span>
                            <input type="date" name="created_to" class="form-control" value="{{ request('created_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small fw-semibold mb-1">Search</label>
                        <input type="text" name="search" class="form-control form-control-sm"
                            placeholder="Name or Ref#" value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary btn-sm flex-fill">Filter</button>
                            <a href="{{ route('hr.job-applications.index') }}" class="btn btn-outline-secondary btn-sm">✕</a>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Table --}}
            @php
                $sort      = request('sort', 'score');
                $direction = request('direction', 'desc');
                $sortLink  = fn($col) => route('hr.job-applications.index', array_merge(
                    request()->except(['sort', 'direction']),
                    ['sort' => $col, 'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc']
                ));
                $statusBadge = [
                    'pending'     => 'secondary',
                    'shortlisted' => 'warning',
                    'interviewed' => 'info',
                    'offered'     => 'primary',
                    'hired'       => 'success',
                    'rejected'    => 'danger',
                ];
            @endphp

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><a href="{{ $sortLink('reference_number') }}" class="text-decoration-none text-dark">Ref #</a></th>
                            <th><a href="{{ $sortLink('full_name') }}" class="text-decoration-none text-dark">Applicant</a></th>
                            <th>Post</th>
                            <th>Age</th>
                            <th><a href="{{ $sortLink('status') }}" class="text-decoration-none text-dark">Status</a></th>
                            <th>
                                <a href="{{ $sortLink('score') }}" class="text-decoration-none text-dark">Score ↕</a>
                                <span class="text-muted small fw-normal">/100</span>
                            </th>
                            <th>Criteria</th>
                            <th><a href="{{ $sortLink('created_at') }}" class="text-decoration-none text-dark">Date</a></th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $app)
                            <tr class="{{ $app->meets_criteria === false ? 'table-danger bg-opacity-25' : '' }}">
                                <td><code class="small">{{ $app->reference_number }}</code></td>
                                <td>{{ $app->full_name }}</td>
                                <td class="small">{{ $app->companyJob?->job_title ?? $app->post_applied ?? '—' }}</td>
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
                                <td class="small text-muted">{{ $app->created_at->format('d/m/Y') }}</td>
                                <td>
                                    <a href="{{ route('hr.job-applications.show', $app->id) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No applications found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <form method="GET" action="{{ route('hr.job-applications.index') }}" class="d-flex align-items-center gap-2">
                    @foreach (request()->except('per_page') as $k => $val)
                        <input type="hidden" name="{{ $k }}" value="{{ $val }}">
                    @endforeach
                    <select name="per_page" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
                        @foreach ([15, 25, 50] as $n)
                            <option value="{{ $n }}" {{ request('per_page', 15) == $n ? 'selected' : '' }}>{{ $n }} / page</option>
                        @endforeach
                    </select>
                </form>
                {{ $applications->appends(request()->query())->links() }}
            </div>

        </div>
    </div>
</x-app-layout>