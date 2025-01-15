<div class="row mb-3">
    <div class="col-md-12">
        <x-forms.input name="holiday_name" label="Public Holiday" type="text" id="holiday_name"
            placeholder="Enter Public Holiday Name" value="{{ old('holiday_name', $holiday->holiday_name ?? '') }}" />
    </div>
    <x-forms.input name="holiday_date" label="Holiday Date" type="date" id="holiday_date"
                    value="{{ old('holiday_date', isset($holiday)?$holiday->holiday_date->toDateString():'') }}" />
</div>
<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>
