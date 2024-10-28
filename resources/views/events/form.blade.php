<div class="row mb-3">
    <div class="col-md-6">
        <x-forms.input name="event_start_date" label="Event Start Date" type="date" id="event_start_date" placeholder="Start date"
            value="{{ old('event_start_date', isset($event) && $event->event_start_date ? $event->event_start_date->toDateString() : '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="event_end_date" label="Event End Date" type="date" id="event_end_date" placeholder="End Date"
            value="{{ old('event_end_date', isset($event) && $event->event_end_date ? $event->event_end_date->toDateString() : '') }}" />
    </div>
</div>
<div class="row mb-3">
    <div class="col-md-6">
        <x-forms.input name="event_title" label="Event Title" type="text" id="event_title"
            placeholder="Enter Event Title" value="{{ old('event_title', $event->event_title ?? '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.text-area name="event_description" label="Event Description" id="event_description"
            :value="old('event_description', $event->event_description ?? '')" />
    </div>
</div>

<div class="row border border-5 border-success">
    <p>Select what user categories should be attached to this Event</p>

    <div class="mb-3 col">
        <label for="usertokenfield" class="form-label">Users</label>
        <input type="text" class="form-control" id="usertokenfield" />
        <input type="hidden" name="category[users]" id="user_ids" />
    </div>

    <div class="mb-3 col">
        <label for="departmenttokenfield" class="form-label">Departments</label>
        <input type="text" class="form-control" id="departmenttokenfield" />
        <input type="hidden" name="category[departments]" id="department_ids" />
    </div>

    <div class="mb-3 col">
        <label for="positiontokenfield" class="form-label">Positions</label>
        <input type="text" class="form-control" id="positiontokenfield" />
        <input type="hidden" name="category[positions]" id="position_ids" />
    </div>
</div>

<div class="form-group">
    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Create' }}">
</div>

@push('scripts')
    <script>
        $(document).ready(function() {
            const users = @json($users);
            const departments = @json($departments);
            const positions = @json($positions);

            const userSource = Object.entries(users).map(([id, name]) => ({
                id,
                name
            }));
            const departmentSource = Object.entries(departments).map(([department_id, department_name]) => ({
                department_id,
                department_name
            }));
            const positionSource = Object.entries(positions).map(([position_id, position_name]) => ({
                position_id,
                position_name
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

            // Departments Tokenfield
            $('#departmenttokenfield').tokenfield({
                autocomplete: {
                    source: departmentSource.map(department => department.department_name),
                    delay: 100
                },
                showAutocompleteOnFocus: true
            }).on('tokenfield:createtoken', function(event) {
                const token = event.attrs;
                const departmentId = departmentSource.find(department => department.department_name ===
                    token.value)?.department_id;
                if (departmentId) {
                    const currentIds = $('#department_ids').val().split(',').filter(Boolean);
                    currentIds.push(departmentId);
                    $('#department_ids').val(currentIds.join(','));
                }
            });

            // Positions Tokenfield
            $('#positiontokenfield').tokenfield({
                autocomplete: {
                    source: positionSource.map(position => position.position_name),
                    delay: 100
                },
                showAutocompleteOnFocus: true
            }).on('tokenfield:createtoken', function(event) {
                const token = event.attrs;
                const positionId = positionSource.find(position => position.position_name === token.value)
                    ?.position_id;
                if (positionId) {
                    const currentIds = $('#position_ids').val().split(',').filter(Boolean);
                    currentIds.push(positionId);
                    $('#position_ids').val(currentIds.join(','));
                }
            });
        });
    </script>
@endpush
