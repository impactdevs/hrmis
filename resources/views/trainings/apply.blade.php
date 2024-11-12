<x-app-layout>
    <h5 class="text-center mt-5">Add a Training</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('save.apply') }}" accept-charset="UTF-8" class="form-horizontal"
            enctype="multipart/form-data">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <x-forms.input name="training_start_date" label="Training Start Date" type="date"
                        id="training_start_date"
                        value="{{ old('training_start_date', isset($training) && $training->training_start_date ? $training->training_start_date->toDateString() : '') }}" />
                </div>
                <div class="col-md-6">
                    <x-forms.input name="training_end_date" label="Training End Date" type="date"
                        id="training_end_date"
                        value="{{ old('training_end_date', isset($training) && $training->training_end_date ? $training->training_end_date->toDateString() : '') }}" />
                </div>
                <div class="col-md-6">
                    <x-forms.input name="training_location" label="Training Location" type="text"
                        id="training_location"
                        value="{{ old('training_title', $training->training_location ?? '') }}" />
                </div>
                <x-forms.hidden name="approval_status" id="approval_status" value="application" />
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <x-forms.input name="training_title" label="Training Title" type="text" id="training_title"
                        placeholder="Enter Training Title"
                        value="{{ old('training_title', $training->training_title ?? '') }}" />
                </div>
                <div class="col-md-6">
                    <x-forms.text-area name="training_description" label="Training Description"
                        id="training_description" :value="old('training_description', $training->training_description ?? '')" />
                </div>
            </div>
            {{-- hidden user_id field --}}
            <x-forms.hidden name="user_id" id="user_id" value="{{ $user_id }}" />

            <div class="form-group">
                <input class="btn btn-primary" type="submit" value="Apply">
            </div>
        </form>
    </div>
</x-app-layout>
