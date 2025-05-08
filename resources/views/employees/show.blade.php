<x-app-layout>
    <div class="container">
        <div class="mb-4 shadow-lg card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="flex-grow-1">
                    <h2 class="mb-0 text-dark">{{ $employee->title }} {{ $employee->first_name }}
                        {{ $employee->last_name }}
                        <span class="text-muted">(Remaining with {{ $employee->retirementYearsRemaining() }} to
                            retire)</span>
                    </h2>
                </div>
                <div class="d-flex align-items-center gap-">
                    {{-- <a href="{{ route('employees.print', $employee->employee_id) }}" target="_blank" class="btn btn-primary btn-sm"
                        onclick="window.print()">
                        <i class="fas fa-print"></i> Print Profile
                    </a> --}}
                    <!-- Add PDF Button Here -->
                    <a href="{{ route('employees.generate-pdf', $employee->employee_id) }}" target="_blank"
                        class="btn btn-danger btn-sm" title="Generate PDF">
                        <i class="fas fa-file-pdf"></i> Export PDF
                    </a>
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
                <section class="p-3 mb-4 border rounded border-1 border-secondary">
                    <h1 class="mt-4 mb-3 text-dark">Basic Information</h1>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Staff ID:</strong></div>
                        <div class="col-md-8">{{ $employee->staff_id ?? 'No Staff ID' }}</div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Position:</strong></div>
                        <div class="col-md-8">{{ optional($employee->position)->position_name }}</div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>NIN:</strong></div>
                        <div class="col-md-8">{{ $employee->nin ?? 'No NIN' }}</div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>National ID:</strong></div>
                        <div class="col-md-8">
                            @if ($employee->national_id_photo)
                                <img src="{{ asset('storage/' . $employee->national_id_photo) }}"
                                    alt="National ID Photo" class="rounded img-fluid">
                            @else
                                <p class="text-muted">No national ID photo provided.</p>
                            @endif
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Date of Entry:</strong></div>
                        <div class="col-md-8">{{ $employee->date_of_entry ?? 'No Date Specified' }}</div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Contract Expiry Date:</strong></div>
                        <div class="col-md-8">{{ $employee->contract_expiry_date ?? 'No Date Specified' }}</div>
                    </div>

                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Department</strong></div>
                        <div class="col-md-8">{{ $employee->department->department_name ?? 'No Department' }}</div>
                    </div>
                    {{-- job --}}
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Job Description:</strong></div>
                        <div class="col-md-8">{{ $employee->job_description ?? 'No Job Description' }}</div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Contract Documents:</strong></div>
                        <div class="col-md-8">
                            @if (!is_null($employee->contract_documents))
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
                                                            class="mt-2 rounded img-fluid" style="max-width: 120px;">
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
                                                    <p class="text-muted">Unsupported file type: {{ $item['title'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @else
                                <p>No contract documents</p>
                            @endif
                        </div>
                    </div>
                </section>

                <!-- Other sections for Department, Contact Information, etc. remain the same... -->
                <section class="p-3 mb-4 border rounded border-1 border-secondary">
                    <div class="flex-row mb-4 d-flex justify-content-between align-items-center">
                        <h5 class="mt-4 mb-3 text-dark">Contracts Information</h5>
                        @if (auth()->user()->isAdminOrSecretary)
                        <a href="{{ route('contract.create', ['employee_id' => $employee->employee_id]) }}"
                           class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-2"></i>Add New Contract
                        </a>
                        @endif
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped"
                               data-toggle="table"
                               data-search="true"
                               data-show-columns="true"
                               data-sortable="true"
                               data-pagination="true"
                               data-page-size="5">
                            <thead class="text-white bg-primary">
                                <tr>
                                    <th data-sortable="true" data-field="id">#</th>
                                    <th data-sortable="true" data-field="start_date">Start Date</th>
                                    <th data-sortable="true" data-field="end_date">End Date</th>
                                    <th data-field="duration">Duration</th>
                                    <th data-field="description">Description</th>
                                    <th data-field="attachments">Attachments</th>
                                    <th data-sortable="true" data-field="status">Status</th>
                                    @can('can edit an employee')
                                    <th data-field="actions">Actions</th>
                                    @endcan
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employee->contracts as $contract)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $contract->start_date->format('d M Y') }}</td>
                                    <td>{{ $contract->end_date->format('d M Y') }}</td>
                                    <td>{{ $contract->start_date->diffInDays($contract->end_date) }} days</td>
                                    <td class="text-truncate" style="max-width: 200px;" title="{{ $contract->description }}">
                                        {{ $contract->description }}
                                    </td>
                                    <td>
                                        @if($contract->contract_documents)
                                            <div class="flex-wrap gap-2 d-flex">
                                                @foreach($contract->contract_documents as $attachment)
                                                <a href="{{ asset('storage/'.$attachment['proof']) }}"
                                                   target="_blank"
                                                   class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                                                    @if(Str::endsWith($attachment['proof'], ['.pdf']))
                                                        <i class="fas fa-file-pdf text-danger me-2"></i>
                                                    @else
                                                        <i class="fas fa-file-image text-primary me-2"></i>
                                                    @endif
                                                    {{ Str::limit($attachment['title'], 15) }}
                                                </a>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-muted">No attachments</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $status = $contract->end_date->isPast() ? 'Expired' : 'Active';
                                            $badgeClass = $status === $status ? 'bg-success' : 'bg-danger';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} ">{{ $status }}</span>
                                    </td>
                                    @can('can edit an employee')
                                    <td>
                                        <div class="gap-2 d-flex">
                                            <a href="{{ route('contract.edit', $contract->id) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               data-bs-toggle="tooltip"
                                               title="Edit Contract">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('contract.destroy', $contract->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                        data-bs-toggle="tooltip"
                                                        title="Delete Contract"
                                                        onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                    @endcan
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Contact Information Section -->
                <section class="p-3 mb-4 border rounded border-1 border-secondary">
                    <h5 class="mt-4 mb-3 text-dark">Contact Information</h5>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Mobile Number:</strong></div>
                        <div class="col-md-8">{{ $employee->phone_number ?? 'No Mobile Number' }}</div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Email:</strong></div>
                        <div class="col-md-8">{{ $employee->email ?? 'No Email' }}</div>
                    </div>
                    <div class="mb-3 row">
                        <div class="col-md-4"><strong>Next of Kin:</strong></div>
                        <div class="col-md-8">{{ $employee->next_of_kin ?? 'No Email' }}</div>
                    </div>
                </section>

                <!-- Qualifications Section -->
                <section class="p-3 mb-4 border rounded border-1 border-secondary">
                    <h5 class="mt-4 mb-3 text-dark">Qualifications</h5>
                    @if (!is_null($employee->qualifications_details))
                        @foreach ($employee->qualifications_details as $item)
                            @if (isset($item['proof']))
                                <div class="mb-3 row">
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
                                                class="mt-2 rounded img-fluid" style="max-width: 120px;">
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
                    @else
                        <p>No qualifications</p>
                    @endif
                </section>
            </div>
        </div>
        @can('can delete an employee')
            <div class="mt-4 text-center">
                <a href="{{ route('employees.index') }}" class="btn btn-primary">Back to Employee List</a>
            </div>
        @endcan
    </div>
</x-app-layout>
