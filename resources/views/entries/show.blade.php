<x-app-layout>
    <div class="py-12">
        @foreach ($entry->formatted_responses as $key => $field)
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg transition-transform transform hover:scale-105">
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <div class="mb-4 d-flex flex-column">
                            <h6 class="font-bold text-lg {{ empty($field) ? 'text-red-600' : 'text-gray-800' }}">
                                Question {{ $loop->iteration }}: {{ $key }}
                                @if (empty($field))
                                    <span class="text-red-500"> - No answer</span>
                                @endif
                            </h6>
                            @if ($key != 'CV')
                                @if (is_array($field))
                                    <ul class="list-disc pl-5 mt-2">
                                        @foreach ($field as $item)
                                            @if (is_array($item))
                                                <table class="min-w-full border-collapse border border-gray-300">
                                                    <tbody>
                                                        <tr class="border-b border-gray-300">
                                                            @foreach ($item as $exp)
                                                                <td class="px-4 py-2">{{ $exp }}</td>
                                                            @endforeach
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            @else
                                                <li class="text-blue-600 mt-2">{{ $item }}</li>
                                            @endif
                                        @endforeach
                                    </ul>
                                @else
                                    <p class="text-green-600 mt-2">{{ $field }}</p>
                                @endif
                            @else
                                {{-- show a pdf
                                     --}}
                                <a href="{{ asset('storage/' . $field) }}" target="_blank"
                                    class="d-flex align-items-center text-decoration-none">
                                    <img src="{{ asset('assets/img/pdf-icon.png') }}" alt="PDF icon"
                                        class="pdf-icon me-2" width="24">
                                    <span class="text-dark">View CV</span>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Handle Appraisals and Applications Status -->
        @foreach (['appraisals', 'applications'] as $type)
            @if (array_key_exists(0, $entry->{$type}->toArray()))
                <div id="status-container" class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
                    @if (!is_null($entry->{$type}[0]->approval_status))
                        <div
                            class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg transition-transform transform hover:scale-105">
                            <div class="p-6 text-gray-900 dark:text-gray-100">
                                <div class="mb-4 d-flex flex-column">
                                    <h6 class="font-bold text-lg text-gray-800">
                                        {{ ucfirst($type) }} Status:
                                        {{ $entry->{$type}[0]->approval_status == 'reject' ? 'Rejected' : 'Approved' }}
                                    </h6>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Approval and Rejection Buttons -->
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
                    @php
                        $typeMod = $type == 'appraisals' ? 'appraisal' : 'application';
                    @endphp
                    <button class="btn btn-primary text-white fs-6 approve-btn"
                        data-{{ $type }}-id="{{ $entry->{$type}[0]->{$typeMod . '_id'} }}"
                        data-{{ $type }}-request-status="approve" data-type="{{ $type }}">
                        <i class="bi bi-check"></i> APPROVE {{ strtoupper($type) }}
                    </button>

                    <button class="btn btn-danger text-white fs-6 reject-btn"
                        data-{{ $type }}-id="{{ $entry->{$type}[0]->{$typeMod . '_id'} }}"
                        data-bs-toggle="modal" data-bs-target="#rejectModal" data-type="{{ $type }}">
                        <i class="bi bi-x"></i> REJECT {{ strtoupper($type) }}
                </button>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Bootstrap Modal for Rejection Reason -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="rejectionReason">Please enter the reason for rejection:</label>
                    <textarea id="rejectionReason" class="form-control" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                let currentId;
                let currentType;

                $('.approve-btn').click(function() {
                    const type = $(this).data('type');
                    const id = $(this).data(type + '-id');
                    updateStatus(type, id, 'approve');
                });

                $('.reject-btn').click(function() {
                    const type = $(this).data('type');
                    currentId = $(this).data(type + '-id');
                    currentType = $(this).data('type');
                });

                $('#confirmReject').click(function() {
                    const reason = $('#rejectionReason').val();
                    if (reason) {
                        updateStatus(currentType, currentId, 'reject', reason);
                        $('#rejectModal').modal('hide');
                    } else {
                        alert('Please enter a rejection reason.');
                    }
                });

                function updateStatus(type, id, status, reason = null) {
                    console.log(type, id, status, reason);
                    let route = (type == "appraisals") ? '/appraisal/appraisal-approval/' :
                        '/application/application-approval/';
                    $.ajax({
                        url: route,
                        type: 'POST',
                        contentType: 'application/json',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        data: JSON.stringify({
                            status: status,
                            reason: reason,
                            [`${type}_id`]: id
                        }),
                        success: function(data) {
                            let statusHtml = `<div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg transition-transform transform hover:scale-105">
                                <div class="p-6 text-gray-900 dark:text-gray-100">
                                    <div class="mb-4 d-flex flex-column">
                                        <h6 class="font-bold text-lg text-gray-800">Status: ${status === 'approve' ? 'Approved' : 'Rejected'}</h6>
                                        ${status === 'reject' && reason ? `<p><strong>Rejection Reason:</strong> ${reason}</p>` : ''}
                                    </div>
                                </div>
                            </div>`;

                            $('#status-container').html(statusHtml);

                            $('.approve-btn').prop('disabled', true);
                            $('.reject-btn').prop('disabled', true);

                            Toastify({
                                text: data.message,
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
                }
            });
        </script>
    @endpush
</x-app-layout>
