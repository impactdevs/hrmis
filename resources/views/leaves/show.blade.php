<x-app-layout>
    <div class="container mt-5">
        <h5 class="mb-4 fs-3 text-primary">
            <i class="fas fa-calendar-alt"></i> Leave Details
        </h5>
        <div class="row">
            <div class="col-md-6">

                <div class="form-group mb-3">
                    <label for="leave_start_date">
                        <i class="fas fa-clock"></i> Leave Start Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leaf->start_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="leave_end_date">
                        <i class="fas fa-clock"></i> Leave End Date
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leaf->end_date->toDateString() }}</p>
                </div>
                <div class="form-group mb-3">
                    <label for="leave_description">
                        <i class="fas fa-info-circle"></i> Handover Notes
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leaf->handover_note }}</p>
                    @if ($leaf->handover_note_file)
                        <a href="{{ asset('storage/' . $leaf->handover_note_file) }}" target="_blank"
                            class="btn btn-primary mt-2">View Handover File</a>
                    @endif
                </div>

                <div class="form-group mb-3">
                    <label for="leave_type">
                        <i class="fas fa-info-circle"></i> Leave Type
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leaf->leaveCategory->leave_type_name }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="leave_address">
                        <i class="fas fa-map-marker-alt"></i> Leave Address
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leaf->leave_address }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="contact_number">
                        <i class="fas fa-phone"></i> Contact Number
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leaf->phone_number }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="other_contact_details">
                        <i class="fas fa-info-circle"></i> Other Contact Details
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leaf->other_contact_details }}</p>
                </div>

                <div class="form-group mb-3">
                    <label for="my_work_will_be_done_by">
                        <i class="fas fa-info-circle"></i> My Work Will Be done By:
                    </label>
                    <p class="border p-2 rounded bg-light">{{ $leaf->my_work_will_be_done_by['users'] }}</p>
                </div>
            </div>
        </div>
</x-app-layout>
