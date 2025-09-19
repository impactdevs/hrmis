<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Appraisal Print Preview - {{ $appraisal->employee->first_name ?? 'N/A' }} {{ $appraisal->employee->last_name ?? 'N/A' }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom print styles -->
    <link href="{{ asset('assets/css/custom-css.css') }}" rel="stylesheet">
    
    <style>
        /* Print preview specific styles */
        .print-preview-header {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 1rem;
            margin-bottom: 2rem;
            border-radius: 0.5rem;
        }
        
        .print-actions {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            background: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            border: 1px solid #ddd;
        }
        
        .print-content {
            max-width: 8.5in;
            margin: 0 auto;
            background: white;
            padding: 1in;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            min-height: 11in;
        }
        
        .preview-mode {
            background-color: #f5f5f5;
            padding: 2rem 0;
        }
        
        .attachment-summary {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            border-radius: 0.375rem;
            padding: 1rem;
            margin: 1rem 0;
        }
        
        .attachment-item {
            display: flex;
            align-items: center;
            justify-content: between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .attachment-item:last-child {
            border-bottom: none;
        }
        
        .attachment-icon {
            margin-right: 0.5rem;
            color: #666;
        }
        
        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .status-draft { background-color: #fff3cd; color: #856404; }
        .status-submitted { background-color: #d1ecf1; color: #0c5460; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .status-completed { background-color: #d4edda; color: #155724; }
        
        /* Hide elements that shouldn't be printed */
        @media print {
            .print-actions,
            .print-preview-header,
            .no-print {
                display: none !important;
            }
            
            .print-content {
                box-shadow: none;
                padding: 0;
                max-width: 100%;
            }
            
            .preview-mode {
                background-color: white;
                padding: 0;
            }
        }
    </style>
</head>
<body class="preview-mode">
    
    <!-- Print Actions (hidden when printing) -->
    <div class="print-actions no-print">
        <div class="d-flex flex-column gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="{{ route('appraisals.download', $appraisal->appraisal_id) }}" class="btn btn-success btn-sm">
                <i class="fas fa-file-pdf me-1"></i> PDF
            </a>
            @if ($attachmentAccessInfo['can_access'] && !empty($appraisal->relevant_documents))
                <a href="{{ route('appraisals.attachments.download-all', $appraisal->appraisal_id) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-download me-1"></i> All Attachments
                </a>
            @endif
            <button onclick="history.back()" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back
            </button>
        </div>
    </div>

    <!-- Print Preview Header (hidden when printing) -->
    <div class="container-fluid print-preview-header no-print">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h3 class="mb-1">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Appraisal Print Preview
                </h3>
                <p class="mb-0 text-muted">
                    Employee: {{ $appraisal->employee->first_name ?? 'N/A' }} {{ $appraisal->employee->last_name ?? 'N/A' }}
                    | Review Period: {{ $appraisal->appraisal_start_date?->format('M d, Y') ?? 'N/A' }} - {{ $appraisal->appraisal_end_date?->format('M d, Y') ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-4 text-end">
                @php
                    $status = 'draft';
                    if (isset($appraisal->appraisal_request_status['Executive Secretary']) && $appraisal->appraisal_request_status['Executive Secretary'] === 'approved') {
                        $status = 'completed';
                    } elseif (!empty($appraisal->appraisal_request_status) && in_array('rejected', $appraisal->appraisal_request_status)) {
                        $status = 'rejected';
                    } elseif (!empty($appraisal->appraisal_request_status)) {
                        $status = 'submitted';
                    }
                @endphp
                <span class="status-badge status-{{ $status }}">
                    {{ ucfirst($status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Print Content -->
    <div class="print-content">
        
        <!-- Header with Logo -->
        <div class="text-center mb-4 avoid-page-break">
            <div class="d-flex align-items-center justify-content-center mb-3">
                <img src="{{ asset('assets/images/coat_of_arms.png') }}" alt="Coat of Arms" style="height: 60px;" class="me-3">
                <div class="text-center">
                    <h4 class="mb-1 fw-bold">REPUBLIC OF UGANDA</h4>
                    <h5 class="mb-1 text-primary">UGANDA NATIONAL COUNCIL FOR SCIENCE AND TECHNOLOGY</h5>
                    <p class="mb-0 small">STAFF PERFORMANCE APPRAISAL FORM</p>
                </div>
            </div>
            <hr class="border-2">
        </div>

        <!-- Employee Information Header -->
        <div class="row mb-4 avoid-page-break">
            <div class="col-md-6">
                <h6 class="text-primary mb-3"><u>EMPLOYEE INFORMATION</u></h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="fw-semibold">Full Name:</td>
                        <td>{{ $appraisal->employee->first_name ?? 'N/A' }} {{ $appraisal->employee->last_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Staff ID:</td>
                        <td>{{ $appraisal->employee->staff_id ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Position:</td>
                        <td>{{ $appraisal->employee->position->position_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Department:</td>
                        <td>{{ $appraisal->employee->department->department_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Date of Entry:</td>
                        <td>{{ $appraisal->employee->date_of_entry ? \Carbon\Carbon::parse($appraisal->employee->date_of_entry)->format('M d, Y') : 'N/A' }}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary mb-3"><u>APPRAISAL INFORMATION</u></h6>
                <table class="table table-borderless table-sm">
                    <tr>
                        <td class="fw-semibold">Review Type:</td>
                        <td>{{ ucwords(str_replace('_', ' ', $appraisal->review_type ?? 'N/A')) }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Review Period:</td>
                        <td>{{ $appraisal->appraisal_start_date?->format('M d, Y') ?? 'N/A' }} to {{ $appraisal->appraisal_end_date?->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Appraiser:</td>
                        <td>{{ $appraisal->appraiser->first_name ?? 'N/A' }} {{ $appraisal->appraiser->last_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="fw-semibold">Current Stage:</td>
                        <td>{{ $appraisal->current_stage ?? 'Staff' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Attachments Summary (if any) -->
        @if (!empty($appraisal->relevant_documents))
            <div class="attachment-summary no-print avoid-page-break">
                <h6 class="mb-3">
                    <i class="fas fa-paperclip me-2"></i>
                    Attachments ({{ count($appraisal->relevant_documents) }})
                    @if ($attachmentAccessInfo['can_access'])
                        <a href="{{ route('appraisals.attachments.download-all', $appraisal->appraisal_id) }}" class="btn btn-sm btn-outline-primary ms-2">
                            <i class="fas fa-download"></i> Download All
                        </a>
                    @endif
                </h6>
                
                @foreach ($appraisal->relevant_documents as $index => $document)
                    <div class="attachment-item">
                        <div class="flex-grow-1">
                            <i class="fas fa-file attachment-icon"></i>
                            {{ $document['title'] ?? 'Document ' . ($index + 1) }}
                        </div>
                        @if ($attachmentAccessInfo['can_access'])
                            <div>
                                <a href="{{ route('appraisals.attachment.view', ['appraisal' => $appraisal->appraisal_id, 'index' => $index]) }}" 
                                   class="btn btn-sm btn-outline-secondary me-1" target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('appraisals.attachment.download', ['appraisal' => $appraisal->appraisal_id, 'index' => $index]) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        @else
                            <small class="text-muted">
                                <i class="fas fa-lock me-1"></i>{{ $attachmentAccessInfo['reason'] }}
                            </small>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Include the main PDF content -->
        @include('appraisals.pdf-content', ['appraisal' => $appraisal, 'users' => $users])

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            if (e.key === 'Escape') {
                history.back();
            }
        });

        // Auto-adjust zoom for better preview (optional)
        document.addEventListener('DOMContentLoaded', function() {
            // Add any preview-specific JavaScript here
            console.log('Print preview loaded');
        });
    </script>
</body>
</html>
