<x-app-layout>
    <div class="mt-3">
        
            <div class="table-wrapper">
                <table class="table table-striped">
                    <thread>

                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Department</th>
                        <th scope="col">Email</th>
                        <th scope="col">Telephone</th>
                        <th scope="col">Job Title</th>
                        <th scope="col">Submission Type</th>
                        <th scope="col">Description</th>
                        <th scope="col">Individuals Involved</th>
                        <th scope="col">Evidence</th>
                        <th scope="col">Issue Reported</th>
                        <th scope="col">Resolution</th>
                        <th scope="col">Confidentiality Statement</th>
                    </tr>
                    </thread>
                    <tbody>
                        @forelse ($whistleblowers as $index => $whistleblower)
                        <tr class="align-middle">
                             <td>{{ $index + 1 }}</td>
                             <td>{{ $whistleblower->employee_name }}</td>
                             <td>{{ $whistleblower->employee_department }}</td>
                             <td>{{ $whistleblower->employee_email }}</td>
                             <td>{{ $whistleblower->employee_telephone }}</td>
                             <td>{{ $whistleblower->job_title }}</td>
                             <td>{{ $whistleblower->submission_type }}</td>
                             <td>{{ $whistleblower->description }}</td>
                             <td>{{ $whistleblower->individuals_involved }}</td>
                            <td style="max-width: 200px; overflow-y: auto;">
                                @if ($whistleblower->evidence && $whistleblower->evidence instanceof \Illuminate\Support\Collection)
                                    @foreach ($whistleblower->evidence as $item)
                                        <div>
                                            <strong>{{ $item->witness_name }}</strong><br>
                                            <small>{{ $item->email }}</small><br>
                                                @if ($item->document)
                                                    <a href="{{ asset('storage/' . $item->document) }}" target="_blank">View Document</a>
                                                @endif
                                        </div>
                                    @endforeach
                                    @else
                                    <span class="text-muted">No evidence submitted</span>
                                @endif
                            </td>

                             <td>{{ $whistleblower->issue_reported }}</td>
                             <td>{{ $whistleblower->resolution }}</td>
                             <td>{{ $whistleblower->confidentiality_statement }}</td>
                        </tr>

                            @empty
                        <tr>
                            <td colspan="13" class="text-center text-muted">No whistleblower reports found.</td>
                        </tr>
                        @endforelse


                    </tbody>
                </table>
                <div class="mt-4">
                    {{ $whistleblowers->links() }}
                </div>
            </div>
    </div>
</x-app-layout>
