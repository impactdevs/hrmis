<x-app-layout>
    <!-- Print Button -->
    <div class="position-fixed top-50 end-0 translate-middle-y d-flex align-items-center bg-white border border-primary rounded shadow p-2 d-print-none"
        style="z-index: 9999; cursor: pointer;" onclick="window.print();" title="Print this page">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="blue" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="bi bi-printer">
            <path d="M6 9V2h12v7" />
            <path d="M6 18h12a2 2 0 002-2v-5H4v5a2 2 0 002 2zm0 0v2h12v-2" />
        </svg>
    </div>

    <!-- Main Content -->
    <div class="container py-5" style="max-width: 1536px;">
        <div class="bg-white shadow rounded p-4 p-md-5">
            <div class="d-flex justify-content-between align-items-center mb-4 mb-md-5">
                <h2 class="h2 fw-bold text-dark">Whistleblowing Report Details</h2>
                <a href="{{ route('whistleblowing.index') }}" 
                   class="link-primary text-decoration-none d-print-none"
                   onmouseover="this.style.textDecoration='underline'"
                   onmouseout="this.style.textDecoration='none'">
                   &larr; Back to list
                </a>
            </div>
            
            <div class="row g-4">
                <!-- Tracking ID -->
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Tracking ID:</span>
                    <span class="font-monospace">{{ $report->tracking_id }}</span>
                </div>
                
                <!-- Submission Type -->
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Submission Type:</span>
                    <span>{{ $report->submission_type }}</span>
                </div>
                
                <!-- Description -->
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Description:</span>
                    <div class="bg-light rounded p-3 mt-1 text-break" style="white-space: pre-line;">
                        {{ $report->description }}
                    </div>
                </div>
                
                <!-- Individuals Involved -->
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Individuals Involved:</span>
                    <div class="bg-light rounded p-3 mt-1 text-break" style="white-space: pre-line;">
                        {{ $report->individuals_involved }}
                    </div>
                </div>
                
                <!-- Evidence Details -->
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Evidence Details:</span>
                    <div class="bg-light rounded p-3 mt-1 text-break" style="white-space: pre-line;">
                        {{ $report->evidence_details }}
                    </div>
                </div>
                
                <!-- Evidence File -->
                @if ($report->evidence_file_path)
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Evidence File:</span>
                    <a href="{{ Storage::disk('public')->url($report->evidence_file_path) }}" target="_blank"
                        class="d-block text-primary mt-1">
                        Download Evidence
                    </a>
                </div>
                @endif
                
                <!-- Reported Before -->
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Has this issue been reported before?</span>
                    <span>{{ $report->reported_before }}</span>
                </div>
                
                <!-- Reported Details -->
                @if ($report->reported_details)
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Reported Details:</span>
                    <div class="bg-light rounded p-3 mt-1 text-break" style="white-space: pre-line;">
                        {{ $report->reported_details }}
                    </div>
                </div>
                @endif
                
                <!-- Suggested Resolution -->
                @if ($report->suggested_resolution)
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Suggested Resolution:</span>
                    <div class="bg-light rounded p-3 mt-1 text-break" style="white-space: pre-line;">
                        {{ $report->suggested_resolution }}
                    </div>
                </div>
                @endif
                
                <!-- Date Submitted -->
                <div class="col-12">
                    <span class="fw-semibold text-secondary">Date Submitted:</span>
                    <span>{{ $report->created_at->format('Y-m-d H:i') }}</span>
                </div>
                
                <!-- IP Address -->
                <div class="col-12">
                    <span class="fw-semibold text-secondary">IP Address:</span>
                    <span>{{ $report->ip_address }}</span>
                </div>
                
                <div class="col-12">
                    <span class="fw-semibold text-secondary">User Agent:</span>
                    <span class="d-inline-block text-break" style="word-break: break-all;">
                        {{ $report->user_agent }}
                    </span>
                </div>
                
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .container, .container * {
                visibility: visible;
            }
            .container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                max-width: 100% !important;
                padding: 0 !important;
            }
            .bg-light {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .shadow, .border {
                box-shadow: none !important;
                border: none !important;
            }
            .rounded {
                border-radius: 0 !important;
            }
        }
    </style>
</x-app-layout>