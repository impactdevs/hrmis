<div class="row mb-3">
    <div class="col-md-6">
        <x-forms.input name="start_date" label="Leave Start Date" type="date" id="start_date"
            value="{{ old('start_date', isset($leave) && $leave->start_date ? $leave->start_date->toDateString() : '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="end_date" label="Leave End Date" type="date" id="end_date"
            value="{{ old('end_date', isset($leave) && $leave->end_date ? $leave->end_date->toDateString() : '') }}" />
    </div>
</div>

<x-forms.hidden name="user_id" id="user_id" value="{{ $user_id }}" />

<div class="row mb-3">
    <div class="col-md-6">
        <x-forms.text-area name="reason" label="Reason" id="reason" :value="old('reason', $leave->reason ?? '')" />
    </div>
    <div class="col-md-6">
        <x-forms.dropdown name="leave_type_id" label="Leave Type" id="leave_type_id" :options="$leaveTypes"
            :selected="$leave->leave_type_id ?? ''" />
    </div>
</div>

<div class="row mb-3">
    <div class="col-md-6">
        <x-forms.input-repeater name="my_work_will_be_done_by" label="My Work Will Be Done By" :values="$existingValuesArray" />
    </div>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
