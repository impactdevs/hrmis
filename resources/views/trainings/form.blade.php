<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="training_start_date" label="Training Start Date" type="date" id="training_start_date"
            value="{{ old('training_start_date', isset($training) && $training->training_start_date ? $training->training_start_date->toDateString() : '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="training_end_date" label="Training End Date" type="date" id="training_end_date"
            value="{{ old('training_end_date', isset($training) && $training->training_end_date ? $training->training_end_date->toDateString() : '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="training_location" label="Training Location" type="text" id="training_location"
            value="{{ old('training_title', $training->training_location ?? '') }}" />
    </div>
</div>
<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="training_title" label="Training Title" type="text" id="training_title"
            placeholder="Enter Training Title" value="{{ old('training_title', $training->training_title ?? '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.text-area name="training_description" label="Training Description" id="training_description"
            :value="old('training_description', $training->training_description ?? '')" />
    </div>
</div>
<div class="border row border-5 border-success">
    <p>Select what user categories should be attached to this training</p>

    <div class="mb-3 col">
        <label for="usertokenfield" class="form-label">Participants</label>
        <input type="text" class="form-control" id="usertokenfield" />
        <input type="hidden" name="training_category[users]" id="user_ids"
            value="{{ old('training_category.users', isset($training) ? (isset($training->training_category['users']) ? $training->training_category['users'] : 'All') : 'All') }}" />
    </div>

    <div class="mb-3 col">
        <label for="departmenttokenfield" class="form-label">Departments</label>
        <input type="text" class="form-control" id="departmenttokenfield" />
        <input type="hidden" name="training_category[departments]" id="department_ids"
            value="{{ old('training_category.departments', isset($training) ? (isset($training->training_category['departments']) ? $training->training_category['departments'] : '') : '') }}" />
    </div>

    <div class="mb-3 col">
        <label for="positiontokenfield" class="form-label">Positions</label>
        <input type="text" class="form-control" id="positiontokenfield" />
        <input type="hidden" name="training_category[positions]" id="position_ids"
            value="{{ old('training_category.positions', isset($training) ? (isset($training->training_category['positions']) ? $training->training_category['positions'] : '') : '') }}" />
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

            // Initialize User Tokenfield
            $('#usertokenfield').tokenfield({
                    autocomplete: {
                        source: userSource.map(user => user.name),
                        delay: 100
                    },
                    showAutocompleteOnFocus: true
                })
                .on('tokenfield:createtoken', function(event) {
                    const tokenValue = event.attrs.value;
                    let userId;

                    if (tokenValue === 'All Users') {
                        userId = 'All';
                    } else {
                        const user = userSource.find(u => u.name === tokenValue);
                        userId = user ? user.id : null;
                    }

                    if (userId) {
                        let currentIds = $('#user_ids').val().split(',').filter(Boolean);

                        if (userId === 'All') {
                            currentIds = ['All'];
                        } else {
                            currentIds = currentIds.filter(id => id !== 'All');
                            if (!currentIds.includes(userId)) {
                                currentIds.push(userId);
                            }
                        }

                        $('#user_ids').val(currentIds.join(','));
                    } else {
                        event.preventDefault();
                    }
                })
                .on('tokenfield:removedtoken', function(e) {
                    const tokenValue = e.attrs.value;
                    let userId;

                    if (tokenValue === 'All Users') {
                        userId = 'All';
                    } else {
                        const user = userSource.find(u => u.name === tokenValue);
                        userId = user ? user.id : null;
                    }

                    if (userId) {
                        let currentIds = $('#user_ids').val().split(',').filter(Boolean);
                        currentIds = currentIds.filter(id => id !== userId);
                        $('#user_ids').val(currentIds.join(','));
                    }
                });

            // Initialize Department Tokenfield
            $('#departmenttokenfield').tokenfield({
                    autocomplete: {
                        source: departmentSource.map(d => d.department_name),
                        delay: 100
                    },
                    showAutocompleteOnFocus: true
                })
                .on('tokenfield:createtoken', function(event) {
                    const tokenValue = event.attrs.value;
                    const department = departmentSource.find(d => d.department_name === tokenValue);
                    if (department) {
                        const currentIds = $('#department_ids').val().split(',').filter(Boolean);
                        currentIds.push(department.department_id);
                        $('#department_ids').val(currentIds.join(','));
                    }
                })
                .on('tokenfield:removedtoken', function(e) {
                    const tokenValue = e.attrs.value;
                    const department = departmentSource.find(d => d.department_name === tokenValue);
                    if (department) {
                        let currentIds = $('#department_ids').val().split(',').filter(Boolean);
                        currentIds = currentIds.filter(id => id !== department.department_id);
                        $('#department_ids').val(currentIds.join(','));
                    }
                });

            // Initialize Position Tokenfield
            $('#positiontokenfield').tokenfield({
                    autocomplete: {
                        source: positionSource.map(p => p.position_name),
                        delay: 100
                    },
                    showAutocompleteOnFocus: true
                })
                .on('tokenfield:createtoken', function(event) {
                    const tokenValue = event.attrs.value;
                    const position = positionSource.find(p => p.position_name === tokenValue);
                    if (position) {
                        const currentIds = $('#position_ids').val().split(',').filter(Boolean);
                        currentIds.push(position.position_id);
                        $('#position_ids').val(currentIds.join(','));
                    }
                })
                .on('tokenfield:removedtoken', function(e) {
                    const tokenValue = e.attrs.value;
                    const position = positionSource.find(p => p.position_name === tokenValue);
                    if (position) {
                        let currentIds = $('#position_ids').val().split(',').filter(Boolean);
                        currentIds = currentIds.filter(id => id !== position.position_id);
                        $('#position_ids').val(currentIds.join(','));
                    }
                });

            // Populate initial tokens for Users
            const initialUserIds = $('#user_ids').val().split(',').filter(Boolean);
            initialUserIds.forEach(id => {
                if (id === 'All') {
                    $('#usertokenfield').tokenfield('createToken', 'All Users');
                } else {
                    const user = userSource.find(u => u.id === id);
                    if (user) {
                        $('#usertokenfield').tokenfield('createToken', user.name);
                    }
                }
            });

            // Populate initial tokens for Departments
            const initialDeptIds = $('#department_ids').val().split(',').filter(Boolean);
            initialDeptIds.forEach(id => {
                const dept = departmentSource.find(d => d.department_id === id);
                if (dept) {
                    $('#departmenttokenfield').tokenfield('createToken', dept.department_name);
                }
            });

            // Populate initial tokens for Positions
            const initialPosIds = $('#position_ids').val().split(',').filter(Boolean);
            initialPosIds.forEach(id => {
                const pos = positionSource.find(p => p.position_id === id);
                if (pos) {
                    $('#positiontokenfield').tokenfield('createToken', pos.position_name);
                }
            });
        });
    </script>
@endpush
