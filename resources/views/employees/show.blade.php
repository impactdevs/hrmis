<x-app-layout>
    <div class="container">
        <h1 class="mb-4">Employee Details</h1>

        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h2>{{ $employee->title }} {{ $employee->first_name }} {{ $employee->last_name }}</h2>
                </div>
                <div>
                    @if ($employee->passport_photo)
                        <img src="{{ asset('storage/' . $employee->passport_photo) }}" alt="Passport Photo"
                            class="img-thumbnail" width="100">
                    @else
                        <span>No passport photo available.</span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <h5>Basic Information</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Staff ID:</strong> {{ $employee->staff_id }}</li>
                    <li class="list-group-item"><strong>Position:</strong>
                        {{ optional($employee->position)->position_name }}</li>
                    <li class="list-group-item"><strong>NIN:</strong> {{ $employee->nin }}</li>
                    <li class="list-group-item"><strong>Date of Entry:</strong> {{ $employee->date_of_entry }}</li>
                    <li class="list-group-item"><strong>Contract Expiry Date:</strong>
                        {{ $employee->contract_expiry_date }}</li>
                </ul>

                <h5>Department Information</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Department:</strong>
                        {{ optional($employee->department)->department_name }}</li>
                    <li class="list-group-item"><strong>NSSF No:</strong> {{ $employee->nssf_no }}</li>
                    <li class="list-group-item"><strong>Home District:</strong> {{ $employee->home_district }}</li>
                </ul>

                <h5>Contact Information</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>TIN No:</strong> {{ $employee->tin_no }}</li>
                    <li class="list-group-item"><strong>Email:</strong> {{ $employee->email }}</li>
                    <li class="list-group-item"><strong>Phone Number:</strong> {{ $employee->phone_number }}</li>
                    <li class="list-group-item"><strong>Next of Kin:</strong> {{ $employee->next_of_kin }}</li>
                    <li class="list-group-item"><strong>Date of Birth:</strong> {{ $employee->date_of_birth }}</li>
                </ul>

                <h5>Job Information</h5>
                <ul class="list-group mb-3">
                    <li class="list-group-item"><strong>Job Description:</strong> {{ $employee->job_description }}</li>
                </ul>

                <h5>Qualifications</h5>
                <ul class="list-group">
                    @foreach ($employee->qualifications_details as $item)
                        @if (isset($item['proof']) && isset($item['title']))
                            <li class="list-group-item">
                                <strong>Qualification:</strong> {{ $item['title'] }}
                                <img src="{{ asset('storage/' . $item['proof']) }}" alt="Qualification Proof"
                                    class="img-thumbnail" width="100" style="margin-left: 10px;">
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>

        <a href="{{ route('employees.index') }}" class="btn btn-primary mt-3">Back to Employee List</a>
    </div>
</x-app-layout>
