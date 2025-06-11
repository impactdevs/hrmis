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
                                <dt>Residency Type</dt>
                                <dd>{{ $application->residency_type ?? '-' }}</dd>
                            </dl>
                        </div>

                        <div class="col-md-4">
                            <dl>
                                <dt>NIN</dt>
                                <dd>{{ $application->nin ?? '-' }}</dd>

                            
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
                    </div>
                </div>

                {{-- education and training summary --}}
                                <!-- Employment Record -->
                <div class="section-card mb-4">
                    <h5 class="section-title"><i class="fas fa-history mr-2"></i>Education Training</h5>
                    @if (!empty($application->education_training))
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Qualification</th>
                                        <th>Institution</th>
                                        <th>Year Of Award</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($application->education_training as $record)
                                        <tr>
                                            <td>{{ $record['qualification'] ?? '-' }}</td>
                                            <td>{{ $record['institution'] ?? '-' }}</td>
                                            <td>{{ $record['year'] ?? '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">No Education training provided</div>
                    @endif
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
                                        <th>Position</th>
                                        <th>Employer Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($application->employment_record as $record)
                                        <tr>
                                            <td>{{ $record['period'] ?? '-' }}</td>
                                            <td>{{ $record['position'] ?? '-' }}</td>
                                            <td>{{ $record['details'] ?? '-' }}</td>
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
