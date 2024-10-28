<div class="row mb-3">
    <div class="col-md-12">
        <x-forms.input name="position_name" label="Position" type="text" id="position_name"
            placeholder="Enter Position" value="{{ old('position_name', $position->position_name ?? '') }}" />
    </div>
</div>
<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
