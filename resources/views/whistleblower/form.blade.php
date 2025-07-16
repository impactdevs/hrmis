@props(['whistleblower' => null, 'evidences' => []])

<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="employee_name" label="Name (optional)" type="text"
            :value="old('employee_name', $whistleblower->employee_name ?? '')" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="employee_email" label="Email (optional)" type="email"
            :value="old('employee_email', $whistleblower->employee_email ?? '')" />
    </div>
</div>

<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="employee_department" label="Department" type="text"
            :value="old('employee_department', $whistleblower->employee_department ?? '')" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="employee_telephone" label="Telephone" type="text"
            :value="old('employee_telephone', $whistleblower->employee_telephone ?? '')" />
    </div>
</div>

<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="job_title" label="Job Title" type="text"
            :value="old('job_title', $whistleblower->job_title ?? '')" />
    </div>
    <div class="col-md-6">
        <x-forms.select name="submission_type" label="Submission Type"
            :options="[
                'Workplace Concern' => 'Workplace Concern',
                'Ethical Misconduct or Violation' => 'Ethical Misconduct or Violation',
                'Harassment or Discrimination' => 'Harassment or Discrimination',
                'Health & Safety Issue' => 'Health & Safety Issue',
                'Suggestion for Improvement' => 'Suggestion for Improvement',
                'Other (Please specify)' => 'Other (Please specify)',
            ]"
            :selected="old('submission_type', $whistleblower->submission_type ?? '')" />
    </div>
</div>

<div class="mb-3 row">
    <div class="col-md-12">
        <x-forms.text-area name="description" label="Description of Concern"
            :value="old('description', $whistleblower->description ?? '')" />
    </div>
</div>

<div class="mb-3 row">
    <div class="col-md-12">
        <x-forms.input name="individuals_involved" label="Individuals Involved" type="text"
            :value="old('individuals_involved', $whistleblower->individuals_involved ?? '')" />
    </div>
</div>

{{-- Evidence inputs with dynamic add/remove --}}
<div class="mb-3">
    <label class="form-label">Evidence</label>
    <table class="table table-bordered" id="evidence-table">
        <thead>
            <tr>
                <th>Witness Name</th>
                <th>Email</th>
                <th>Document</th>
                <th style="width: 100px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @php
                $oldNames = old('evidence_name', []);
                $oldEmails = old('evidence_email', []);
            @endphp

            @if(count($oldNames) > 0)
                @foreach($oldNames as $i => $name)
                    <tr>
                        <td><input type="text" name="evidence_name[]" class="form-control" value="{{ $name }}"></td>
                        <td><input type="email" name="evidence_email[]" class="form-control" value="{{ $oldEmails[$i] ?? '' }}"></td>
                        <td><input type="file" name="evidence_document[]" class="form-control"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                    </tr>
                @endforeach
            @elseif(isset($evidences) && count($evidences) > 0)
                @foreach($evidences as $evidence)
                    <tr>
                        <td><input type="text" name="witness_name[]" class="form-control" value="{{ $evidence->witness_name }}"></td>
                        <td><input type="email" name="email[]" class="form-control" value="{{ $evidence->email }}"></td>
                        <td>
                            <input type="file" name="document[]" class="form-control">
                            @if($evidence->document)
                                <small>Existing: <a href="{{ asset('storage/' . $evidence->document) }}" target="_blank">View</a></small>
                            @endif
                        </td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td><input type="text" name="witness_name[]" class="form-control"></td>
                    <td><input type="email" name="email[]" class="form-control"></td>
                    <td><input type="file" name="document[]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                </tr>
            @endif
        </tbody>
    </table>
    <button type="button" class="btn btn-primary" id="add-evidence">Add Evidence</button>
</div>

<div class="mb-3 row">
    <label class="form-label">Has the issue been reported?</label>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="issue_reported" id="reported_yes" value="Yes" {{ old('issue_reported', $whistleblower->issue_reported ?? '') == 'Yes' ? 'checked' : '' }}>
        <label class="form-check-label" for="reported_yes">Yes</label>
    </div>
    <div class="form-check form-check-inline">
        <input class="form-check-input" type="radio" name="issue_reported" id="reported_no" value="No" {{ old('issue_reported', $whistleblower->issue_reported ?? '') == 'No' ? 'checked' : '' }}>
        <label class="form-check-label" for="reported_no">No</label>
    </div>
</div>

<div class="mb-3 form-check">
    <input type="checkbox" name="confidentiality_statement" class="form-check-input" id="confidentiality_statement" value="agreed" {{ old('confidentiality_statement', $whistleblower->confidentiality_statement ?? '') == 'agreed' ? 'checked' : '' }} required>
    <label class="form-check-label" for="confidentiality_statement">
        I agree to the confidentiality statement.
    </label>
</div>
