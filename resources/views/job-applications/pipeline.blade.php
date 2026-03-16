<x-app-layout>
    <style>
        .pipeline-wrap { display: flex; gap: 14px; overflow-x: auto; padding-bottom: 20px; align-items: flex-start; }
        .pipeline-col  { flex: 0 0 230px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; display: flex; flex-direction: column; }
        .pipeline-col-header { padding: 10px 14px; border-radius: 10px 10px 0 0; font-weight: 700; font-size: .85rem; display: flex; justify-content: space-between; align-items: center; }
        .pipeline-cards { padding: 10px; flex: 1; min-height: 80px; }
        .app-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 11px 13px; margin-bottom: 10px; font-size: .82rem; cursor: pointer; transition: box-shadow .15s; }
        .app-card:hover { box-shadow: 0 3px 12px rgba(0,0,0,.1); }
        .app-card .name { font-weight: 600; color: #1e293b; margin-bottom: 2px; }
        .app-card .meta { color: #94a3b8; font-size: .75rem; }
        .score-bar { height: 4px; border-radius: 2px; margin-top: 6px; background: #e2e8f0; }
        .score-bar-fill { height: 100%; border-radius: 2px; }
        .col-pending     .pipeline-col-header { background: #f1f5f9; color: #475569; }
        .col-shortlisted .pipeline-col-header { background: #fef3c7; color: #92400e; }
        .col-interviewed .pipeline-col-header { background: #e0f2fe; color: #075985; }
        .col-offered     .pipeline-col-header { background: #dbeafe; color: #1e3a8a; }
        .col-hired       .pipeline-col-header { background: #d1fae5; color: #065f46; }
        .col-rejected    .pipeline-col-header { background: #fee2e2; color: #7f1d1d; }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-bold">Application Pipeline</h5>
            <small class="text-muted">Drag-free — use the buttons on each card to move applications</small>
        </div>
        <div class="d-flex gap-2 align-items-center">
            {{-- Job filter --}}
            <form method="GET" action="{{ route('hr.job-applications.pipeline') }}" class="d-flex gap-2">
                <select name="company_job_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Job Posts</option>
                    @foreach ($companyJobs as $job)
                        <option value="{{ $job->company_job_id }}"
                            {{ $jobId == $job->company_job_id ? 'selected' : '' }}>
                            {{ $job->job_title }}
                        </option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('hr.job-applications.index', $jobId ? ['company_job_id' => $jobId] : []) }}"
                class="btn btn-outline-secondary btn-sm">☰ List View</a>
        </div>
    </div>

    @foreach (['success' => 'success', 'error' => 'danger'] as $key => $cls)
        @if (session($key))
            <div class="alert alert-{{ $cls }} alert-dismissible fade show mb-3">
                {{ session($key) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    @php
        $statusLabels = [
            'pending'     => '⏳ Pending',
            'shortlisted' => '🌟 Shortlisted',
            'interviewed' => '📋 Interviewed',
            'offered'     => '📄 Offered',
            'hired'       => '✅ Hired',
            'rejected'    => '✗ Rejected',
        ];
        $nextStatus = [
            'pending'     => 'shortlisted',
            'shortlisted' => 'interviewed',
            'interviewed' => 'offered',
            'offered'     => 'hired',
        ];
    @endphp

    <div class="pipeline-wrap">
        @foreach ($columns as $status => $applications)
            <div class="pipeline-col col-{{ $status }}">
                <div class="pipeline-col-header">
                    <span>{{ $statusLabels[$status] }}</span>
                    <span class="badge bg-white bg-opacity-50 text-dark">{{ $applications->count() }}</span>
                </div>
                <div class="pipeline-cards">
                    @forelse ($applications as $app)
                        <div class="app-card" onclick="window.location='{{ route('hr.job-applications.show', $app->id) }}'">
                            <div class="name">{{ $app->full_name }}</div>
                            <div class="meta">{{ $app->reference_number }}</div>
                            <div class="meta">{{ $app->companyJob?->job_title ?? $app->post_applied ?? '—' }}</div>

                            @if (!is_null($app->score))
                                @php $sc = $app->score; $fill = $sc >= 70 ? '#16a34a' : ($sc >= 40 ? '#f59e0b' : '#ef4444'); @endphp
                                <div class="score-bar"><div class="score-bar-fill" style="width:{{ $sc }}%;background:{{ $fill }}"></div></div>
                                <div class="meta mt-1">Score: <strong>{{ $sc }}/100</strong></div>
                            @endif

                            {{-- Quick action buttons --}}
                            <div class="d-flex gap-1 mt-2 flex-wrap" onclick="event.stopPropagation()">
                                @if (isset($nextStatus[$status]))
                                    <form method="POST"
                                        action="{{ route('hr.job-applications.updateStatus', $app->id) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $nextStatus[$status] }}">
                                        <button type="submit" class="btn btn-xs btn-outline-success py-0 px-2"
                                            style="font-size:.72rem">
                                            → {{ ucfirst($nextStatus[$status]) }}
                                        </button>
                                    </form>
                                @endif
                                @if ($status !== 'rejected')
                                    <form method="POST"
                                        action="{{ route('hr.job-applications.updateStatus', $app->id) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="rejected">
                                        <input type="hidden" name="rejection_reason" value="Rejected via pipeline board.">
                                        <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-2"
                                            style="font-size:.72rem"
                                            onclick="return confirm('Reject {{ addslashes($app->full_name) }}?')">
                                            Reject
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-muted text-center small py-3">No applications</div>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</x-app-layout>