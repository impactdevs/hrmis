<x-app-layout>
    <div class="container mt-5">
        <h5 class="mb-4">
            <i class="fas fa-user-secret"></i> Whistleblower Report Details
        </h5>

        <div class="row">
            <div class="col-md-6">

                <div class="mb-3 form-group">
                    <label><i class="fas fa-user"></i> Name</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->employee_name ?? 'Anonymous' }}
                    </p>
                </div>

                <div class="mb-3 form-group">
                    <label><i class="fas fa-building"></i> Department</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->employee_department }}
                    </p>
                </div>

                <div class="mb-3 form-group">
                    <label><i class="fas fa-envelope"></i> Email</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->employee_email ?? 'N/A' }}
                    </p>
                </div>



                <div class="mb-3 form-group">
                    <label><i class="fas fa-phone"></i> Telephone</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->employee_telephone }}
                    </p>
                </div>

                <div class="mb-3 form-group">
                    <label><i class="fas fa-briefcase"></i> Job Title</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->job_title }}
                    </p>
                </div>

                <div class="mb-3 form-group">
                    <label><i class="fas fa-user-shield"></i> Submission Type</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->submission_type }}
                    </p>
                </div>
            </div>

            <div class="col-md-6">

                <div class="mb-3 form-group">
                    <label><i class="fas fa-info-circle"></i> Description</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->description }}
                    </p>
                </div>

                <div class="mb-3 form-group">
                    <label><i class="fas fa-users"></i> Individuals Involved</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->individuals_involved }}
                    </p>
                </div>

                <div class="mb-3 form-group">
                    <label><i class="fas fa-file-alt"></i> Evidence</label>
                    <div class="p-2 border rounded bg-light">
                        @php
                            $evidenceIds = explode(',', $whistleblower->evidence['evidence'] ?? '');
                        @endphp
                        @foreach ($whistleblower->evidences as $evidence)
                            <div class="mb-3">
                                <p><strong>Witness:</strong> {{ $evidence->witness_name }} ({{ $evidence->email }})</p>
                                @if ($evidence->document)
                                    <a href="{{ asset('storage/' . $evidence->document) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    View Document
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-3 form-group">
                    <label><i class="fas fa-bullhorn"></i> Issue Reported</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->issue_reported }}
                    </p>
                </div>

                <div class="mb-3 form-group">
                    <label><i class="fas fa-gavel"></i> Resolution</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->resolution }}
                    </p>
                </div>

                <div class="mb-3 form-group">
                    <label><i class="fas fa-lock"></i> Confidentiality Statement</label>
                    <p class="p-2 border rounded bg-light">
                        {{ $whistleblower->confidentiality_statement }}
                    </p>
                </div>



            </div>
        </div>

        <a href="{{ route('whistleblowers.index') }}" class="btn btn-secondary mt-3">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</x-app-layout>
