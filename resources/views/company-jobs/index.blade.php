<x-app-layout>
    <div class="card shadow">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Job Postings</h5>
            <a href="{{ route('hr.company-jobs.create') }}" class="btn btn-light btn-sm">+ New Posting</a>
        </div>
        <div class="card-body">

            @foreach (['success' => 'success', 'error' => 'danger'] as $key => $cls)
                @if (session($key))
                    <div class="alert alert-{{ $cls }} alert-dismissible fade show">
                        {{ session($key) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
            @endforeach

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th><th>Title</th><th>Status</th>
                            <th>Opens</th><th>Closes</th><th>Applications</th>
                            <th>Screening</th><th>Application Link</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jobs as $job)
                            @php
                                $status = $job->statusLabel();
                                $color  = match($status) { 'active' => 'success', 'upcoming' => 'warning', default => 'secondary' };
                            @endphp
                            <tr>
                                <td><code class="small">{{ $job->job_code }}</code></td>
                                <td>
                                    <a href="{{ route('hr.company-jobs.show', $job->company_job_id) }}"
                                        class="fw-semibold text-decoration-none">{{ $job->job_title }}</a>
                                </td>
                                <td><span class="badge bg-{{ $color }}">{{ ucfirst($status) }}</span></td>
                                <td class="small">{{ $job->will_become_active_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td class="small">{{ $job->will_become_inactive_at?->format('d/m/Y H:i') ?? '—' }}</td>
                                <td>
                                    <a href="{{ route('hr.job-applications.index', ['company_job_id' => $job->company_job_id]) }}"
                                        class="badge bg-primary text-decoration-none">{{ $job->job_applications_count }}</a>
                                </td>
                                <td>
                                    @if ($job->hasCriteria())
                                        <span class="badge bg-info text-dark">✓ Set</span>
                                    @else
                                        <span class="text-muted small">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($status === 'active')
                                        <div class="input-group input-group-sm" style="min-width:160px;max-width:240px">
                                            <input type="text" class="form-control font-monospace"
                                                id="link_{{ $job->company_job_id }}"
                                                value="{{ $job->applicationLink() }}" readonly>
                                            <button class="btn btn-outline-secondary"
                                                onclick="copyLink('link_{{ $job->company_job_id }}', this)">📋</button>
                                        </div>
                                    @else
                                        <span class="text-muted small">{{ ucfirst($status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('hr.company-jobs.show', $job->company_job_id) }}"
                                        class="btn btn-sm btn-outline-primary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    No postings yet. <a href="{{ route('hr.company-jobs.create') }}">Create one.</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $jobs->links() }}
        </div>
    </div>
    <script>
        function copyLink(id, btn) {
            navigator.clipboard.writeText(document.getElementById(id).value).then(() => {
                btn.textContent = '✅'; setTimeout(() => btn.textContent = '📋', 2000);
            });
        }
    </script>
</x-app-layout>