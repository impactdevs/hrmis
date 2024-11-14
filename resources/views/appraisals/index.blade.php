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
                                <th scope="col">First Name</th>
                                <th scope="col">Last Name</th>
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
                                        <th scope="row">
                                            {{ $appraisal->entry->id }}
                                        </th>
                                        <td>


                                            {{ $appraisal->employee->first_name }}

                                        </td>
                                        <td> {{ $appraisal->employee->last_name }}</td>
                                        <td>{{ $appraisal->employee->position->position_name }}</td>
                                        <td>
                                            @if (!is_null($appraisal->approval_status))
                                                <span
                                                    class="badge {{ $appraisal->approval_status == 'reject' ? 'text-bg-danger' : 'text-bg-success' }}">
                                                    {{ $appraisal->approval_status == 'reject' ? 'Rejected' : 'Approved' }}
                                                </span>
                                            @else
                                                <span class="badge text-bg-warning">Pending</span>
                                            @endif

                                        </td>
                                        <td>
                                            <a href="{{ url('entries', $appraisal->entry->id) }}"
                                                class="btn btn-info text-white fs-6">
                                                <i class="bi bi-eye"></i>
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



</x-app-layout>
