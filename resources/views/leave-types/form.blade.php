<div class="row mb-3">
    <div class="col-md-12">
        <x-forms.input name="leave_type_name" label="Leave Type" type="text" id="leave_type_name"
            placeholder="Enter Leave Type" value="{{ old('leave_type_name', $leaveType->leave_type_name ?? '') }}" />
    </div>
</div>
<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
