<x-app-layout>

    <div class="py-12">
        <div class="d-flex flex-row justify-content-between align-items-center mb-4 ms-5">
            <form method="GET" action="{{ route('applications.index') }}">
                <select name="job_id" class="form-select" onchange="this.form.submit()">
                    <option value="">Select Job</option>
                    @foreach ($company_jobs as $job)
                        <option value="{{ $job->company_job_id }}"
                            {{ request()->get('job_id') == $job->company_job_id ? 'selected' : '' }}>
                            {{ $job->job_title }}
                        </option>
                    @endforeach
                </select>
            </form>

            <div>
                <a href="{{ route('form-builder.show', '5b39330c-9bed-4289-a60b-d19947d5f5d9') }}"
                    class="btn border-t-neutral-50 btn-primary">
                    <i class="bi bi-database-add me-2"></i>Application Form
                </a>
                <a href="{{ route('application.survey') }}" class="btn border-t-neutral-50 btn-info text-light"
                    id="copyLink">
                    <i class="bi bi-link-45deg"></i>Copy Application Link
                </a>
            </div>
        </div>

        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table id="entriesTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
                                <th scope="col">Job</th>
                                <th scope="col">Application Date</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (filled($applications))
                                @foreach ($applications as $application)
                                    @php
                                        $data = json_decode($application->entry->responses, true);
                                    @endphp
                                    <tr>
                                        <th scope="row">{{ $application->id }}</th>
                                        <td>{{ $data[93] }}</td>
                                        <td>{{ $data[94] }}</td>
                                        <td>{{ $application->job->job_title }}</td>
                                        <td>Accepted</td>
                                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ url('entries', $application->entry->id) }}"
                                                class="btn btn-info text-white">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="pagination-wrapper">
                        {!! $applications->appends(['job_id' => request()->get('job_id'), 'search' => request()->get('search')])->render() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
