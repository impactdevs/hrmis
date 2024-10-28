<x-app-layout>
    <h5 class="text-center mt-5">Edit Employee</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('employees.update', $employee->employee_id) }}" accept-charset="UTF-8"
            class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include ('employees.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
