<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('List of submitted entries') }}
            </h2>

            {{-- settings button --}}

        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table id="entriesTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Title</th>
                                <th scope="col">Sub title</th>
                                <th scope="col">Created By</th>
                                <th scope="col">Created At</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (filled($entries))
                                @foreach ($entries as $entry)
                                    <tr>
                                        <th scope="row">{{ $entry->id }}</th>
                                        <td>{{ $entry->title }}</td>
                                        <td>{{ $entry->subtitle }}</td>
                                        <td>{{ $entry->user->name ?? 'Unknown User' }}</td>
                                        <td>{{ $entry->created_at->format('M d, Y') }}</td>
                                        <td>
                                            <a href="{{ url('entries', $entry->id) }}" class="">
                                                <i class="bi bi-eye"></i>
                                            </a>

                                            {{-- edit entry with pencil icon --}}
                                            <form action="{{ route('entries.edit', $entry->id) }}" method="GET"
                                                class="d-inline">
                                                @csrf
                                                @method('PUT')
                                                {{-- hidden form_id field --}}
                                                <input type="hidden" name="form_id" value="{{ $entry->form_id }}">
                                                <button type="submit" class="btn btn-link">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('script')
        <!-- Initialize DataTable -->
        <script>
            $(document).ready(function() {
                $('#entriesTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "lengthChange": true, // Ensure the length menu is shown
                    "lengthMenu": [10, 20, 30, 40, 50, 60, 70], // Define options for number of entries to show
                    // Export buttons
                    dom: "Bflrtip",
                    // Style the buttons
                    buttons: [{
                            extend: "csv",
                            className: "btn btn-warning btn-small text-white",
                            messageTop: "Comments about Arrears",
                        },
                        {
                            extend: "excel",
                            className: "btn btn-warning btn-small text-white",
                            messageTop: "Comments about Arrears",
                        },
                        {
                            extend: "pdf",
                            className: "btn btn-warning btn-small text-white",
                            messageTop: "Comments about Arrears",
                            customize: function(doc) {
                                doc.styles.tableHeader.fillColor = '#FFA500';
                            }
                        },
                        {
                            extend: "print",
                            className: "btn btn-warning btn-small text-white",
                            messageTop: "Comments about Arrears",
                        },
                    ],
                });
            });
        </script>
    @endpush

</x-app-layout>
