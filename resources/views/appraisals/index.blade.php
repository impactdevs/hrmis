<x-app-layout>

    <div class="py-12">
        <div class="d-flex flex-row flex-1 justify-content-between">
            <h5 class="ms-3">Appraisal</h5>

            <div>
                <a href="{{ route('appraisal.survey') }}" class="btn border-t-neutral-50 btn-primary">
                    <i class="bi bi-database-add me-2"></i>Appraisal Form
                </a>
                <a href="{{ route('appraisal.survey') }}" class="btn border-t-neutral-50 btn-info text-light"
                    id="copyLink">
                    <i class="bi bi-link-45deg"></i>Copy Appraisal Link
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
                                <th scope="col">Full Name</th>
                                <th scope="col">Position</th>
                                <th scope="col">Status</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (filled($appraisals))
                                @foreach ($appraisals as $appraisal)
                                    @php
                                        // Assuming $response contains the JSON string
                                        $data = json_decode($appraisal->entry->responses, true); // true for associative array
                                    @endphp
                                    <tr>
                                        <th scope="row">{{ $appraisal->entry->id }}</th>
                                        <td>
                                            @if ($appraisal->appraisal_request_status == 'approve')
                                                <span class="badge text-bg-success">
                                                    {{ $data[76] }}
                                                </span>
                                            @else
                                                {{ $data[76] }}
                                            @endif
                                        </td>
                                        <td>{{ $data[77] }}</td>
                                        <td>Accepted</td>
                                        <td>
                                            <a href="{{ url('entries', $appraisal->entry->id) }}"
                                                class="btn btn-info text-white fs-6">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            <a href="#" class="btn btn-primary text-white fs-6" id="accept"
                                                data-appraisal-id="{{ $appraisal->appraisal_id }}"
                                                data-appraisal-request-status="approve">
                                                <i class="bi bi-check"></i>
                                            </a>

                                            <a href="#" class="btn btn-danger text-white fs-6" id="reject"
                                                data-appraisal-id="{{ $appraisal->appraisal_id }}"
                                                data-appraisal-request-status="reject">
                                                <i class="bi bi-x"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="pagination-wrapper">
                        {!! $appraisals->appends(['search' => request()->get('search')])->render() !!}
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

                    var appraisalId = $(this).data('appraisal-id');
                    var appraisalRequestStatus = $(this).data('appraisal-request-status');

                    $.ajax({
                        url: '/appraisal/appraisal-approval/',
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: {
                            appraisal_id: appraisalId,
                            appraisal_request_status: appraisalRequestStatus
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
