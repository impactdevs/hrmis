<x-app-layout>
    <div class="container">
        <h1 class="mb-4 text-center text-primary">Employee Details</h1>

        <div class="card mb-4 shadow-lg">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h2 class="mb-0 text-dark">{{ $employee->title }} {{ $employee->first_name }}
                        {{ $employee->last_name }}
                        <span class="text-muted">(Remaining with {{ $employee->retirementYearsRemaining() }} to
                            retire)</span>
                    </h2>
                </div>
                <div class="text-center">
                    @if ($employee->passport_photo)
                        <img src="{{ asset('storage/' . $employee->passport_photo) }}" alt="Passport Photo"
                            class="img-fluid rounded-circle" width="100">
                    @else
                        <span class="text-muted">No passport photo available.</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Basic Information Section -->
                <section class="mb-4 border border-1 border-secondary p-3 rounded">
                    <h1 class="mt-4 mb-3 text-dark">Basic Information</h1>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Staff ID:</strong></div>
                        <div class="col-md-8">{{ $employee->staff_id ?? 'No Staff ID' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Position:</strong></div>
                        <div class="col-md-8">{{ optional($employee->position)->position_name }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>NIN:</strong></div>
                        <div class="col-md-8">{{ $employee->nin ?? 'No NIN' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>National ID:</strong></div>
                        <div class="col-md-8">
                            @if ($employee->national_id_photo)
                                <img src="{{ asset('storage/' . $employee->national_id_photo) }}"
                                    alt="National ID Photo" class="img-fluid rounded">
                            @else
                                <p class="text-muted">No national ID photo provided.</p>
                            @endif
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Date of Entry:</strong></div>
                        <div class="col-md-8">{{ $employee->date_of_entry ?? 'No Date Specified' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Contract Expiry Date:</strong></div>
                        <div class="col-md-8">{{ $employee->contract_expiry_date ?? 'No Date Specified' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Department</strong></div>
                        <div class="col-md-8">{{ $employee->department->department_name ?? 'No Department' }}</div>
                    </div>
                    {{-- job --}}
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Job Description:</strong></div>
                        <div class="col-md-8">{{ $employee->job_description ?? 'No Job Description' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Contract Documents:</strong></div>
                        <div class="col-md-8">
                            <div class="mt-2">
                                @foreach ($employee->contract_documents as $item)
                                    @if (isset($item['proof']))
                                        <div class="mb-2">
                                            @php
                                                $filePath = asset('storage/' . $item['proof']);
                                                $fileExtension = pathinfo($item['proof'], PATHINFO_EXTENSION);
                                            @endphp
                                            @if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                                <!-- Display Image -->
                                                <div>
                                                    <img src="{{ $filePath }}" alt="{{ $item['title'] }}"
                                                        class="img-fluid rounded mt-2" style="max-width: 120px;">
                                                </div>
                                            @elseif ($fileExtension === 'pdf')
                                                <!-- Display PDF Link -->
                                                <div>
                                                    <a href="{{ $filePath }}" target="_blank"
                                                        class="d-flex align-items-center text-decoration-none">
                                                        <img src="{{ asset('assets/img/pdf-icon.png') }}"
                                                            alt="PDF icon" class="pdf-icon me-2" width="24">
                                                        <span
                                                            class="text-dark">{{ $item['title'] ?? 'The document has no title' }}</span>
                                                    </a>
                                                </div>
                                            @else
                                                <!-- Handle other file types -->
                                                <p class="text-muted">Unsupported file type: {{ $item['title'] }}</p>
                                            @endif
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Other sections for Department, Contact Information, etc. remain the same... -->

                <!-- Contact Information Section -->
                <section class="mb-4 border border-1 border-secondary p-3 rounded">
                    <h5 class="mt-4 mb-3 text-dark">Contact Information</h5>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Mobile Number:</strong></div>
                        <div class="col-md-8">{{ $employee->phone_number ?? 'No Mobile Number' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Email:</strong></div>
                        <div class="col-md-8">{{ $employee->email ?? 'No Email' }}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Next of Kin:</strong></div>
                        <div class="col-md-8">{{ $employee->next_of_kin ?? 'No Email' }}</div>
                    </div>
                </section>

                <!-- Qualifications Section -->
                <section class="mb-4 border border-1 border-secondary p-3 rounded">
                    <h5 class="mt-4 mb-3 text-dark">Qualifications</h5>
                    @foreach ($employee->qualifications_details as $item)
                        @if (isset($item['proof']))
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Qualification:</strong></div>
                                <div class="col-md-8">
                                    <div>{{ $item['title'] }}</div>
                                    @php
                                        $qualificationFilePath = asset('storage/' . $item['proof']);
                                        $qualificationFileExtension = pathinfo($item['proof'], PATHINFO_EXTENSION);
                                    @endphp
                                    @if (in_array($qualificationFileExtension, ['jpg', 'jpeg', 'png', 'gif']))
                                        <!-- Display Image for Qualification -->
                                        <img src="{{ $qualificationFilePath }}" alt="Qualification Proof"
                                            class="img-fluid rounded mt-2" style="max-width: 120px;">
                                    @elseif ($qualificationFileExtension === 'pdf')
                                        <!-- Display PDF Link for Qualification -->
                                        <a href="{{ $qualificationFilePath }}" target="_blank"
                                            class="d-flex align-items-center text-decoration-none">
                                            <img src="{{ asset('assets/img/pdf-icon.png') }}" alt="PDF icon"
                                                class="pdf-icon me-2" width="24">
                                            <span class="text-dark">View Qualification Proof</span>
                                        </a>
                                    @else
                                        <p class="text-muted">Unsupported qualification file type.</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </section>
            </div>
        </div>
        @can('can delete an employee')
        <div class="text-center mt-4">
            <a href="{{ route('employees.index') }}" class="btn btn-primary">Back to Employee List</a>
        </div>
        @endcan
    </div>
</x-app-layout>
