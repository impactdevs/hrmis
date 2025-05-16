<x-app-layout>
    <div class="container-fluid py-4">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0"><i class="fas fa-file-invoice mr-2"></i>Application Details -
                        {{ $application->reference_number }}</h3>
                    <div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- Personal Details Section -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-user-circle mr-2"></i>Personal Details</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <dl>
                                <dt>Full Name</dt>
                                <dd>{{ $application->full_name ?? '-' }}</dd>

                                <dt>Date of Birth</dt>
                                <dd>{{ $application->date_of_birth ? $application->date_of_birth->format('d/m/Y') : '-' }}
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-4">
                            <dl>
                                <dt>Email</dt>
                                <dd>{{ $application->email ?? '-' }}</dd>

                                <dt>Telephone</dt>
                                <dd>{{ $application->telephone ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-4">
                            <dl>
                                <dt>Nationality</dt>
                                <dd>{{ $application->nationality ?? '-' }}</dd>

                                <dt>Post Applied</dt>
                                <dd>{{ $application->post_applied ?? '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Residence Information -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-home mr-2"></i>Residence Details</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <dl>
                                <dt>Home District</dt>
                                <dd>{{ $application->home_district ?? '-' }}</dd>

                                <dt>Sub County</dt>
                                <dd>{{ $application->sub_county ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-4">
                            <dl>
                                <dt>Village</dt>
                                <dd>{{ $application->village ?? '-' }}</dd>

                                <dt>Residency Type</dt>
                                <dd>{{ $application->residency_type ?? '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Work Background -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-briefcase mr-2"></i>Work Background</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <dl>
                                <dt>Current Department</dt>
                                <dd>{{ $application->present_department ?? '-' }}</dd>

                                <dt>Current Post</dt>
                                <dd>{{ $application->present_post ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-4">
                            <dl>
                                <dt>Appointment Date</dt>
                                <dd>{{ $application->date_of_appointment_present_post ? $application->date_of_appointment_present_post->format('d/m/Y') : '-' }}
                                </dd>

                                <dt>Current Salary</dt>
                                <dd>{{ $application->present_salary ? number_format($application->present_salary) : '-' }}
                                </dd>
                            </dl>
                        </div>
                        <div class="col-md-4">
                            <dl>
                                <dt>Employment Terms</dt>
                                <dd>{{ $application->terms_of_employment ?? '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Family Background -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-users mr-2"></i>Family Background</h5>
                    <div class="row">
                        <div class="col-md-4">
                            <dl>
                                <dt>Marital Status</dt>
                                <dd>{{ $application->marital_status ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-4">
                            <dl>
                                <dt>Number of Children</dt>
                                <dd>{{ $application->number_of_children ?? '-' }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-4">
                            <dl>
                                <dt>Children Ages</dt>
                                <dd>{{ $application->children_ages ?? '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- Education Section -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-graduation-cap mr-2"></i>Education History</h5>
                    @if (!empty($application->education_history))
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Period</th>
                                        <th>Institution</th>
                                        <th>Award</th>
                                        <th>Class</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($application->education_history as $edu)
                                        <tr>
                                            <td>{{ $edu['period'] ?? '-' }}</td>
                                            <td>{{ $edu['institution'] ?? '-' }}</td>
                                            <td>{{ $edu['award'] ?? '-' }}</td>
                                            <td>{{ $edu['class'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No education history provided</div>
                    @endif
                </div>

                <!-- Examination Results -->
                <!-- Examination Results -->
                <div class="row mb-4">
                    <!-- UCE Section -->
                    <div class="col-md-6">
                        <div class="section-card h-100">
                            <h5 class="section-title"><i class="fas fa-file-alt mr-2"></i>UCE Details</h5>
                            @if (!empty($application->uce_details))
                                <div class="mb-3">
                                    <span
                                        class="badge {{ $application->uce_details['passed'] == 'yes' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $application->uce_details['passed'] == 'yes' ? 'Passed' : 'Not Passed' }}
                                    </span>
                                    <span class="text-muted ml-2">Year:
                                        {{ $application->uce_details['year'] ?? '-' }}</span>
                                </div>

                                @if (!empty($application->uce_details['scores']))
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Subject</th>
                                                    <th>Grade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($application->uce_details['scores'] as $score)
                                                    <tr>
                                                        <td>{{ $score['subject'] ?? '-' }}</td>
                                                        <td>{{ $score['grade'] ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-2 mb-0">No UCE subjects recorded</div>
                                @endif
                            @else
                                <div class="alert alert-info py-2 mb-0">No UCE details provided</div>
                            @endif
                        </div>
                    </div>

                    <!-- UACE Section -->
                    <div class="col-md-6">
                        <div class="section-card h-100">
                            <h5 class="section-title"><i class="fas fa-file-certificate mr-2"></i>UACE Details</h5>
                            @if (!empty($application->uace_details))
                                <div class="mb-3">
                                    <span
                                        class="badge {{ $application->uace_details['passed'] == 'yes' ? 'badge-success' : 'badge-danger' }}">
                                        {{ $application->uace_details['passed'] == 'yes' ? 'Passed' : 'Not Passed' }}
                                    </span>
                                    <span class="text-muted ml-2">Year:
                                        {{ $application->uace_details['year'] ?? '-' }}</span>
                                </div>

                                @if (!empty($application->uace_details['scores']))
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered">
                                            <thead class="bg-light">
                                                <tr>
                                                    <th>Subject</th>
                                                    <th>Grade</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($application->uace_details['scores'] as $score)
                                                    <tr>
                                                        <td>{{ $score['subject'] ?? '-' }}</td>
                                                        <td>{{ $score['grade'] ?? '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-warning py-2 mb-0">No UACE subjects recorded</div>
                                @endif
                            @else
                                <div class="alert alert-info py-2 mb-0">No UACE details provided</div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Employment Record -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-history mr-2"></i>Employment Record</h5>
                    @if (!empty($application->employment_record))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Period</th>
                                        <th>Employer</th>
                                        <th>Position</th>
                                        <th>Duties</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($application->employment_record as $record)
                                        <tr>
                                            <td>{{ $record['period'] ?? '-' }}</td>
                                            <td>{{ $record['employer'] ?? '-' }}</td>
                                            <td>{{ $record['position'] ?? '-' }}</td>
                                            <td>{{ $record['duties'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No employment records provided</div>
                    @endif
                </div>

                <!-- Criminal History -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-gavel mr-2"></i>Criminal History</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <dl class="row">
                                <dt class="col-sm-3">Convicted of Crime?</dt>
                                <dd class="col-sm-9">

                                    <span
                                        class="badge {{ $application->criminal_convicted ? 'bg-success' : 'bg-danger' }}">
                                        {{ $application->criminal_convicted ? 'Yes' : 'No' }}
                                    </span>
                                </dd>

                                @if ($application->criminal_convicted)
                                    <dt class="col-sm-3">Details</dt>
                                    <dd class="col-sm-9">{{ $application->criminal_details }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <!-- References Section -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-user-check mr-2"></i>References</h5>
                    <div class="row">
                        @if (!empty($application->references))
                            @foreach ($application->references as $reference)
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-body">
                                            <p class="card-text">

                                                {{ $reference }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-md-12">
                                <div class="alert alert-info">No references provided</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- References Section -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-user-check mr-2"></i>Recommender</h5>
                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <div class="card border-primary">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $application->recommender_name ?? '' }}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">{{ $reference->recommender_title ?? '' }}</small>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add this new Documents Section -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-file-archive mr-2"></i>Application Documents</h5>

                    <!-- Academic Documents -->
                    <div class="mb-4">
                        <h6 class="sub-section-title"><i class="fas fa-graduation-cap mr-2"></i>Academic Documents
                        </h6>
                        @if (!empty($application->academic_documents))
                            <div class="list-group">
                                @foreach ($application->academic_documents as $doc)
                                    <a href="{{ asset('storage/' . $doc) }}" target="_blank"
                                        class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        <span>
                                            <i class="fas fa-file-pdf text-danger mr-2"></i>
                                            {{ basename($doc) }}
                                        </span>
                                        <i class="fas fa-external-link-alt text-muted"></i>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">No academic documents uploaded</div>
                        @endif
                    </div>

                    <!-- CV -->
                    <div class="mb-4">
                        <h6 class="sub-section-title"><i class="fas fa-file-contract mr-2"></i>Curriculum Vitae</h6>
                        @if ($application->cv)
                            <a href="{{ asset('storage/' . $application->cv) }}" target="_blank"
                                class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye mr-2"></i>View CV ({{ basename($application->cv) }})
                            </a>
                        @else
                            <div class="alert alert-info">No CV uploaded</div>
                        @endif
                    </div>

                    <!-- Other Documents -->
                    <div class="mb-4">
                        <h6 class="sub-section-title"><i class="fas fa-file-alt mr-2"></i>Supporting Documents</h6>
                        @if (!empty($application->other_documents))
                            <div class="row">
                                @foreach ($application->other_documents as $doc)
                                    <div class="col-md-4 mb-3">
                                        <div class="card document-card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $extension = pathinfo($doc, PATHINFO_EXTENSION);
                                                        $icon =
                                                            $extension === 'pdf'
                                                                ? 'file-pdf text-danger'
                                                                : 'file-image text-primary';
                                                    @endphp
                                                    <i
                                                        class="fas fa-{{ $extension === 'pdf' ? 'file-pdf' : 'file-image' }} fa-2x mr-3"></i>
                                                    <div class="flex-grow-1">
                                                        <small class="text-muted d-block">{{ strtoupper($extension) }}
                                                            Document</small>
                                                        <a href="{{ asset('storage/' . $doc) }}" target="_blank"
                                                            class="text-truncate d-block" style="max-width: 200px">
                                                            {{ basename($doc) }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-info">No supporting documents uploaded</div>
                        @endif
                    </div>
                </div>
                <!-- End of Documents Section -->

            </div>
        </div>
    </div>

    <style>
        .section-card {
            padding: 1.5rem;
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            margin-bottom: 1.5rem;
            background: #fff;
        }

        .section-title {
            color: #4e73df;
            border-bottom: 2px solid #4e73df;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-weight: 600;
        }

        dt {
            font-weight: 500;
            color: #6c757d;
        }

        dd {
            color: #3a3b45;
            margin-bottom: 0.8rem;
        }

        .table thead {
            background-color: #f8f9fc;
        }
    </style>
</x-app-layout>
