<x-app-layout>
    @php
        $rejectedEntry = collect($appraisal->appraisal_request_status)
            ->filter(fn($status) => $status === 'rejected')
            ->keys()
            ->first(); // Get the first person/role who rejected

        $rejectionReason = $appraisal->rejection_reason ?? 'No reason provided.';
    @endphp
    <div class="gap-2 p-2 bg-white border rounded shadow position-fixed top-50 end-0 translate-middle-y d-flex align-items-center border-primary no-print"
        style="z-index: 9999; cursor: pointer;" role="button" onclick="window.print();" title="Print this page">

        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="blue" stroke-width="2"
            stroke-linecap="round" stroke-linejoin="round" class="bi bi-printer">
            <path d="M6 9V2h12v7" />
            <path d="M6 18h12a2 2 0 002-2v-5H4v5a2 2 0 002 2zm0 0v2h12v-2" />
        </svg>

    </div>

    <div class="top-0 p-3 toast-container position-fixed start-50 translate-middle-x text-bg-success approval"
        role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                class="bi bi-x-octagon-fill text-danger" viewBox="0 0 16 16">
                <path
                    d="M11.46.146A.5.5 0 0 1 12 .5v3.793a.5.5 0 0 1-.146.354l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 0 1 0-.708l7-7A.5.5 0 0 1 8.207.146L11.46.146z" />
            </svg>
            <strong class="me-auto" id="totalScore"></strong>
            {{-- <button type="button" class="btn-close no-print" data-bs-dismiss="toast" aria-label="Close"></button> --}}
        </div>

    </div>


    @if ($rejectedEntry)
        <!-- Toast for rejection -->
        <div class="top-0 p-3 toast-container position-fixed start-50 translate-middle-x text-bg-danger approval"
            role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                    class="bi bi-x-octagon-fill text-danger" viewBox="0 0 16 16">
                    <path
                        d="M11.46.146A.5.5 0 0 1 12 .5v3.793a.5.5 0 0 1-.146.354l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 0 1 0-.708l7-7A.5.5 0 0 1 8.207.146L11.46.146z" />
                </svg>
                <strong class="me-auto">Rejected by {{ $rejectedEntry }}</strong>
                <button type="button" class="btn-close no-print" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                <strong>Reason:</strong> {{ $rejectionReason }}
            </div>
        </div>
    @endif


    <form action="{{ route('uncst-appraisals.update', ['uncst_appraisal' => $appraisal->appraisal_id]) }}"
        method="post" class="m-2" id="appraisalForm">
        @csrf
        @method('PUT')
        <div class="entire-form">

            <!-- IMPORTANT NOTES -->
            <div class="p-3 mb-4 border shadow card border-primary">
                <div class="p-0 mb-2 bg-white border-0 card-header">
                    <legend class="w-auto text-primary d-flex align-items-center">
                        <i class="fas fa-exclamation-triangle fa-lg me-2"></i>
                        <span class="mb-0 h5">IMPORTANT NOTES:</span>
                    </legend>
                    <small class="text-muted ms-4">Please read carefully before proceeding</small>
                </div>
                <div class="p-0 card-body">
                    <ol type="I" style="list-style-type: upper-roman; padding-left: 20px;">
                        <li class="mb-2">
                            Completing Staff Performance Assessment Forms is mandatory for all UNCST members of staff,
                            including those who are on probation or temporary terms of service. Any employee on leave or
                            absent for any reason should have a review completed within 15 days of return to work.
                        </li>
                        <li class="mb-2">
                            The Appraisal process offers an opportunity to the appraiser and appraisee to discuss and
                            obtain
                            feedback on performance, therefore participatory approach to the appraisal process,
                            consistence
                            and objectivity are very important aspects of this exercise.
                        </li>
                        <li class="mb-2">
                            Oral interviews and appearance before a UNCST Management Assessment Panel may be done (under
                            Section 4) when deemed necessary and with the approval of the Executive Secretary before
                            making
                            his/her overall assessment and final comments.
                        </li>
                        <li class="mb-0">
                            In cases where information to be filled in form does not fit in the space provided, the back
                            face of the same sheet may be used with an indication of a “PTO” where applicable.
                        </li>
                    </ol>
                </div>
            </div>


            <!-- TYPE OF REVIEW -->
            <div class="mb-4 shadow card">
                <div class="text-white card-header bg-secondary">
                    <h4 class="mb-0">TYPE OF REVIEW</h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="p-3 rounded form-section bg-light">
                                <h5 class="mb-3 text-muted">Review Type</h5>
                                <div class="flex-wrap gap-4 d-flex">
                                    <div class="form-group">
                                        <label for="review_type">Select the type of review</label>

                                        @php
                                            $options = [
                                                'confirmation' => 'Confirmation',
                                                'end_of_contract' => 'End of Contract',
                                                'mid_financial_year' => 'End of Financial Year',
                                                'other' => 'Other',
                                            ];
                                            $selected = old('review_type', $appraisal->review_type ?? '');
                                            $otherValue = old('review_type_other', $appraisal->review_type_other ?? '');
                                        @endphp

                                        @foreach ($options as $value => $text)
                                            <div class="form-check">
                                                <input type="radio" name="review_type"
                                                    id="review_type_{{ $value }}" value="{{ $value }}"
                                                    class="form-check-input @error('review_type') is-invalid @enderror"
                                                    {{ $selected === $value ? 'checked' : '' }}
                                                    @if ($value === 'end_of_contract') 
                                                        onclick="document.getElementById('expireContractDetails').classList.remove('d-none');document.getElementById('review_type_other_input').classList.add('d-none');"
                                                    @elseif ($value === 'other')
                                                        onclick="document.getElementById('expireContractDetails').classList.add('d-none');document.getElementById('review_type_other_input').classList.remove('d-none');"
                                                    @else 
                                                        onclick="document.getElementById('expireContractDetails').classList.add('d-none');document.getElementById('review_type_other_input').classList.add('d-none');"
                                                    @endif
                                                >
                                                <label class="form-check-label" for="review_type_{{ $value }}">
                                                    {{ $text }}
                                                </label>
                                            </div>
                                        @endforeach

                                        <div id="review_type_other_input" class="mt-2 {{ $selected === 'other' ? '' : 'd-none' }}">
                                            <input type="text" name="review_type_other" class="form-control"
                                                placeholder="Please specify other review type"
                                                value="{{ $otherValue }}">
                                        </div>

                                        @error('review_type')
                                            <div class="invalid-feedback d-block">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <script>
                                    // Ensure correct visibility on page load
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var selected = document.querySelector('input[name="review_type"]:checked');
                                        var otherInput = document.getElementById('review_type_other_input');
                                        if (selected && selected.value === 'other') {
                                            otherInput.classList.remove('d-none');
                                        } else {
                                            otherInput.classList.add('d-none');
                                        }
                                    });
                                </script>
                            </div>

                            <div id="expireContractDetails"
                                class="{{ $selected === 'end_of_contract' ? '' : 'd-none' }}">
                                {{-- contract --}}
                                @if (isset($expiredContract))
                                    <div class="alert alert-info mt-3">
                                        <strong>Contract being Appraised Details</strong>
                                        <ul class="mb-0">
                                            <li>
                                                <strong>Contract Start Date:</strong>
                                                {{ isset($expiredContract) && $expiredContract->start_date ? $expiredContract->start_date->toDateString() : 'N/A' }}
                                            </li>
                                            <li>
                                                <strong>Contract End Date:</strong>
                                                {{ isset($expiredContract) && $expiredContract->end_date ? $expiredContract->end_date->toDateString() : 'N/A' }}
                                            </li>
                                            <li>
                                                <strong>Contract Description:</strong>
                                                {{ $expiredContract->description ?? 'N/A' }}
                                            </li>
                                            <li>
                                                <strong>Contract Documents:</strong>
                                                @if (!empty($expiredContract->contract_documents))
                                                    <div class="flex-wrap gap-2 d-flex">
                                                        @foreach ($expiredContract->contract_documents as $attachment)
                                                            <a href="{{ asset('storage/' . $attachment['proof']) }}"
                                                                target="_blank"
                                                                class="btn btn-sm btn-outline-secondary d-flex align-items-center">
                                                                @if (Str::endsWith($attachment['proof'], ['.pdf']))
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
                                            </li>
                                        </ul>
                                        {{-- Add hidden contract_id input if end_of_contract --}}
                                        @if ($selected === 'end_of_contract' && isset($expiredContract))
                                            <input type="hidden" name="contract_id" id="contract_id_input"
                                                value="{{ $expiredContract->id }}">
                                        @endif
                                    </div>
                                @else
                                    <div class="alert alert-warning mt-3">
                                        No contract expiry details available.
                                    </div>
                                @endif
                            </div>
                        </div>

                        @push('scripts')
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    // Show/hide contract details on page load if needed
                                    var selected = document.querySelector('input[name="review_type"]:checked');
                                    var expireDiv = document.getElementById('expireContractDetails');
                                    if (selected && selected.value === 'end_of_contract') {
                                        expireDiv.classList.remove('d-none');
                                    } else {
                                        expireDiv.classList.add('d-none');
                                    }

                                    // Listen for changes
                                    document.querySelectorAll('input[name="review_type"]').forEach(function(radio) {
                                        radio.addEventListener('change', function() {
                                            if (this.value === 'end_of_contract') {
                                                expireDiv.classList.remove('d-none');
                                            } else {
                                                expireDiv.classList.add('d-none');

                                            }
                                        });
                                    });
                                });
                            </script>
                        @endpush

                        <div class="col-lg-6">
                            <div class="p-3 rounded form-section bg-light">
                                <h5 class="mb-3 text-muted">Review Period</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <x-forms.input name="appraisal_start_date" label="Start Date" type="date"
                                            id="appraisal_start_date"
                                            value="{{ old('appraisal_start_date', isset($appraisal) && $appraisal->appraisal_start_date ? $appraisal->appraisal_start_date->toDateString() : '') }}" />
                                    </div>
                                    <div class="col-md-6">
                                        <x-forms.input name="appraisal_end_date" label="End Date" type="date"
                                            id="appraisal_end_date"
                                            value="{{ old('appraisal_end_date', isset($appraisal) && $appraisal->appraisal_end_date ? $appraisal->appraisal_end_date->toDateString() : '') }}" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- APPRAISAL INFORMATION -->
            <div class="mb-4 shadow card appraisal-information">
                <div class="text-white card-header bg-secondary">
                    <h4 class="mb-0">APPRAISAL INFORMATION</h4>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-lg-6">
                            <div class="p-3 rounded form-section bg-light">
                                <div class="flex-wrap gap-4 d-flex">
                                    <label for="supervisor">Appraiser</label>
                                    {{-- put a readonly input with the head of division value --}}
                                    <select class="employees form-control" name="appraiser_id" id="appraiser_id"
                                        data-placeholder="Choose the Appraiser" required>
                                        @foreach ($users as $user)
                                            <option value=""></option>
                                            <option value="{{ optional($user->employee)->employee_id }}"
                                                {{ $user->employee && $user->employee->employee_id == $appraisal->appraiser_id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="p-3 rounded form-section bg-light">
                                <h5 class="mb-3 text-muted">Your Personal Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <p> FULL NAME:
                                            {{ auth()->user()->employee->first_name . ' ' . auth()->user()->employee->last_name }}
                                        </p>
                                    </div>
                                    <div class="col-md-12">
                                        <p> POSITION:
                                            {{ optional(auth()->user()->employee->position)->position_name }}
                                        </p>
                                    </div>

                                    <div class="col-md-12">
                                        <p> DIVISION:
                                            {{ optional(auth()->user()->employee->department)->department_name }}
                                        </p>
                                    </div>
                                    <div class="col-md-12">
                                        <p> DATE OF 1ST APPOINTMENT:
                                            {{ \Carbon\Carbon::parse(auth()->user()->employee->date_of_entry)->toFormattedDateString() }}
                                        </p>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PERFORMANCE EVALUATION -->
            <div class="mb-4 shadow card">
                <div class="text-white card-header bg-info">
                    <h4 class="mb-0">Performance Evaluation</h4>
                </div>
                <div class="card-body">
                    <p>The following ratings should be used to ensure consistency on overall ratings: (provide
                        supporting
                        comments to justify ratings of Excellent/outstanding 80% – 100%, Very good 70% - 79%,
                        Satisfactory
                        60% - 69%, Average 50% - 59%, Unsatisfactory 0% - 49%.)
                        The overall total Score for the evaluation is 100% i.e., 60% - Key result areas and 40% for
                        personal
                        attributes.
                    </p>
                    <div class="table-responsive">
                        <table class="table mb-0 align-middle table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 15%">Rating</th>
                                    <th style="width: 60%">Description</th>
                                    <th style="width: 10%">Score</th>
                                    <th style="width: 15%">Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="text-white bg-success">Excellent/Outstanding</td>
                                    <td>Consistently exceeds work expectations and job requirements. Employee has
                                        exceeded
                                        all targets and has consistently produced outputs/results of excellent quality.
                                    </td>
                                    <td>5</td>
                                    <td>80–100%</td>
                                </tr>
                                <tr>
                                    <td class="text-white bg-primary">Very Good</td>
                                    <td>Consistently meets work expectations and job requirements. Employee achieved all
                                        planned outputs, and the quality of work overall was very good.</td>
                                    <td>4</td>
                                    <td>70–79%</td>
                                </tr>
                                <tr>
                                    <td class="text-white bg-info">Satisfactory</td>
                                    <td>Performance consistently meets most work expectations and job requirements.
                                        Achieved
                                        most but not all of the agreed outputs, with no supporting rationale for
                                        inability
                                        to meet all commitments.</td>
                                    <td>3</td>
                                    <td>60–69%</td>
                                </tr>
                                <tr>
                                    <td class="bg-warning text-dark">Average</td>
                                    <td>Does not consistently meet work expectations and requirements but achieved
                                        minimal
                                        outputs compared to planned outputs, with no supporting rationale for inability
                                        to
                                        meet commitments.</td>
                                    <td>2</td>
                                    <td>50–59%</td>
                                </tr>
                                <tr>
                                    <td class="text-white bg-danger">Unsatisfactory</td>
                                    <td>Consistently below expectations and job requirements. Employee has not achieved
                                        most
                                        of the planned outputs, with no supporting rationale for not achieving them, and
                                        has
                                        demonstrated inability or unwillingness to improve.</td>
                                    <td>1</td>
                                    <td>0–49%</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <!-- KEY DUTIES SECTION -->
            <div class="mb-4 shadow card">
                <div class="text-white card-header bg-primary">
                    <h4 class="mb-0">SECTION 1</h4>
                    <small class="fw-light">(To be completed by staff under review)</small>
                </div>
                <div class="card-body form-section">
                    <div class="row g-4">
                        <div class="col-12">

                            <div class="mt-4 col-12">
                                <p class="fw-bold">a. Job Compatibility</p>
                                <x-forms.radio name="job_compatibility" :isDisabled="!$appraisal->is_appraisee"
                                    label="Is the job and tasks performed compatible with your qualifications and experience?"
                                    value="{{ $appraisal->job_compatibility ?? '' }}" id="job_compatibility"
                                    :options="['yes' => 'Yes', 'no' => 'No']" :selected="$appraisal->job_compatibility ?? ''" />
                                <x-forms.text-area name="if_no_job_compatibility" :isDisabled="!$appraisal->is_appraisee"
                                    label="If No, explain:" id="if_no_job_compatibility" :value="old(
                                        'if_no_job_compatibility',
                                        $appraisal->if_no_job_compatibility ?? '',
                                    )" />
                            </div>

                            <div class="mt-4 col-12">
                                <p class="fw-bold">b. Challenges</p>
                                <x-forms.text-area name="unanticipated_constraints" :isDisabled="!$appraisal->is_appraisee"
                                    label="Briefly state unanticipated constraints/problems that you encountered and how they affected the achievements of the objectives."
                                    id="unanticipated_constraints" :value="old(
                                        'unanticipated_constraints',
                                        $appraisal->unanticipated_constraints ?? '',
                                    )" />
                            </div>

                            <div class="mt-4 col-12">
                                <p class="fw-bold">c. Personal Initiatives</p>
                                <x-forms.text-area name="personal_initiatives" :isDisabled="!$appraisal->is_appraisee"
                                    label="Outline personal initiatives and any other factors that you think contributed to your achievements and successes."
                                    id="personal_initiatives" :value="old('personal_initiatives', $appraisal->personal_initiatives ?? '')" />
                            </div>

                            <div class="mt-4 col-12">
                                <p class="fw-bold">d. Training Support Needs</p>
                                <x-forms.text-area name="training_support_needs" :isDisabled="!$appraisal->is_appraisee"
                                    label="Indicate the nature of training support you may need to effectively perform your duties. Training support should be consistent with the job requirements and applicable to UNCST policies and regulations."
                                    id="training_support_needs" :value="old('training_support_needs', $appraisal->training_support_needs ?? '')" />
                            </div>


                        </div>
                    </div>
                </div>
            </div>


            <!-- PERSONAL ATTRIBUTES SECTION -->
            <div class="mb-4 shadow card">
                <div class="text-white card-header bg-primary">
                    <h4 class="mb-0">SECTION 2</h4>
                    <small class="fw-light">(To be completed by Staff Under Review (Appraisee) and Supervisor
                        (Appraiser))</small>
                    <p>The Appraiser should take into consideration the Appraisee’s job description and the actual
                        activities
                        performed and outputs produced, constraints encountered as described by the Appraisee in Section
                        1, as
                        well as any other relevant information</p>
                </div>
                <div class="card-body">
                    <div class="mt-4 col-12">
                        <p class="fw-bold">KEY DUTIES AND RESPONSIBILITIES</p>
                        <p>List and rate the major planned activities during the appraisal period, including
                            outputs/results attained.
                            You may include activities outside your job description but falling in line with your
                            duties.
                        </p>
                        <div class="overflow-x-auto bg-white rounded-lg shadow-lg">
                            <table class="min-w-full text-base border border-gray-200" id="key-duties-table">
                                <thead class="text-sm text-gray-700 uppercase bg-blue-100 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-3 border border-gray-200 w-12">No.</th>
                                        <th class="px-6 py-3 border border-gray-200">Planned Tasks (Target)</th>
                                        <th class="px-6 py-3 border border-gray-200">Output / Results</th>
                                        <th class="px-6 py-3 border border-gray-200 w-32">Supervisee (/6)</th>
                                        <th class="px-6 py-3 border border-gray-200 w-32">Supervisor (/6)</th>
                                        <th class="px-6 py-3 border border-gray-200 w-40">Agreed Score (/6)</th>
                                        <th class="px-6 py-3 border border-gray-200 w-10 no-print"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $rowCount = max(
                                            4,
                                            isset($appraisal->appraisal_period_rate)
                                                ? count($appraisal->appraisal_period_rate)
                                                : 0,
                                        );
                                    @endphp
                                    @for ($i = 1; $i <= $rowCount; $i++)
                                        @php
                                            $plannedActivity =
                                                $appraisal->appraisal_period_rate[$i - 1]['planned_activity'] ?? '';
                                            $outputResults =
                                                $appraisal->appraisal_period_rate[$i - 1]['output_results'] ?? '';
                                            $superviseeScore =
                                                $appraisal->appraisal_period_rate[$i - 1]['supervisee_score'] ?? 0;
                                            $supervisorScore =
                                                $appraisal->appraisal_period_rate[$i - 1]['supervisor_score'] ?? 0;
                                            $agreedScore =
                                                $appraisal->appraisal_period_rate[$i - 1]['agreed_score'] ?? 0;
                                            $supervisorComment =
                                                $appraisal->appraisal_period_rate[$i - 1]['supervisor_comment'] ?? '';
                                        @endphp

                                        <tr class="hover:bg-blue-50 transition-colors"
                                            data-row="{{ $i }}">
                                            <td class="px-6 py-4 border border-gray-200 font-bold text-gray-900 align-top"
                                                rowspan="2">
                                                <span class="row-number">{{ $i }}.</span>
                                            </td>

                                            {{-- Planned Activity --}}
                                            <td class="px-6 py-2 border border-gray-200">
                                                <div class="editable-cell p-2 rounded {{ empty($plannedActivity) ? 'text-muted' : '' }}"
                                                    contenteditable="{{ $appraisal->is_appraisee ? 'true' : 'false' }}"
                                                    data-placeholder="Enter task" oninput="updateHiddenInput(this)"
                                                    @unless ($appraisal->is_appraisee)
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top"
                                                        title="Editing is disabled for your role"
                                                        onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                    @endunless>
                                                    {{ $plannedActivity ?: 'Enter task' }}
                                                </div>
                                                <input type="hidden"
                                                    name="appraisal_period_rate[{{ $i - 1 }}][planned_activity]"
                                                    value="{{ $plannedActivity }}">
                                            </td>

                                            {{-- Output Results --}}
                                            <td class="px-6 py-2 border border-gray-200">
                                                <div class="editable-cell p-2 rounded {{ empty($outputResults) ? 'text-muted' : '' }}"
                                                    contenteditable="{{ $appraisal->is_appraisee ? 'true' : 'false' }}"
                                                    data-placeholder="Enter result" oninput="updateHiddenInput(this)"
                                                    @unless ($appraisal->is_appraisee)
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top"
                                                        title="Editing is disabled for your role"
                                                        onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                    @endunless>
                                                    {{ $outputResults ?: 'Enter result' }}
                                                </div>
                                                <input type="hidden"
                                                    name="appraisal_period_rate[{{ $i - 1 }}][output_results]"
                                                    value="{{ $outputResults }}">
                                            </td>

                                            {{-- Supervisee Score --}}
                                            <td class="px-6 py-2 border border-gray-200">
                                                <div class="score-cell"
                                                    contenteditable="{{ $appraisal->is_appraisee ? 'true' : 'false' }}"
                                                    data-type="score" oninput="updateScoreInput(this)"
                                                    @unless ($appraisal->is_appraisee)
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top"
                                                        title="Editing is disabled for your role"
                                                        onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                    @endunless>
                                                    {{ $superviseeScore }}
                                                </div>
                                                <input type="hidden"
                                                    name="appraisal_period_rate[{{ $i - 1 }}][supervisee_score]"
                                                    value="{{ $superviseeScore }}">
                                            </td>

                                            {{-- Supervisor Score --}}
                                            <td class="px-6 py-2 border border-gray-200">
                                                <div class="score-cell"
                                                    contenteditable="{{ $appraisal->is_appraisor ? 'true' : 'false' }}"
                                                    data-type="score" oninput="updateScoreInput(this)"
                                                    @unless ($appraisal->is_appraisor)
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top"
                                                        title="Editing is disabled for your role"
                                                        onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                    @endunless>
                                                    {{ $supervisorScore }}
                                                </div>
                                                <input type="hidden"
                                                    name="appraisal_period_rate[{{ $i - 1 }}][supervisor_score]"
                                                    value="{{ $supervisorScore }}">
                                            </td>

                                            {{-- Agreed Score --}}
                                            <td class="px-6 py-2 border border-gray-200">
                                                <input type="number"
                                                    name="appraisal_period_rate[{{ $i - 1 }}][agreed_score]"
                                                    class="form-control form-control-sm agreed-score-input"
                                                    min="0" max="6" step="0.5"
                                                    value="{{ $agreedScore }}"
                                                    @if ($appraisal->is_appraisee) readonly @endif
                                                    oninput="updateOverallAverage()">
                                            </td>
                                            <td
                                                class="px-2 py-2 border border-gray-200 align-middle text-center no-print">
                                                <button type="button" class="btn btn-sm btn-danger remove-duty-row"
                                                    title="Remove row" style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>

                                        {{-- Supervisor Comment --}}
                                        <tr class="hover:bg-blue-50 transition-colors">
                                            <td class="px-6 py-2 border border-gray-200 bg-gray-50 italic text-gray-600"
                                                colspan="6">
                                                <div class="editable-cell p-2 rounded {{ empty($supervisorComment) ? 'text-muted' : '' }}"
                                                    contenteditable="{{ $appraisal->is_appraisor ? 'true' : 'false' }}"
                                                    data-placeholder="Supervisor's comment..."
                                                    oninput="updateHiddenInput(this)"
                                                    @unless ($appraisal->is_appraisor)
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top"
                                                        title="Editing is disabled for your role"
                                                        onclick="bootstrap.Tooltip.getOrCreateInstance(this).show()"
                                                    @endunless>
                                                    {{ $supervisorComment ?: "Supervisor's comment..." }}
                                                </div>
                                                <input type="hidden"
                                                    name="appraisal_period_rate[{{ $i - 1 }}][supervisor_comment]"
                                                    value="{{ $supervisorComment }}">
                                            </td>
                                        </tr>
                                    @endfor


                                
                                    <!-- Total Row -->
                                    <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 font-semibold">
                                        <td class="px-6 py-4 border border-gray-200" colspan="7">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-4">
                                                    <div class="text-lg font-bold text-blue-700">
                                                        Overall Average:
                                                        <span id="overallAverage" class="text-2xl ml-2">0</span>%
                                                    </div>
                                                    
                                                    <div
                                                        class="relative w-48 h-3 bg-gray-200 rounded-full overflow-hidden">
                                                        <div id="averageProgress"
                                                            class="absolute left-0 top-0 h-full bg-gradient-to-r from-blue-400 to-indigo-600 transition-all duration-500"
                                                            style="width: 0%">
                                                        </div>
                                                    </div>

                                                    <div class="mt-2 no-print">
                                               
                                            </div>
                                                </div>
                                                <div id="performanceStatus" class="text-sm font-medium text-gray-600">
                                                    Enter scores to view performance
                                                </div>

                                                 <button type="button" class="btn btn-outline-primary btn-sm"
                                                    id="add-duty-row">
                                                    <i class="fas fa-plus"></i> Add Row
                                                </button>
                                            </div>
                                            
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                            @push('scripts')
                                <script>
                                    $(function() {
                                        // Add row button for Key Duties table
                                        $('#add-duty-row').on('click', function() {
                                            let $table = $('#key-duties-table');
                                            let $tbody = $table.find('tbody');
                                            // Find last data-row index
                                            let rowCount = $tbody.find('tr[data-row]').length;
                                            let i = rowCount + 1;
                                            let canAppraisee = @json($appraisal->is_appraisee);
                                            let canAppraisor = @json($appraisal->is_appraisor);

                                            let newRow = `
                                    <tr class="hover:bg-blue-50 transition-colors" data-row="${i}">
                                        <td class="px-6 py-4 border border-gray-200 font-bold text-gray-900 align-top" rowspan="2">
                                            <span class="row-number">${i}.</span>
                                        </td>
                                        <td class="px-6 py-2 border border-gray-200">
                                            <div class="editable-cell p-2 rounded text-muted"
                                                contenteditable="${canAppraisee ? 'true' : 'false'}"
                                                data-placeholder="Enter task"
                                                oninput="updateHiddenInput(this)"
                                                onfocus="if(this.classList.contains('text-muted')){this.textContent='';this.classList.remove('text-muted');}">
                                                Enter task
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[${i - 1}][planned_activity]"
                                                value="">
                                        </td>
                                        <td class="px-6 py-2 border border-gray-200">
                                            <div class="editable-cell p-2 rounded text-muted"
                                                contenteditable="${canAppraisee ? 'true' : 'false'}"
                                                data-placeholder="Enter result"
                                                oninput="updateHiddenInput(this)"
                                                onfocus="if(this.classList.contains('text-muted')){this.textContent='';this.classList.remove('text-muted');}">
                                                Enter result
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[${i - 1}][output_results]"
                                                value="">
                                        </td>
                                        <td class="px-6 py-2 border border-gray-200">
                                            <div class="score-cell"
                                                contenteditable="${canAppraisee ? 'true' : 'false'}"
                                                data-type="score" oninput="updateScoreInput(this)">
                                                0
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[${i - 1}][supervisee_score]"
                                                value="0">
                                        </td>
                                        <td class="px-6 py-2 border border-gray-200">
                                            <div class="score-cell"
                                                contenteditable="${canAppraisor ? 'true' : 'false'}"
                                                data-type="score" oninput="updateScoreInput(this)">
                                                0
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[${i - 1}][supervisor_score]"
                                                value="0">
                                        </td>
                                        <td class="px-6 py-2 border border-gray-200">
                                            <input type="number"
                                                name="appraisal_period_rate[${i - 1}][agreed_score]"
                                                class="form-control form-control-sm agreed-score-input"
                                                min="0" max="6" step="0.5"
                                                value="0"
                                                ${canAppraisee ? 'readonly' : ''}
                                                oninput="updateOverallAverage()">
                                        </td>
                                        <td class="px-2 py-2 border border-gray-200 align-middle text-center no-print">
                                            <button type="button" class="btn btn-sm btn-danger remove-duty-row" title="Remove row">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <tr class="hover:bg-blue-50 transition-colors">
                                        <td class="px-6 py-2 border border-gray-200 bg-gray-50 italic text-gray-600" colspan="6">
                                            <div class="editable-cell p-2 rounded text-muted"
                                                contenteditable="${canAppraisor ? 'true' : 'false'}"
                                                data-placeholder="Supervisor's comment..."
                                                oninput="updateHiddenInput(this)"
                                                onfocus="if(this.classList.contains('text-muted')){this.textContent='';this.classList.remove('text-muted');}">
                                                Supervisor's comment...
                                            </div>
                                            <input type="hidden"
                                                name="appraisal_period_rate[${i - 1}][supervisor_comment]"
                                                value="">
                                        </td>
                                    </tr>
                                    `;
                                            // Insert before the total row (last tr)
                                            $tbody.find('tr').last().before(newRow);
                                            updateDutyRowNumbers();
                                            showDutyRemoveButtons();
                                        });

                                        // Remove row button for Key Duties table
                                        $('#key-duties-table').on('click', '.remove-duty-row', function() {
                                            let $row = $(this).closest('tr');
                                            // Remove both the main row and the comment row
                                            let $next = $row.next();
                                            if ($next && !$next.attr('data-row')) {
                                                $next.remove();
                                            }
                                            $row.remove();
                                            updateDutyRowNumbers();
                                            updateDutyInputNames();
                                            showDutyRemoveButtons();
                                        });

                                        function updateDutyRowNumbers() {
                                            $('#key-duties-table tbody tr[data-row]').each(function(i, tr) {
                                                $(tr).find('.row-number').text((i + 1) + '.');
                                            });
                                        }

                                        function updateDutyInputNames() {
                                            let idx = 0;
                                            $('#key-duties-table tbody tr[data-row]').each(function() {
                                                // Main row
                                                $(this).find('input[name^="appraisal_period_rate"]').each(function() {
                                                    let name = $(this).attr('name');
                                                    let field = name.match(/\]\[(.*?)\]$/)[1];
                                                    $(this).attr('name', `appraisal_period_rate[${idx}][${field}]`);
                                                });
                                                // Comment row (next)
                                                let $commentRow = $(this).next();
                                                if ($commentRow && $commentRow.find('input[name^="appraisal_period_rate"]').length) {
                                                    $commentRow.find('input[name^="appraisal_period_rate"]').attr('name',
                                                        `appraisal_period_rate[${idx}][supervisor_comment]`);
                                                }
                                                idx++;
                                            });
                                        }

                                        function showDutyRemoveButtons() {
                                            let $rows = $('#key-duties-table tbody tr[data-row]');
                                            if ($rows.length > 1) {
                                                $rows.each(function() {
                                                    $(this).find('.remove-duty-row').show();
                                                });
                                            } else {
                                                $rows.each(function() {
                                                    $(this).find('.remove-duty-row').hide();
                                                });
                                            }
                                        }
                                        showDutyRemoveButtons();
                                    });
                                </script>
                            @endpush
                        </div>

                    </div>
                    <div class="table-responsive">
                        <h6 class="h1">ASSESSMENT OF PERSONAL ATTRIBUTES</h6>
                        <p>The Appraisee should score her/his attributes in relation to performance.
                        </p>
                        <table class="table align-middle table-striped table-hover table-borderless table-primary"
                            id="personal-attributes">
                            <caption>Rating: 80-100 – Excellent 70-79 – Very Good 60-69 - Satisfactory 50-59 – Average
                                0-49
                                - Unsatisfactory </caption>
                            <thead class="table-light">
                                <tr>
                                    <th>Measurable Indicators/Personal Attributes</th>
                                    <th>Maximum Score</th>
                                    <th>Appraisee’s Score</th>
                                    <th>Appraiser’s Score</th>
                                    <th>Agreed Score</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Technical and Professional Knowledge</span>
                                        <p class="mb-0 text-muted small">Exhibits basic technical and professional
                                            knowledge of the assigned tasks</p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[technical_knowledge][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            value="{{ $appraisal->personal_attributes_assessment['technical_knowledge']['appraisee_score'] ?? '' }}"
                                            max="4"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[technical_knowledge][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            value="{{ $appraisal->personal_attributes_assessment['technical_knowledge']['appraiser_score'] ?? '' }}"
                                            max="4"></td>
                                    <td class="text-center"><input type="number"
                                            name="personal_attributes_assessment[technical_knowledge][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['technical_knowledge']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4"></td>
                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Commitment to Mission</span>
                                        <p class="mb-0 text-muted small">Understands and exhibits a sense of working
                                            for
                                            the UNCST & at all times projects the interest of the Organization as a
                                            priority.</p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[commitment][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['commitment']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4" @if (!$appraisal->is_appraisee) readonly @endif>
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[commitment][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['commitment']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[commitment][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['commitment']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Team Work</span>
                                        <p class="mb-0 text-muted small">Is reliable, cooperates with other staff, is
                                            willing to share information, resources and knowledge with others. Exhibits
                                            sensitivity to deadlines and to the time constraints of other
                                            staff/departments.
                                        </p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[team_work][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['team_work']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[team_work][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['team_work']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[team_work][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['team_work']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Productivity and Organizational Skills</span>
                                        <p class="mb-0 text-muted small">Makes efficient use of time, fulfilling
                                            responsibilities and completing tasks by deadlines. Demonstrates
                                            responsiveness
                                            and structured approach to tasks.</p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[productivity][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['productivity']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[productivity][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['productivity']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[productivity][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['productivity']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Integrity</span>
                                        <p class="mb-0 text-muted small">Is honest and trustworthy, follows procedures,
                                            takes responsibility, and respects others. Deals with conflict
                                            professionally
                                            and values diversity.</p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[integrity][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['integrity']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[integrity][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['integrity']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[integrity][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['integrity']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Flexibility and Adaptability</span>
                                        <p class="mb-0 text-muted small">Willing to take on new job responsibilities or
                                            to
                                            assist the Organization through peak workloads. Able to accept the changing
                                            needs of the organization with enthusiasm.</p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[flexibility][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['flexibility']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[flexibility][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['flexibility']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[flexibility][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['flexibility']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Attendance and Punctuality</span>
                                        <p class="mb-0 text-muted small">Maintains agreed upon work schedule and does
                                            not
                                            abuse leave/sick time. Maintains agreed upon work hours and does not abuse
                                            break/lunch policies. Keeps supervisor other staff informed of itinerary all
                                            the
                                            time.</p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[attendance][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['attendance']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[attendance][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['attendance']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[attendance][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['attendance']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Professional Appearance</span>
                                        <p class="mb-0 text-muted small">Maintains professional appearance, always
                                            neat,
                                            presentable, descent and keeps the work space in an orderly, clean and
                                            professional manner. </p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[appearance][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['appearance']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[appearance][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['appearance']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[appearance][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['appearance']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Interpersonal Relations</span>
                                        <p class="mb-0 text-muted small">Maintains a positive and balanced disposition
                                            towards fellow employees, the Organization and the assigned job
                                            responsibilities. Deals directly with people in order to establish
                                            harmonious
                                            working relationships, offering positive win-win solutions in dealing with
                                            problem or conflict situations.</p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[interpersonal][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['interpersonal']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[interpersonal][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['interpersonal']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[interpersonal][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['interpersonal']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>

                                </tr>
                                <tr class="table-primary">
                                    <td><span class="fw-bold">Initiative</span>
                                        <p class="mb-0 text-muted small">In unsupervised situations able to anticipate,
                                            and
                                            act on, the needs of the Organization. Proactively seeks out new
                                            responsibilities and offers solutions on improving efficiency and
                                            productivity
                                            and also demonstrates the ability to perceive alternatives and make good
                                            decisions. </p>
                                    </td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[initiative][appraisee_score]"
                                            @if (!$appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['initiative']['appraisee_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[initiative][appraiser_score]"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            value="{{ $appraisal->personal_attributes_assessment['initiative']['appraiser_score'] ?? '' }}"
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>
                                    <td class="text-center">
                                        <input type="number"
                                            name="personal_attributes_assessment[initiative][agreed_score]"
                                            value="{{ $appraisal->personal_attributes_assessment['initiative']['agreed_score'] ?? '' }}"
                                            @if ($appraisal->is_appraisee) readonly @endif
                                            class="form-control form-control-sm score-input" min="0"
                                            max="4">
                                    </td>

                                </tr>
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th>Total Score</th>
                                    <th>40%</th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                        <p><strong>Overall Score (max 40%):</strong> <span id="overall-40pct"></span></p>

                        <div id="performance-table-wrapper" class="table-responsive">
                            <h6 class="h1">PERFORMANCE PLANNING</h6>
                            <p>The Appraiser and Appraisee discuss and agree on the key outputs for the next performance
                                cycle.</p>
                            <table class="table table-hover table-striped mb-0" id="performance-planning-table">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3 text-center">No.</th>
                                        <th class="py-3">Key Output Description</th>
                                        <th class="py-3">Agreed Performance Targets</th>
                                        <th class="py-3">Target Dates</th>
                                        <th class="py-3 no-print"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $planningRows = max(
                                            8,
                                            isset($appraisal->performance_planning)
                                                ? count($appraisal->performance_planning)
                                                : 0,
                                        );
                                    @endphp
                                    @for ($j = 0; $j < $planningRows; $j++)
                                        <tr>
                                            <td class="text-center align-middle row-number">{{ $j + 1 }}.</td>
                                            {{-- Key Output --}}
                                            <td>
                                                @php
                                                    $keyOutput =
                                                        $appraisal->performance_planning[$j]['description'] ?? '';
                                                @endphp
                                                <div class="editable-cell {{ empty($keyOutput) ? 'text-muted' : '' }}"
                                                    contenteditable="{{ $appraisal->is_appraisee || $appraisal->is_appraisor ? 'true' : 'false' }}"
                                                    data-placeholder="Enter key output..."
                                                    oninput="updateHiddenInput(this)">
                                                    {{ $keyOutput ?: 'Enter key output...' }}</div>
                                                <input type="hidden"
                                                    name="performance_planning[{{ $j }}][description]"
                                                    value="{{ $keyOutput }}">
                                            </td>
                                            {{-- Performance Targets --}}
                                            <td>
                                                @php
                                                    $target =
                                                        $appraisal->performance_planning[$j]['performance_target'] ??
                                                        '';
                                                @endphp
                                                <div class="editable-cell {{ empty($target) ? 'text-muted' : '' }}"
                                                    contenteditable="{{ $appraisal->is_appraisee || $appraisal->is_appraisor ? 'true' : 'false' }}"
                                                    data-placeholder="Enter performance targets..."
                                                    oninput="updateHiddenInput(this)">
                                                    {{ $target ?: 'Enter performance target...' }}</div>
                                                <input type="hidden"
                                                    name="performance_planning[{{ $j }}][performance_target]"
                                                    value="{{ $target }}">
                                            </td>
                                            {{-- Target Date --}}
                                            <td>
                                                <input type="date" class="form-control form-control-sm date-input"
                                                    name="performance_planning[{{ $j }}][target_date]"
                                                    value="{{ $appraisal->performance_planning[$j]['target_date'] ?? '' }}">
                                            </td>
                                            <td class="no-print align-middle text-center">
                                                <button type="button" class="btn btn-sm btn-danger remove-row-btn"
                                                    title="Remove row" style="display: none;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endfor
                                </tbody>
                            </table>
                            <div class="mt-2 no-print">
                                <button type="button" class="btn btn-outline-primary btn-sm"
                                    id="add-performance-row">
                                    <i class="fas fa-plus"></i> Add Row
                                </button>
                            </div>
                        </div>
                        @push('scripts')
                            <script>
                                $(function() {
                                    // Add row button
                                    $('#add-performance-row').on('click', function() {
                                        let $table = $('#performance-planning-table');
                                        let $tbody = $table.find('tbody');
                                        let rowCount = $tbody.find('tr').length;
                                        let canEdit = @json($appraisal->is_appraisee || $appraisal->is_appraisor);

                                        let newRow = `
                                    <tr>
                                        <td class="text-center align-middle row-number">${rowCount + 1}.</td>
                                        <td>
                                            <div class="editable-cell text-muted"
                                                contenteditable="${canEdit ? 'true' : 'false'}"
                                                data-placeholder="Enter key output..."
                                                oninput="updateHiddenInput(this)">Enter key output...</div>
                                            <input type="hidden" name="performance_planning[${rowCount}][description]" value="">
                                        </td>
                                        <td>
                                            <div class="editable-cell text-muted"
                                                contenteditable="${canEdit ? 'true' : 'false'}"
                                                data-placeholder="Enter performance targets..."
                                                oninput="updateHiddenInput(this)">Enter performance target...</div>
                                            <input type="hidden" name="performance_planning[${rowCount}][performance_target]" value="">
                                        </td>
                                        <td>
                                            <input type="date" class="form-control form-control-sm date-input"
                                                name="performance_planning[${rowCount}][target_date]" value="">
                                        </td>
                                        <td class="no-print align-middle text-center">
                                            <button type="button" class="btn btn-sm btn-danger remove-row-btn" title="Remove row">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                `;
                                        $tbody.append(newRow);
                                        updateRowNumbers();
                                        showRemoveButtons();
                                    });

                                    // Remove row button
                                    $('#performance-planning-table').on('click', '.remove-row-btn', function() {
                                        let $row = $(this).closest('tr');
                                        $row.remove();
                                        updateRowNumbers();
                                        updateInputNames();
                                        showRemoveButtons();
                                    });

                                    // Show remove buttons only if more than 1 row
                                    function showRemoveButtons() {
                                        let $rows = $('#performance-planning-table tbody tr');
                                        if ($rows.length > 1) {
                                            $rows.find('.remove-row-btn').show();
                                        } else {
                                            $rows.find('.remove-row-btn').hide();
                                        }
                                    }
                                    showRemoveButtons();

                                    // Update row numbers and input names after add/remove
                                    function updateRowNumbers() {
                                        $('#performance-planning-table tbody tr').each(function(i, tr) {
                                            $(tr).find('.row-number').text((i + 1) + '.');
                                        });
                                    }

                                    function updateInputNames() {
                                        $('#performance-planning-table tbody tr').each(function(i, tr) {
                                            $(tr).find('input[type="hidden"]').eq(0).attr('name',
                                                `performance_planning[${i}][description]`);
                                            $(tr).find('input[type="hidden"]').eq(1).attr('name',
                                                `performance_planning[${i}][performance_target]`);
                                            $(tr).find('input[type="date"]').attr('name',
                                                `performance_planning[${i}][target_date]`);
                                        });
                                    }
                                });
                            </script>
                        @endpush


                    </div>
                </div>
            </div>

            <!-- IMMEDIATE SUPERVISOR'S REPORT  -->
            <fieldset class="p-2 mb-4 border">
                <legend class="w-auto">SECTION 3</legend>
                <H1>IMMEDIATE SUPERVISOR'S REPORT</H1>
                <p>(To be completed by Appraiser (Supervisor-Head of Division) after taking into consideration
                    information
                    provided in sections 1 and 2 above)</p>
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <x-forms.text-area name="employee_strength"
                            label="i. Strengths - Summarize employee's strengths" id="employee_strength"
                            :value="old('employee_strength', $appraisal->employee_strength ?? '')" :isDisabled="!$appraisal->is_appraisor" />

                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="employee_improvement"
                            label="ii.	Areas for Improvement - Summarize employee’s areas for improvement"
                            id="employee_improvement" :value="old('employee_improvement', $appraisal->employee_improvement ?? '')" :isDisabled="!$appraisal->is_appraisor" />
                    </div>


                    <div class="col-md-12">
                        <x-forms.text-area name="superviser_overall_assessment"
                            label="iii.	Supervisor’s overall assessment - Describe overall performance in accomplishing goals, fulfilling other results and responsibilities; eg Excellent, Very good, Satisfactory, Average, Unsatisfactory. "
                            id="superviser_overall_assessment" :value="old(
                                'superviser_overall_assessment',
                                $appraisal->superviser_overall_assessment ?? '',
                            )" :isDisabled="!$appraisal->is_appraisor" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="recommendations"
                            label="iv. Recommendations: Recommendations with reasons on whether the employee under review should be promoted, confirmed, remain on probation, redeployed, terminated from Council Service, contract renewed, go for further training, needs counseling, status quo should be maintained, etc.)."
                            id="recommendations" :value="old('recommendations', $appraisal->recommendations ?? '')" :isDisabled="!$appraisal->is_appraisor" />
                    </div>
                </div>
            </fieldset>

            <!-- EVALUATION BY REVIEW PANEL   -->
            <fieldset class="p-2 mb-4 border">
                <legend class="w-auto">SECTION 4</legend>
                <P>EVALUATION BY REVIEW PANEL (When deemed necessary by UNCST Management and
                    with approval of the Executive Secretary)
                </P>
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <x-forms.text-area name="panel_comment" label="(a)	Comments of the Panel." id="panel_comment"
                            :value="old('panel_comment', $appraisal->panel_comment ?? '')" :isDisabled="!$appraisal->is_es" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="panel_recommendation" label="(b)	Recommendation of the Panel"
                            id="panel_recommendation" :value="old('panel_recommendation', $appraisal->panel_recommendation ?? '')" :isDisabled="!$appraisal->is_es" />
                    </div>


                    <div class="col-md-12">
                        <x-forms.text-area name="overall_assessment"
                            label="iii.	Supervisor’s overall assessment - Describe overall performance in accomplishing goals, fulfilling other results and responsibilities; eg Excellent, Very good, Satisfactory, Average, Unsatisfactory. "
                            id="overall_assessment" :value="old('overall_assessment', $appraisal->overall_assessment ?? '')" :isDisabled="!$appraisal->is_es" />
                    </div>

                    <div class="col-md-12">
                        <x-forms.text-area name="recommendations"
                            label="iv. Recommendations: Recommendations with reasons on whether the employee under review should be promoted, confirmed, remain on probation, redeployed, terminated from Council Service, contract renewed, go for further training, needs counseling, status quo should be maintained, etc.)."
                            id="recommendations" :value="old('recommendations', $appraisal->recommendations ?? '')" :isDisabled="!$appraisal->is_es" />
                    </div>
                </div>
            </fieldset>

            {{-- OVERALL ASSESSMENT AND COMMENTS BY THE EXECUTIVE SECRETARY --}}
            <fieldset class="p-2 mb-4 border">
                <legend>SECTION 5</legend>
                <div class="mb-3 row">
                    <div class="col-md-12">
                        <x-forms.text-area name="overall_assessment_and_comments"
                            label="OVERALL ASSESSMENT AND COMMENTS BY THE EXECUTIVE SECRETARY"
                            id="overall_assessment_and_comments" :value="old(
                                'overall_assessment_and_comments',
                                $appraisal->overall_assessment_and_comments ?? '',
                            )" :isDisabled="!$appraisal->is_es" />
                    </div>
                </div>
            </fieldset>

            <!-- SUBMIT SECTION -->
            <div class="py-4 bg-white sticky-bottom border-top">
                <div class="container-lg">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="form-text">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Thank you for filling the appraisal
                        </div>
                        @if ($appraisal->is_appraisee || $appraisal->is_appraisor)
                            <div class="gap-3 d-flex no-print">
                                <button type="reset" class="btn btn-lg btn-outline-secondary">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>

                                <button type="submit" class="btn btn-lg btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Review & Submit
                                </button>
                            </div>
                        @endif
                        @can('approve appraisal')
                            @if (!is_null($appraisal->employee->user_id))
                                @php
                                    $userRole = Auth::user()->roles->pluck('name')[0];
                                    $roleNames = [
                                        'Head of Division' => 'Head of Division',
                                        'HR' => 'HR',
                                        'Executive Secretary' => 'Executive Secretary',
                                    ];
                                    $currentApprover = $appraisal->current_approver ?? 'Executive Secretary';
                                    $previousApprover = 'None';
                                    $isHR = $appraisal->appraiser_id == auth()->user()->employee->employee_id;

                                    if ($userRole == 'HR' && !$isHR) {
                                        $previousApprover = 'Head of Division';
                                    }

                                    if ($userRole == 'Executive Secretary') {
                                        $previousApprover = 'HR';
                                    }
                                    $hasBeenRejected = collect($appraisal->appraisal_request_status)->contains(
                                        fn($status) => $status === 'rejected',
                                    );

                                    $approvedBy = collect($appraisal->appraisal_request_status)
                                        ->filter(fn($status) => $status === 'approved')
                                        ->keys();
                                    $rejectedBy = collect($appraisal->appraisal_request_status)
                                        ->filter(fn($status) => $status === 'rejected')
                                        ->keys();
                                @endphp

                                <div class="m-2 status">
                                    {{-- Current User's Decision --}}
                                    @if (isset($appraisal->appraisal_request_status[$userRole]) &&
                                            $appraisal->appraisal_request_status[$userRole] === 'approved')
                                        <span class="badge bg-success">You Approved this Leave Request.</span>
                                    @elseif (isset($appraisal->appraisal_request_status[$userRole]) &&
                                            $appraisal->appraisal_request_status[$userRole] === 'rejected')
                                        <span class="badge bg-danger">You Rejected this Request</span>
                                        <p class="mt-1"><strong>Rejection Reason:</strong>
                                            {{ $appraisal->rejection_reason }}</p>
                                    @elseif ($appraisal->approval_status === 'approved')
                                        <span class="badge bg-danger">Approved</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif

                                    {{-- Approved By List --}}
                                    @if ($approvedBy->isNotEmpty())
                                        <p class="mt-2"><strong>Approved by:</strong> {{ $approvedBy->join(', ') }}</p>
                                    @endif

                                    {{-- Rejected By List --}}
                                    @if ($rejectedBy->isNotEmpty())
                                        <p class="mt-2"><strong>Rejected by:</strong> {{ $rejectedBy->join(', ') }}</p>
                                    @endif
                                </div>

                                {{-- Approval / Rejection Controls --}}
                                @if ($userRole == $currentApprover || $isHR)
                                    @if ($previousApprover != 'None' && $appraisal->appraisal_request_status[$previousApprover] == 'approved')
                                        <div class="form-group no-print mt-3 d-flex gap-2">
                                            <input class="btn btn-outline-primary btn-large approve-btn" type="button"
                                                value="Approve" data-appraisal-id="{{ $appraisal->appraisal_id }}">
                                            <input class="btn btn-outline-danger btn-large reject-btn" type="button"
                                                value="Reject" data-appraisal-id="{{ $appraisal->appraisal_id }}"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        </div>
                                    @elseif ($previousApprover != 'None' && $appraisal->appraisal_request_status[$previousApprover] == 'rejected')
                                        <p>Rejected by the {{ $previousApprover }}</p>
                                    @else
                                        <div class="form-group no-print mt-3 d-flex gap-2">
                                            <input class="btn btn-outline-primary btn-large approve-btn" type="button"
                                                value="Approve" data-appraisal-id="{{ $appraisal->appraisal_id }}">
                                            <input class="btn btn-outline-danger btn-large reject-btn" type="button"
                                                value="Reject" data-appraisal-id="{{ $appraisal->appraisal_id }}"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal">
                                        </div>
                                    @endif
                                @else
                                    <p class="mt-3">
                                        Waiting for approval from:
                                        <strong>{{ $roleNames[$currentApprover] ?? $currentApprover }}</strong>
                                    </p>
                                @endif
                            @endif
                        @endcan


                    </div>
                </div>
            </div>

        </div>

        <div class="d-none preview-section">
            {{-- preview all the form information --}}
            <h1 class="text-center">Appraisal Preview</h1>
            <div class="container-lg">
                @include('appraisals.appraisal_preview', ['appraisal' => $appraisal])
            </div>
        </div>

        {{-- back button to the form --}}
        <div class="d-flex justify-content-center align-items-center gap-3 my-4 no-print">
            <button class="btn btn-outline-secondary btn-lg d-none" id="backToFormBtn" style="min-width: 180px;">
                <i class="fas fa-arrow-left me-2"></i>Back to Form
            </button>
            <button type="submit" class="btn btn-outline-primary btn-lg d-none" id="proceed-btn"
                style="min-width: 180px;">
                <i class="fas fa-paper-plane me-2"></i>Proceed
            </button>
        </div>
        </div>

    </form>



    <!-- Bootstrap Modal for Rejection Reason -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">Reject Appraisal Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <label for="rejectionReason">Please enter the reason for rejection:</label>
                    <textarea id="rejectionReason" class="form-control" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirmReject">Reject</button>
                </div>
            </div>
        </div>
    </div>


    <style>
        .notes-list {
            counter-reset: section;
            padding-left: 0;
        }

        @media print {

            /* Remove Bootstrap container padding if needed */
            .container,
            .container-fluid {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
            }

            /* Ensure background colors show (some browsers strip them) */
            .bg-primary,
            .bg-secondary,
            .bg-info,
            .bg-danger,
            .bg-success,
            .bg-warning {
                -webkit-print-color-adjust: exact !important;
                /* Chrome/Safari */
                print-color-adjust: exact !important;
                color: white !important;
            }

            /* Ensure text colors are preserved */
            .text-white {
                color: white !important;
            }

            .text-primary,
            .text-danger,
            .text-muted {
                print-color-adjust: exact !important;
            }

            /* Hide elements with Bootstrap display utilities */
            .no-print,
            .d-none,
            .d-print-none {
                display: none !important;
            }

            /* Show hidden print-only elements */
            .d-print-block {
                display: block !important;
            }

            .d-print-inline {
                display: inline !important;
            }

            .d-print-inline-block {
                display: inline-block !important;
            }

            /* Force table styles */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
            }

            th,
            td {
                border: 1px solid #000 !important;
                padding: 5px !important;
            }

            /* Remove box shadows and rounded corners */
            .shadow,
            .card,
            .rounded,
            .border {
                box-shadow: none !important;
                border-radius: 0 !important;
            }
        }


        .notes-list li {
            counter-increment: section;
            list-style: none;
            position: relative;
            padding-left: 2.5rem;
        }

        .notes-list li::before {
            content: counter(section, upper-roman);
            position: absolute;
            left: 0;
            top: -0.1em;
            font-weight: bold;
            color: #0d6efd;
            font-size: 1.2em;
        }

        .form-section {
            transition: all 0.3s ease;
            border: 1px solid #dee2e6;
        }

        .form-section:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.05);
        }

        .score-input {
            width: 80px;
            margin: 0 auto;
            text-align: center;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.05);
        }

        .sticky-bottom {
            position: sticky;
            bottom: 0;
            z-index: 1020;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05);
        }

        .editable-cell {
            min-height: 10vh;
            line-height: 1.5;
            font-family: Verdana, Geneva, Tahoma, sans-serif
        }

        .editable-cell[data-placeholder]:empty::before {
            content: attr(data-placeholder);
            color: #9ca3af;
        }

        .score-cell {
            min-height: 2rem;
            min-width: 3rem;
            padding: 0.5rem;
            border-radius: 0.375rem;
            text-align: center;
            transition: all 0.2s ease;
            background-color: #f8fafc;
            font-weight: 600;
            color: #1e40af;
        }

        .score-cell:focus {
            background-color: #e0f2fe;
            outline: none;
        }

        .percentage-display {
            font-weight: 600;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
            transition: color 0.3s ease;
        }

        #averageProgress {
            transition: width 0.7s cubic-bezier(0.4, 0, 0.2, 1), background-color 0.7s ease;
        }

        #performance-table-wrapper .date-input {
            border: none;
            width: 100%;
            padding: 1rem;
            background-color: transparent;
            font-size: 1rem;
            color: #212529;
        }

        #performance-table-wrapper .date-input:focus {
            outline: none;
            background-color: #f8f9fa;
        }
    </style>
    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize all components
                initScoreValidation();
                initSelect2();
                initAppraisalApproval();
                initActivityRatingTable();
                initPersonalAttributesTable();
                initPerformanceTableAutoSave();

                // on submitting the form, update the hidden inputs
                $('#appraisalForm').on('submit', function(e) {
                    e.preventDefault();

                    if ($('input[name="review_type"]:checked').val() === 'end_of_contract') {
                        // If contract details are missing, show message and return
                        var contractDetails = $('#expireContractDetails');
                        if (contractDetails.length && contractDetails.find('.alert-info').length === 0) {
                            alert(
                                'No contract expiry details available. Please comfirm the Review type you selected and try updating again'
                                );
                            return;
                        }
                    }

                    // show the preview section
                    $('.preview-section').removeClass('d-none');

                    // get all the form data
                    const formData = $(this).serializeArray();

                    // Helper: get label for a field
                    function getLabel(name) {
                        let label = $(`label[for="${name}"]`).text();
                        if (!label) {
                            label = $(`[name="${name}"]`).closest('.form-group, .form-section, td').find(
                                'label').first().text();
                        }
                        if (label) {
                            label = label.replace(/^(.*?)(\1)+$/, '$1');
                        }
                        return label || name.replace(/_/g, ' ').replace(/\[|\]/g, ' ');
                    }

                    // Helper: get select display value
                    function getSelectDisplay(name, value) {
                        const $select = $(`[name="${name}"]`);
                        if ($select.length && $select.is('select')) {
                            const $option = $select.find(`option[value="${value}"]`);
                            return $option.length ? $option.text() : value;
                        }
                        return value;
                    }

                    // Helper: get radio value (even if not selected)
                    function getRadioDisplay(name) {
                        const $radios = $(`input[type="radio"][name="${name}"]`);
                        if ($radios.length) {
                            const $checked = $radios.filter(':checked');
                            if ($checked.length) {
                                const id = $checked.attr('id');
                                let label = id ? $(`label[for="${id}"]`).text() : '';
                                return label || $checked.val();
                            } else {
                                let groupLabel = $radios.closest('.form-section, .form-group').find('label')
                                    .first().text();
                                if (groupLabel) {
                                    groupLabel = groupLabel.replace(/^(.*?)(\1)+$/, '$1');
                                }
                                return {
                                    label: groupLabel || name.replace(/_/g, ' '),
                                    value: null
                                };
                            }
                        }
                        return null;
                    }

                    // Helper: get textarea value (even if empty)
                    function getTextareaDisplay(name) {
                        const $textarea = $(`textarea[name="${name}"]`);
                        if ($textarea.length) {
                            return $textarea.val();
                        }
                        return null;
                    }

                    // Group fields by section/table
                    const sectionFields = {
                        'review': [],
                        'personal_details': [],
                        'appraisal_period_rate': [],
                        'personal_attributes_assessment': [],
                        'performance_planning': [],
                        'supervisor_report': [],
                        'panel_evaluation': [],
                        'es_comments': [],
                    };
                    let otherFields = [];

                    // Map for section assignment (simple, can be improved)
                    const sectionMap = {
                        'review_type': 'review',
                        'appraisal_start_date': 'review',
                        'appraisal_end_date': 'review',
                        'appraiser_id': 'review',
                        'job_compatibility': 'personal_details',
                        'if_no_job_compatibility': 'personal_details',
                        'unanticipated_constraints': 'personal_details',
                        'personal_initiatives': 'personal_details',
                        'training_support_needs': 'personal_details',
                        'employee_strength': 'supervisor_report',
                        'employee_improvement': 'supervisor_report',
                        'superviser_overall_assessment': 'supervisor_report',
                        'recommendations': 'supervisor_report',
                        'panel_comment': 'panel_evaluation',
                        'panel_recommendation': 'panel_evaluation',
                        'overall_assessment': 'panel_evaluation',
                        'overall_assessment_and_comments': 'es_comments',
                    };

                    // Track which radio/textarea questions we've seen
                    const radioQuestions = {};
                    const textareaQuestions = {};

                    // Find all radio and textarea questions in the DOM for previewing missing answers
                    $('input[type="radio"]').each(function() {
                        const name = $(this).attr('name');
                        if (name && !radioQuestions[name]) {
                            let groupLabel = $(this).closest('.form-section, .form-group').find('label')
                                .first().text();
                            if (groupLabel) {
                                groupLabel = groupLabel.replace(/^(.*?)(\1)+$/, '$1');
                            }
                            radioQuestions[name] = groupLabel || name.replace(/_/g, ' ');
                        }
                    });
                    $('textarea').each(function() {
                        const name = $(this).attr('name');
                        if (name && !textareaQuestions[name]) {
                            let label = $(`label[for="${name}"]`).text();
                            if (!label) {
                                label = $(this).closest('.form-section, .form-group').find('label')
                                    .first().text();
                            }
                            if (label) {
                                label = label.replace(/^(.*?)(\1)+$/, '$1');
                            }
                            textareaQuestions[name] = label || name.replace(/_/g, ' ');
                        }
                    });

                    // Helper: check if a field is readonly/disabled or contenteditable and not editable
                    function isReadonlyField(name) {
                        // Try input, textarea, select
                        let $el = $(`[name="${name}"]`);
                        if (!$el.length) {
                            // Try for array fields (e.g. appraisal_period_rate[0][planned_activity])
                            $el = $(`[name^="${name}"]`);
                        }
                        if ($el.length) {
                            if ($el.prop('readonly') || $el.prop('disabled') || $el.is('[readonly]') || $el.is(
                                    '[disabled]')) {
                                return true;
                            }
                        }
                        // For radio groups, check all radios
                        if (name && name.includes('[')) {
                            // Try to match for array fields
                            let base = name.replace(/\[\d+\]/, '');
                            let $radios = $(`input[type="radio"][name^="${base}"]`);
                            if ($radios.length) {
                                return $radios.prop('readonly') || $radios.prop('disabled');
                            }
                        }
                        // Check for contenteditable fields (e.g. supervisor_comment)
                        // Try to find a .editable-cell or .score-cell with a matching input[name]
                        let $input = $(`[name="${name}"]`);
                        if ($input.length) {
                            let $editable = $input.prev('.editable-cell, .score-cell');
                            if ($editable.length) {
                                // If contenteditable is false or not present, treat as readonly
                                if ($editable.attr('contenteditable') !== 'true') {
                                    return true;
                                }
                            }
                        }
                        return false;
                    }

                    // Helper for missing fields, now also checks for contenteditable fields
                    function displayValue(val, name = null) {
                        if (val && val !== 'Not provided' && val.trim() !== '') {
                            return `<span class="text-success fw-semibold">${val}</span>`;
                        }
                        // If name is provided, check if field is readonly or not editable (including contenteditable)
                        if (name && isReadonlyField(name)) {
                            return `<span class="text-warning fw-semibold">The user who is supposed to fill this has not filled it yet</span>`;
                        }
                        return `<span class="text-danger fw-semibold">Not provided</span>`;
                    }

                    // Helper: for each section, get all textarea fields in that section
                    function getSectionTextareaNames(sectionKey) {
                        const sectionSelectors = {
                            personal_details: '.form-section:has(textarea[name="if_no_job_compatibility"]), .form-section:has(textarea[name="unanticipated_constraints"]), .form-section:has(textarea[name="personal_initiatives"]), .form-section:has(textarea[name="training_support_needs"])',
                            supervisor_report: 'fieldset:has(textarea[name="employee_strength"])',
                            panel_evaluation: 'fieldset:has(textarea[name="panel_comment"])',
                            es_comments: 'fieldset:has(textarea[name="overall_assessment_and_comments"])'
                        };
                        let names = [];
                        if (sectionSelectors[sectionKey]) {
                            $(sectionSelectors[sectionKey]).find('textarea').each(function() {
                                const name = $(this).attr('name');
                                if (name) names.push(name);
                            });
                        }
                        return names;
                    }

                    formData.forEach(function(item) {
                        if (item.name === '_method' || item.name === '_token') return;

                        if (item.name.startsWith('appraisal_period_rate')) {
                            const match = item.name.match(/appraisal_period_rate\[(\d+)\]\[(.+?)\]/);
                            if (match) {
                                const idx = match[1],
                                    key = match[2];
                                if (!sectionFields.appraisal_period_rate[idx]) sectionFields
                                    .appraisal_period_rate[idx] = {};
                                sectionFields.appraisal_period_rate[idx][key] = item.value || '';
                            }
                        } else if (item.name.startsWith('personal_attributes_assessment')) {
                            const match = item.name.match(
                                /personal_attributes_assessment\[(.+?)\]\[(.+?)\]/);
                            if (match) {
                                const attr = match[1],
                                    key = match[2];
                                if (!sectionFields.personal_attributes_assessment[attr]) sectionFields
                                    .personal_attributes_assessment[attr] = {};
                                sectionFields.personal_attributes_assessment[attr][key] = item.value ||
                                    '';
                            }
                        } else if (item.name.startsWith('performance_planning')) {
                            const match = item.name.match(/performance_planning\[(\d+)\]\[(.+?)\]/);
                            if (match) {
                                const idx = match[1],
                                    key = match[2];
                                if (!sectionFields.performance_planning[idx]) sectionFields
                                    .performance_planning[idx] = {};
                                sectionFields.performance_planning[idx][key] = item.value || '';
                            }
                        } else if (sectionMap[item.name]) {
                            sectionFields[sectionMap[item.name]].push({
                                name: item.name,
                                value: item.value
                            });
                        } else {
                            otherFields.push(item);
                        }
                    });

                    function sectionBlock(title, icon, html, opts = {}) {
                        return `
                        <section class="preview-section-block mb-4 border rounded shadow-sm bg-white">
                            <div class="section-header px-3 py-2 d-flex align-items-center ${opts.headerClass || ''}">
                                <span class="me-2">${icon}</span>
                                <h5 class="mb-0">${title}</h5>
                            </div>
                            <div class="section-body px-3 py-2">${html}</div>
                        </section>
                        `;
                    }

                    // 1. Review Section
                    let reviewHtml = '';
                    if (sectionFields.review.length) {
                        reviewHtml += '<div class="row">';
                        sectionFields.review.forEach(function(item) {
                            let label = getLabel(item.name);
                            let value = getSelectDisplay(item.name, item.value) || '';
                            reviewHtml += `<div class="col-md-6 mb-2">
                                <strong>${label}:</strong>
                                ${displayValue(value, item.name)}
                            </div>`;
                        });
                        reviewHtml += '</div>';
                    }

                    // 2. Personal Details Section (handle missing radios/textareas)
                    let personalHtml = '';
                    if (sectionFields.personal_details.length || Object.keys(radioQuestions).length || Object
                        .keys(textareaQuestions).length) {
                        personalHtml += '<div class="row">';
                        // Show all radio questions, even if not answered
                        Object.entries(radioQuestions).forEach(([name, label]) => {
                            if (label) {
                                label = label.replace(/^(.*?)(\1)+$/, '$1');
                            }
                            let value = formData.find(f => f.name === name);
                            if (value && value.value) {
                                const $checked = $(`input[type="radio"][name="${name}"]:checked`);
                                let radioLabel = '';
                                if ($checked.length) {
                                    const id = $checked.attr('id');
                                    radioLabel = id ? $(`label[for="${id}"]`).text() : '';
                                }
                                personalHtml += `<div class="col-md-6 mb-2">
                                    <strong>${label}:</strong>
                                    ${displayValue(radioLabel || value.value, name)}
                                </div>`;
                            } else {
                                personalHtml += `<div class="col-md-6 mb-2">
                                    <strong>${label}:</strong>
                                    ${displayValue('', name)}
                                </div>`;
                            }
                        });
                        // Show all textarea questions, even if not answered, but only those in this section
                        getSectionTextareaNames('personal_details').forEach(name => {
                            let label = textareaQuestions[name] || name.replace(/_/g, ' ');
                            if (label) {
                                label = label.replace(/^(.*?)(\1)+$/, '$1');
                            }
                            let valueObj = formData.find(f => f.name === name);
                            let value = valueObj ? valueObj.value : '';
                            personalHtml += `<div class="col-md-6 mb-2">
                                <strong>${label}:</strong>
                                ${displayValue(value, name)}
                            </div>`;
                        });
                        // Also show any other personal_details fields
                        sectionFields.personal_details.forEach(function(item) {
                            if (!radioQuestions[item.name] && !textareaQuestions[item.name]) {
                                let label = getLabel(item.name);
                                if (label) {
                                    label = label.replace(/^(.*?)(\1)+$/, '$1');
                                }
                                personalHtml += `<div class="col-md-6 mb-2">
                                    <strong>${label}:</strong>
                                    ${displayValue(item.value, item.name)}
                                </div>`;
                            }
                        });
                        personalHtml += '</div>';
                    }

                    // 3. Appraisal Period Rate Table
                    let dutiesHtml = '';
                    if (sectionFields.appraisal_period_rate.length) {
                        dutiesHtml += `
                        <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle preview-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Planned Activity</th>
                                    <th>Output Results</th>
                                    <th>Supervisee Score</th>
                                    <th>Supervisor Score</th>
                                    <th>Agreed Score</th>
                                    <th>Supervisor Comment</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;
                        sectionFields.appraisal_period_rate.forEach(function(row, i) {
                            if (!row) return;
                            dutiesHtml += `<tr>
                                <td>${i + 1}</td>
                                <td>${displayValue(row.planned_activity, `appraisal_period_rate[${i}][planned_activity]`)}</td>
                                <td>${displayValue(row.output_results, `appraisal_period_rate[${i}][output_results]`)}</td>
                                <td>${displayValue(row.supervisee_score, `appraisal_period_rate[${i}][supervisee_score]`)}</td>
                                <td>${displayValue(row.supervisor_score, `appraisal_period_rate[${i}][supervisor_score]`)}</td>
                                <td>${displayValue(row.agreed_score, `appraisal_period_rate[${i}][agreed_score]`)}</td>
                                <td>${displayValue(row.supervisor_comment, `appraisal_period_rate[${i}][supervisor_comment]`)}</td>
                            </tr>`;
                        });
                        dutiesHtml += '</tbody></table></div>';
                    }

                    // 4. Personal Attributes Table
                    let attributesHtml = '';
                    if (Object.keys(sectionFields.personal_attributes_assessment).length) {
                        attributesHtml += `
                        <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle preview-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Attribute</th>
                                    <th>Appraisee Score</th>
                                    <th>Appraiser Score</th>
                                    <th>Agreed Score</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;
                        Object.entries(sectionFields.personal_attributes_assessment).forEach(([attr,
                            scores
                        ]) => {
                            attributesHtml += `<tr>
                                <td>${attr.replace(/_/g, ' ')}</td>
                                <td>${displayValue(scores.appraisee_score, `personal_attributes_assessment[${attr}][appraisee_score]`)}</td>
                                <td>${displayValue(scores.appraiser_score, `personal_attributes_assessment[${attr}][appraiser_score]`)}</td>
                                <td>${displayValue(scores.agreed_score, `personal_attributes_assessment[${attr}][agreed_score]`)}</td>
                            </tr>`;
                        });
                        attributesHtml += '</tbody></table></div>';
                    }

                    // 5. Performance Planning Table
                    let planningHtml = '';
                    if (sectionFields.performance_planning.length) {
                        planningHtml += `
                        <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle preview-table">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Key Output Description</th>
                                    <th>Performance Target</th>
                                    <th>Target Date</th>
                                </tr>
                            </thead>
                            <tbody>
                        `;
                        sectionFields.performance_planning.forEach(function(row, i) {
                            if (!row) return;
                            planningHtml += `<tr>
                                <td>${i + 1}</td>
                                <td>${displayValue(row.description, `performance_planning[${i}][description]`)}</td>
                                <td>${displayValue(row.performance_target, `performance_planning[${i}][performance_target]`)}</td>
                                <td>${displayValue(row.target_date, `performance_planning[${i}][target_date]`)}</td>
                            </tr>`;
                        });
                        planningHtml += '</tbody></table></div>';
                    }

                    // 6. Supervisor Report
                    let supervisorHtml = '';
                    if (sectionFields.supervisor_report.length || getSectionTextareaNames('supervisor_report')
                        .length) {
                        supervisorHtml += '<div class="row">';
                        getSectionTextareaNames('supervisor_report').forEach(name => {
                            let label = textareaQuestions[name] || name.replace(/_/g, ' ');
                            if (label) {
                                label = label.replace(/^(.*?)(\1)+$/, '$1');
                            }
                            let valueObj = formData.find(f => f.name === name);
                            let value = valueObj ? valueObj.value : '';
                            supervisorHtml += `<div class="col-md-12 mb-2">
                                <strong>${label}:</strong>
                                ${displayValue(value, name)}
                            </div>`;
                        });
                        sectionFields.supervisor_report.forEach(function(item) {
                            if (!textareaQuestions[item.name]) {
                                let label = getLabel(item.name);
                                if (label) {
                                    label = label.replace(/^(.*?)(\1)+$/, '$1');
                                }
                                supervisorHtml += `<div class="col-md-12 mb-2">
                                    <strong>${label}:</strong>
                                    ${displayValue(item.value, item.name)}
                                </div>`;
                            }
                        });
                        supervisorHtml += '</div>';
                    }

                    // 7. Panel Evaluation
                    let panelHtml = '';
                    if (sectionFields.panel_evaluation.length || getSectionTextareaNames('panel_evaluation')
                        .length) {
                        panelHtml += '<div class="row">';
                        getSectionTextareaNames('panel_evaluation').forEach(name => {
                            let label = textareaQuestions[name] || name.replace(/_/g, ' ');
                            if (label) {
                                label = label.replace(/^(.*?)(\1)+$/, '$1');
                            }
                            let valueObj = formData.find(f => f.name === name);
                            let value = valueObj ? valueObj.value : '';
                            panelHtml += `<div class="col-md-12 mb-2">
                                <strong>${label}:</strong>
                                ${displayValue(value, name)}
                            </div>`;
                        });
                        sectionFields.panel_evaluation.forEach(function(item) {
                            if (!textareaQuestions[item.name]) {
                                let label = getLabel(item.name);
                                if (label) {
                                    label = label.replace(/^(.*?)(\1)+$/, '$1');
                                }
                                panelHtml += `<div class="col-md-12 mb-2">
                                    <strong>${label}:</strong>
                                    ${displayValue(item.value, item.name)}
                                </div>`;
                            }
                        });
                        panelHtml += '</div>';
                    }

                    // 8. ES Comments
                    let esHtml = '';
                    if (sectionFields.es_comments.length || getSectionTextareaNames('es_comments').length) {
                        esHtml += '<div class="row">';
                        getSectionTextareaNames('es_comments').forEach(name => {
                            let label = textareaQuestions[name] || name.replace(/_/g, ' ');
                            if (label) {
                                label = label.replace(/^(.*?)(\1)+$/, '$1');
                            }
                            let valueObj = formData.find(f => f.name === name);
                            let value = valueObj ? valueObj.value : '';
                            esHtml += `<div class="col-md-12 mb-2">
                                <strong>${label}:</strong>
                                ${displayValue(value, name)}
                            </div>`;
                        });
                        sectionFields.es_comments.forEach(function(item) {
                            if (!textareaQuestions[item.name]) {
                                let label = getLabel(item.name);
                                if (label) {
                                    label = label.replace(/^(.*?)(\1)+$/, '$1');
                                }
                                esHtml += `<div class="col-md-12 mb-2">
                                    <strong>${label}:</strong>
                                    ${displayValue(item.value, item.name)}
                                </div>`;
                            }
                        });
                        esHtml += '</div>';
                    }

                    // Build preview HTML with clear sections
                    let previewHtml = `
                        <style>
                            .preview-section-block {
                                border-left: 5px solid #0d6efd;
                                margin-bottom: 2rem;
                                background: #f8fafc;
                            }
                            .preview-section-block .section-header {
                                background: #e9ecef;
                                border-bottom: 1px solid #dee2e6;
                                font-weight: 600;
                                font-size: 1.1rem;
                                color: #0d6efd;
                            }
                            .preview-section-block .section-body {
                                font-size: 1rem;
                                color: #212529;
                            }
                            .preview-table th, .preview-table td {
                                vertical-align: middle !important;
                            }
                            .preview-table td .text-danger {
                                font-weight: bold;
                                letter-spacing: 0.5px;
                            }
                            .preview-table td .text-success {
                                font-weight: 500;
                            }
                            .preview-section-block:not(:last-child) {
                                box-shadow: 0 2px 8px rgba(13,110,253,0.04);
                            }
                            .preview-section-block .section-header {
                                border-radius: 0.25rem 0.25rem 0 0;
                            }
                            .preview-section-block .section-body {
                                border-radius: 0 0 0.25rem 0.25rem;
                            }
                            .preview-section-block .section-header .fa, 
                            .preview-section-block .section-header svg {
                                color: #0d6efd;
                                font-size: 1.3em;
                            }
                        </style>
                        <div class="mb-4">
                            <h2 class="text-center text-primary mb-4">Appraisal Preview</h2>
                        </div>
                    `;

                    previewHtml += sectionBlock(
                        'Type of Review & Period',
                        '<i class="fa fa-calendar-alt"></i>',
                        reviewHtml
                    );
                    previewHtml += sectionBlock(
                        'Personal Details & Section 1',
                        '<i class="fa fa-user"></i>',
                        personalHtml
                    );
                    previewHtml += sectionBlock(
                        'Section 2: Key Duties & Responsibilities',
                        '<i class="fa fa-tasks"></i>',
                        dutiesHtml
                    );
                    previewHtml += sectionBlock(
                        'Section 2: Personal Attributes Assessment',
                        '<i class="fa fa-star"></i>',
                        attributesHtml
                    );
                    previewHtml += sectionBlock(
                        'Section 2: Performance Planning',
                        '<i class="fa fa-bullseye"></i>',
                        planningHtml
                    );
                    previewHtml += sectionBlock(
                        "Section 3: Supervisor's Report",
                        '<i class="fa fa-user-tie"></i>',
                        supervisorHtml
                    );
                    previewHtml += sectionBlock(
                        'Section 4: Panel Evaluation',
                        '<i class="fa fa-users"></i>',
                        panelHtml
                    );
                    previewHtml += sectionBlock(
                        'Section 5: Executive Secretary Comments',
                        '<i class="fa fa-user-shield"></i>',
                        esHtml
                    );

                    $('.preview-section').html(previewHtml);

                    $('#proceed-btn').removeClass('d-none');
                    $('#backToFormBtn').removeClass('d-none');

                    $('#backToFormBtn').on('click', function(event) {
                        event.preventDefault();
                        $('.entire-form').removeClass('d-none');
                        $('.preview-section').addClass('d-none');
                        $('#proceed-btn').addClass('d-none');
                        $('#backToFormBtn').addClass('d-none');
                        $('html, body').animate({
                            scrollTop: $('#appraisalForm').offset().top
                        }, 500);
                    });

                    $('#proceed-btn').on('click', function(event) {
                        event.preventDefault();
                        $('.entire-form').removeClass('d-none');
                        $('#appraisalForm').off('submit');
                        $('#appraisalForm').submit();
                    });

                    $('.entire-form').addClass('d-none');
                    $('html, body').animate({
                        scrollTop: $('.preview-section').offset().top
                    }, 500);
                });


                // proceed button click should should submit the form
                $('#proceed-btn').click(function() {
                    //remove the d-none class from the entire form
                    $('.entire-form').removeClass('d-none');

                    //detach the submit event handler
                    $('#appraisalForm').off('submit');

                    ///then submit the form
                    $('#appraisalForm').submit();
                });

                // 1. Score Input Validation
                function initScoreValidation() {
                    function clampScore($input, max = 4, min = 0) {
                        let val = parseFloat($input.val());
                        if (val > max) $input.val(max);
                        else if (val < min) $input.val(min);
                    }

                    $('.score-input, .score-input-component').on('input', function() {
                        clampScore($(this));
                    });
                }

                // 2. Select2 Initialization
                function initSelect2() {
                    $('.employees').select2({
                        theme: "bootstrap-5",
                        placeholder: $(this).data('placeholder'),
                        dropdownParent: $('.appraisal-information')
                    });
                }
                // Expose this function globally
                window.updateHiddenInput = function(element) {
                    const text = element.textContent.trim();
                    const placeholder = element.dataset.placeholder || 'Enter text';
                    const hiddenInput = element.nextElementSibling;

                    // If the field is empty, reapply placeholder and muted style
                    if (!text) {
                        element.textContent = placeholder;
                        element.classList.add('text-muted');
                        if (hiddenInput && hiddenInput.tagName === 'INPUT') {
                            hiddenInput.value = '';
                        }
                    } else {
                        // If user types, remove muted style and update input
                        if (element.classList.contains('text-muted')) {
                            element.classList.remove('text-muted');
                        }
                        if (hiddenInput && hiddenInput.tagName === 'INPUT') {
                            hiddenInput.value = text;
                        }
                    }
                };

                document.querySelectorAll('.editable-cell').forEach(cell => {
                    const placeholder = cell.dataset.placeholder;
                    const text = cell.textContent.trim();

                    // If empty, show placeholder
                    if (!text && placeholder) {
                        cell.textContent = placeholder;
                        cell.classList.add('text-muted');
                    }

                    cell.addEventListener('focus', () => {
                        if (cell.classList.contains('text-muted')) {
                            cell.textContent = '';
                            cell.classList.remove('text-muted');
                        }
                    });

                    cell.addEventListener('blur', () => {
                        if (!cell.textContent.trim()) {
                            cell.textContent = placeholder;
                            cell.classList.add('text-muted');
                        }
                    });
                });


                // Expose this function globally
                window.updateScoreInput = function(scoreCell) {
                    const value = Math.min(Math.max(parseInt(scoreCell.textContent) || 0, 0), 6);
                    scoreCell.textContent = value;
                    const hiddenInput = scoreCell.nextElementSibling;
                    if (hiddenInput && hiddenInput.tagName === 'INPUT') {
                        hiddenInput.value = value;
                    }
                }

                document.querySelectorAll('.score-cell').forEach(cell => {
                    // Only add events if cell is editable (not readonly)
                    if (cell.getAttribute('contenteditable') === 'true') {
                        cell.addEventListener('click', function () {
                            if (this.textContent.trim() === '0') {
                                this.textContent = '';
                                const hiddenInput = this.nextElementSibling;
                                if (hiddenInput && hiddenInput.tagName === 'INPUT') {
                                    hiddenInput.value = '';
                                }
                            }
                        });
                        cell.addEventListener('blur', function () {
                            if (this.textContent.trim() === '') {
                                this.textContent = '0';
                                const hiddenInput = this.nextElementSibling;
                                if (hiddenInput && hiddenInput.tagName === 'INPUT') {
                                    hiddenInput.value = 0;
                                }
                            }
                        });
                    }
                });

                // 3. Appraisal Approval Logic
                function initAppraisalApproval() {
                    let currentAppraisalId;

                    $('.approve-btn').click(function() {
                        currentAppraisalId = $(this).data('appraisal-id');
                        approveAppraisal(currentAppraisalId, 'approved');
                    });

                    $('.reject-btn').click(function() {
                        currentAppraisalId = $(this).data('appraisal-id');
                    });

                    $('#confirmReject').click(function() {
                        const reason = $('#rejectionReason').val();
                        if (reason) {
                            approveAppraisal(currentAppraisalId, 'rejected', reason);
                            $('#rejectModal').modal('hide');
                        } else {
                            showToast('Please enter a rejection reason.');
                        }
                    });

                    function approveAppraisal(appraisalId, status, reason = null) {
                        $.ajax({
                            url: `/appraisals/${appraisalId}/status`,
                            type: 'POST',
                            contentType: 'application/json',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            data: JSON.stringify({
                                status,
                                reason
                            }),
                            success: function(data) {
                                handleApprovalSuccess(data, status, reason);
                                $('.approve-btn, .reject-btn').prop('disabled', true);
                            },
                            error: function(xhr) {
                                showToast(xhr.responseJSON?.error || 'An error occurred');
                            }
                        });
                    }

                    function handleApprovalSuccess(data, status, reason) {
                        showToast(data.message);
                        const $statusContainer = $('.status').empty();

                        if (status === 'approved') {
                            $statusContainer.append(`<span class="badge bg-success">You Approved this request</span>`);
                        } else if (status === 'rejected') {
                            $statusContainer.append(`<span class="badge bg-danger">You Rejected this request</span>`);
                            if (reason) {
                                $statusContainer.append(
                                    `<p class="mt-1"><strong>Rejection Reason:</strong> ${reason}</p>`);
                            }
                        }
                    }
                }

                // 4. Activity Rating Table Logic
                function initActivityRatingTable() {
                    const rows = document.querySelectorAll('tr[data-row]');
                    const overallAverageElement = document.getElementById('overallAverage');
                    const averageProgress = document.getElementById('averageProgress');
                    const performanceStatus = document.getElementById('performanceStatus');

                    const statusMessages = [{
                            threshold: 90,
                            message: 'Exceptional Performance',
                            color: 'from-green-400 to-emerald-600'
                        },
                        {
                            threshold: 75,
                            message: 'Exceeds Expectations',
                            color: 'from-blue-400 to-indigo-600'
                        },
                        {
                            threshold: 50,
                            message: 'Meets Expectations',
                            color: 'from-yellow-400 to-amber-600'
                        },
                        {
                            threshold: 25,
                            message: 'Needs Improvement',
                            color: 'from-orange-400 to-red-600'
                        },
                        {
                            threshold: 0,
                            message: 'Requires Immediate Attention',
                            color: 'from-red-400 to-rose-600'
                        }
                    ];

                    // Add event to score cells only for clamping
                    rows.forEach(row => {
                        row.querySelectorAll('.score-cell').forEach(cell => {
                            cell.addEventListener('input', function() {
                                clampScoreCell(this);
                            });
                        });
                    });

                    // Add event to agreed score inputs
                    const agreedInputs = document.querySelectorAll('.agreed-score-input');
                    agreedInputs.forEach(input => {
                        input.addEventListener('input', function() {
                            // Clamp value between 0 and 6
                            let val = parseFloat(this.value);
                            if (val > 6) this.value = 6;
                            else if (val < 0 || isNaN(val)) this.value = '';
                            updateOverallAverage();
                        });
                        input.addEventListener('focus', function() {
                            // If input is readonly, show a tooltip and do not clear value
                            if (this.hasAttribute('readonly') || this.hasAttribute('disabled')) {
                                this.title = "You are not allowed to update this value.";
                                // Optionally, show a Bootstrap tooltip if available
                                if (typeof bootstrap !== 'undefined') {
                                    bootstrap.Tooltip.getOrCreateInstance(this).show();
                                }
                                return;
                            } else {
                                // If value is default (e.g. 0), clear on focus for easier entry
                                if (this.value == '0') this.value = '';
                            }
                        });
                        input.addEventListener('blur', function() {
                            // If left empty, set to 0
                            if (this.value === '') this.value = 0;
                            updateOverallAverage();
                        });
                    });

                    // Initial calculation
                    updateOverallAverage();

                    function clampScoreCell(cell) {
                        let value = parseInt(cell.textContent);
                        cell.textContent = isNaN(value) ? '' : Math.min(Math.max(value, 0), 6);
                    }

                    function updateOverallAverage() {
                        let totalAgreed = 0;
                        let rowCount = 0;

                        // Calculate based on agreed scores only
                        agreedInputs.forEach(input => {
                            const value = parseFloat(input.value);
                            if (!isNaN(value) && value >= 0 && value <= 6) {
                                totalAgreed += value;
                                rowCount++;
                            }
                        });

                        const averagePerRow = rowCount > 0 ? totalAgreed / rowCount : 0;
                        const averagePercentage = (averagePerRow / 6) * 100;
                        const scaledAvg = averagePercentage * 0.6; // 60% weight

                        overallAverageElement.textContent = scaledAvg.toFixed(1);
                        averageProgress.style.width = `${averagePercentage}%`;

                        const status = statusMessages.find(s => averagePercentage >= s.threshold);
                        if (status) {
                            performanceStatus.textContent = status.message;
                            averageProgress.className =
                                `absolute left-0 top-0 h-full bg-gradient-to-r ${status.color} transition-all duration-500`;
                        }

                        window.keyDutiesContribution = scaledAvg;
                        updateTotalScore();
                    }
                }

                function initPersonalAttributesTable() {
                    const $table = $('#personal-attributes');

                    function recalc() {
                        let sumAppraisee = 0,
                            sumAppraiser = 0,
                            sumAgreed = 0,
                            rowCount = 0,
                            maxPerRow = 4;

                        $table.find('tbody tr').each(function() {
                            const $row = $(this);
                            // Get values directly from inputs
                            const agreed = parseFloat($row.find('input[name*="[agreed_score]"]').val()) || 0;

                            sumAgreed += agreed;
                            rowCount++;

                            // For display purposes only
                            const a = parseFloat($row.find('input[name*="[appraisee_score]"]').val()) || 0;
                            const b = parseFloat($row.find('input[name*="[appraiser_score]"]').val()) || 0;
                            sumAppraisee += a;
                            sumAppraiser += b;
                        });

                        const $tfootTds = $table.find('tfoot tr:first td');
                        $tfootTds.eq(0).text(sumAppraisee.toFixed(2)); // Appraisee total
                        $tfootTds.eq(1).text(sumAppraiser.toFixed(2)); // Appraiser total
                        $tfootTds.eq(2).text(sumAgreed.toFixed(2)); // Agreed total

                        const totalMax = rowCount * maxPerRow;
                        const pct40 = totalMax > 0 ? (sumAgreed / totalMax) * 40 : 0;
                        $('#overall-40pct').text(`${pct40.toFixed(2)}% of 40%`);

                        window.personalAttributesContribution = pct40;
                        updateTotalScore();
                    }

                    // Listen for changes on ALL score input types
                    $table.on('input change', '.score-input', recalc);
                    recalc(); // Initial calculation
                }

                function updateTotalScore() {
                    const total = (window.keyDutiesContribution || 0) +
                        (window.personalAttributesContribution || 0);
                    document.getElementById('totalScore').textContent = `${total.toFixed(1)}%`;
                }

                // 6. Performance Table Auto-Save
                function initPerformanceTableAutoSave() {
                    const wrapper = document.querySelector('#performance-table-wrapper');
                    if (!wrapper) return;

                    const today = new Date().toISOString().split('T')[0];
                    let saveTimeout;

                    wrapper.querySelectorAll('.date-input').forEach(input => {
                        input.setAttribute('min', today);
                    });

                    wrapper.querySelectorAll('[ contenteditable="true"]').forEach(cell => {
                        const placeholder = cell.getAttribute('data-placeholder');
                        if (!cell.textContent.trim()) {
                            cell.textContent = placeholder;
                            cell.classList.add('text-muted');
                        }

                        cell.addEventListener('input', autoSave);
                        cell.addEventListener('focus', handleCellFocus);
                        cell.addEventListener('blur', handleCellBlur);
                    });

                    wrapper.querySelectorAll('.date-input').forEach(input => {
                        input.addEventListener('change', autoSave);
                    });

                    loadSavedData();

                    function autoSave() {
                        clearTimeout(saveTimeout);
                        saveTimeout = setTimeout(() => {
                            const data = [];
                            wrapper.querySelectorAll('tbody tr').forEach(row => {
                                data.push({
                                    keyOutput: row.querySelector('.editable-cell:nth-child(2)')
                                        .textContent.trim(),
                                    targets: row.querySelector('.editable-cell:nth-child(3)')
                                        .textContent.trim(),
                                    date: row.querySelector('input.date-input').value
                                });
                            });
                            localStorage.setItem('performanceData', JSON.stringify(data));
                            showStatus('Auto-saved successfully!', 'success');
                        }, 1000);
                    }

                    function handleCellFocus(e) {
                        const cell = e.target;
                        const placeholder = cell.getAttribute('data-placeholder')?.trim() || '';

                        // Remove muted style
                        cell.classList.remove('text-muted');

                        // Clear placeholder if it matches
                        if (cell.textContent.trim() === placeholder) {
                            cell.textContent = '';
                        }

                        // Add editing class
                        cell.classList.add('editing');
                    }


                    function handleCellBlur(e) {
                        const cell = e.target;
                        cell.classList.remove('editing');
                        if (!cell.textContent.trim()) {
                            cell.textContent = cell.getAttribute('data-placeholder');
                            cell.classList.add('text-muted');
                        }
                    }

                    function loadSavedData() {
                        const savedData = localStorage.getItem('performanceData');
                        if (savedData) {
                            JSON.parse(savedData).forEach((rowData, index) => {
                                const row = wrapper.querySelector(`tbody tr:nth-child(${index + 1})`);
                                if (!row) return;

                                const keyOutput = row.querySelector('.editable-cell:nth-child(2)');
                                const targets = row.querySelector('.editable-cell:nth-child(3)');
                                const date = row.querySelector('input.date-input');

                                if (keyOutput) keyOutput.textContent = rowData.keyOutput;
                                if (targets) targets.textContent = rowData.targets;
                                if (date) date.value = rowData.date;
                            });
                        }
                    }
                }

                // Utility Functions
                function showToast(message) {
                    Toastify({
                        text: message,
                        duration: 3000,
                        gravity: "top",
                        position: "right",
                        backgroundColor: "linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(121,14,9,1) 35%, rgba(0,212,255,1) 100%)"
                    }).showToast();
                }

                function showStatus(message, type = 'success') {
                    const status = document.createElement('div');
                    status.className = `status-message alert alert-${type}`;
                    status.textContent = message;
                    document.body.appendChild(status);

                    setTimeout(() => status.remove(), 2000);
                }
            });
        </script>
    @endpush
</x-app-layout>
