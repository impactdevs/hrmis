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
                                        <th scope="row">{{ $application->application_id }}</th>
                                        <td>

                                            @if ($application->application_status == 'approve')
                                                <span class="badge text-bg-success">
                                                    {{ $data[93] }}
                                                </span>
                                            @else
                                                {{ $data[93] }}
                                            @endif
                                        </td>
                                        <td>

                                            @if ($application->application_status == 'approve')
                                                <span class="badge text-bg-success">
                                                    {{ $data[94] }}
                                                </span>
                                            @else
                                                {{ $data[94] }}
                                            @endif
                                        </td>
                                        <td>{{ $application->job->job_title }}</td>
                                        <td>Accepted</td>
                                        <td>{{ $application->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ url('entries', $application->entry->id) }}"
                                                class="btn btn-info text-white fs-6">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="#" class="btn btn-primary text-white fs-6" id="accept"
                                                data-application-id="{{ $application->application_id }}"
                                                data-application-request-status="approve">
                                                <i class="bi bi-check"></i>
                                            </a>

                                            <a href="#" class="btn btn-danger text-white fs-6" id="reject"
                                                data-application-id="{{ $application->application_id }}"
                                                data-application-request-status="reject">
                                                <i class="bi bi-x"></i>
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

    @push('scripts')
        <script>
            $(document).ready(function() {
                //accept button
                $(document).on('click', '#accept', function(e) {
                    e.preventDefault();

                    var appraisalId = $(this).data('application-id');
                    var applicationRequestStatus = $(this).data('application-request-status');

                    $.ajax({
                        url: '/application/application-approval/',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            application_id: appraisalId,
                            application_request_status: applicationRequestStatus
                        },
                        success: function(response) {
                            location.reload();
                            Toastify({
                                text: response.message,
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                            }).showToast();
                        },
                        error: function(xhr, status, error) {
                            Toastify({
                                text: xhr.responseJSON?.error || 'An error occurred',
                                duration: 3000,
                                gravity: "top",
                                position: "right",
                                backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)",
                            }).showToast();
                        }
                    });
                });

            });
        </script>
    @endpush

</x-app-layout>
