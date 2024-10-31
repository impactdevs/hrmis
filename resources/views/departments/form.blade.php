<div class="row mb-3">
    <div class="col-md-12">
        <x-forms.input name="department_name" label="Department" type="text" id="department_name"
            placeholder="Enter Department Name" value="{{ old('department_name', $department->department_name ?? '') }}" />
    </div>

    <div class="col-md-12">
        <x-forms.dropdown name="department_head" label="Department Head" id="department_head" :options="$users"
            :selected="$department->department_head ?? ''" />
    </div>
</div>
<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
