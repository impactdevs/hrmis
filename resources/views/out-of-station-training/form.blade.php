<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="destination" label="Destination" type="text" id="destination"
            value="{{ old('destination', $training->destination ?? '') }}" />
    </div>

    <div class="col-md-6">
        <x-forms.input name="travel_purpose" label="Travel Purpose" type="text" id="travel_purpose"
            value="{{ old('travel_purpose', $training->travel_purpose ?? '') }}" />
    </div>
</div>

<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="departure_date" label="Departure Date" type="date" id="departure_date"
            value="{{ old('departure_date', isset($training) && $training->departure_date ? $training->departure_date->toDateString() : '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.input name="return_date" label="Return Date" type="date" id="return_date"
            value="{{ old('return_date', isset($training) && $training->return_date ? $training->return_date->toDateString() : '') }}" />
    </div>
    <div class="col-md-6">
        <x-forms.repeater name="relevant_documents" label="Documents Relevant To This Travel" :values="$training->relevant_documents ?? []" />
    </div>
</div>

<div class="mb-3 row">
    <div class="col-md-6">
        <x-forms.input name="sponsor" label="Sponsor(s)" type="text" id="sponsor"
            value="{{ old('sponsor', $training->sponsor ?? '') }}" />
    </div>


    <div class="mb-3 col">
        <label for="usertokenfield" class="form-label">The following do my work</label>
        <input type="text" class="form-control" id="usertokenfield" />
        <input type="hidden" name="my_work_will_be_done_by[users]" id="user_ids"
            value="{{ old('my_work_will_be_done_by.users', isset($training) ? (isset($training->my_work_will_be_done_by['users']) ? $training->my_work_will_be_done_by['users'] : '') : '') }}" />
    </div>
</div>

<div class="mb-3 row">
    <h5>Contact Address while out of Station:</h5>
    <div class="col-md-6">
        <x-forms.input name="hotel" label="Place of Residence" type="text" id="hotel"
            value="{{ old('hotel', $training->hotel ?? '') }}" />
    </div>

    <div class="col-md-6">
        <x-forms.input name="email" label="Email" type="text" id="email"
            value="{{ old('email', $training->email ?? '') }}" />
    </div>

    <div class="col-md-6">
        <x-forms.input name="tel" label="Tel" type="text" id="tel"
            value="{{ old('tel', $training->tel ?? '') }}" />
    </div>
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
        });
    </script>
@endpush
