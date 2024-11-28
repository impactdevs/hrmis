<div class="row mb-3">
    <div class="col-md-6">
        @if (!isset($leaveRoster))
            <x-forms.input name="start_date" label="Leave Start Date" type="date" id="start_date"
                value="{{ old('start_date', isset($leave) && $leave->start_date ? $leave->start_date->toDateString() : '') }}" />
        @else
            <x-forms.input name="start_date" label="Leave Start Date" type="date" id="start_date"
                value="{{ old('start_date', $leaveRoster->start_date->toDateString()) }}" />
        @endif
    </div>
    <div class="col-md-6">
        @if (!isset($leaveRoster))
            <x-forms.input name="end_date" label="Leave End Date" type="date" id="end_date"
                value="{{ old('end_date', isset($leave) && $leave->end_date ? $leave->end_date->toDateString() : '') }}" />
        @else
            <x-forms.input name="end_date" label="Leave End Date" type="date" id="end_date"
                value="{{ old('end_date', $leaveRoster->end_date->toDateString()) }}" />
        @endif
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

<div class="mb-3 col">
    <label for="usertokenfield" class="form-label">The following do my work</label>
    <input type="text" class="form-control" id="usertokenfield" />
    <input type="hidden" name="my_work_will_be_done_by[users]" id="user_ids" />
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            const users = @json($users);
            const userSource = Object.entries(users).map(([id, name]) => ({
                id,
                name
            }));
            // Users Tokenfield
            $('#usertokenfield').tokenfield({
                autocomplete: {
                    source: userSource.map(user => user.name),
                    delay: 100
                },
                showAutocompleteOnFocus: true
            }).on('tokenfield:createtoken', function(event) {
                const token = event.attrs;
                const userId = userSource.find(user => user.name === token.value)?.id;
                if (userId) {
                    const currentIds = $('#user_ids').val().split(',').filter(Boolean);
                    currentIds.push(userId);
                    $('#user_ids').val(currentIds.join(','));
                }
            });
        });
    </script>
@endpush
