<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="start_datetime" label="Start Date & Time" type="datetime-local" id="start_datetime"
            value="{{ old('start_datetime', isset($offdesk) && $offdesk->start_datetime ? $offdesk->start_datetime->toDateTimeString() : '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="end_datetime" label="End Date & Time" type="datetime-local" id="end_datetime"
            value="{{ old('end_datetime', isset($offdesk) && $offdesk->end_datetime ? $offdesk->end_datetime->toDateTimeString() : '') }}" />
    </div>
</div>

<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="destination" label="Where Are you going to be(Destination)" type="text" id="destination"
            value="{{ old('destination', $offdesk->destination ?? '') }}" />
    </div>

    <div class="col-md-6">
        <x-forms.input name="duty_allocated" label="Duty Allocated" type="text" id="duty_allocated"
            value="{{ old('duty_allocated', $offdesk->duty_allocated ?? '') }}" />
    </div>

    <div class="col-md-6">
        <x-forms.text-area name="reason" label="Reason for being off desk" id="reason" :value="old('reason', $offdesk->reason ?? '')" />
    </div>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Submit The Request' }}">
</div>
