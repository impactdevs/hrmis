<x-app-layout>
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Job Applications</h5>
                <a href="{{ route('applications.create') }}" class="btn btn-light">
                    Go to the application Form
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Enhanced Filters Section --}}
            <div class="mb-4 border-bottom pb-3">
                <form method="GET" action="{{ route('applications.index') }}">
                    <div class="row g-3 align-items-end">
                        {{-- Job Post Filter --}}
                        <div class="col-md-3">
                            <label for="company_job_id" class="form-label fw-medium">Filter by Job Post</label>
                            <select class="form-select" name="company_job_id" id="company_job_id">
                                <option value="">All Job Posts</option>
                                @foreach ($companyJobs as $job)
                                    <option value="{{ $job->company_job_id }}"
                                        {{ request('company_job_id') == $job->company_job_id ? 'selected' : '' }}>
                                        {{ $job->job_title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Date Range Filter --}}
                        <div class="col-md-4">
                            <label class="form-label fw-medium">Application Date Range</label>
                            <div class="row g-2">
                                <div class="col">
                                    <input type="date" name="created_from" class="form-control" 
                                        value="{{ request('created_from') }}" placeholder="From" id="created_from">
                                </div>
                                <div class="col">
                                    <input type="date" name="created_to" class="form-control" 
                                        value="{{ request('created_to') }}" placeholder="To" id="created_to">
                                </div>
                            </div>
                        </div>

                        {{-- Search Filter --}}
                        <div class="col-md-3">
                            <label for="search" class="form-label fw-medium">Search Applications</label>
                            <input type="text" name="search" class="form-control" 
                                placeholder="Ref# or Applicant Name..." value="{{ request('search') }}" id="search">
                        </div>

                        {{-- Action Buttons --}}
                        <div class="col-md-2">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-filter me-2"></i>Apply Filters
                                </button>
                                <a href="{{ route('applications.index') }}" class="btn btn-outline-secondary">
                                    Clear Filters
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table Section --}}
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ref Number</th>
                            <th>Applicant Name</th>
                            <th>Post Applied</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td>{{ $application->reference_number }}</td>
                                <td>{{ $application->full_name }}</td>
                                @php
                                    $job = \App\Models\CompanyJob::where(
                                        'job_code',
                                        $application->reference_number,
                                    )->first();
                                @endphp
                                <td>{{ $job ? $job->job_title : 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('applications.show', $application->id) }}"
                                        class="btn btn-sm btn-primary">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No applications found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $applications->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>