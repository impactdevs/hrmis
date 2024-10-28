<x-app-layout>
    <h5 class="text-center mt-5">Add Department</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('departments.store') }}" accept-charset="UTF-8" class="form-horizontal"
            enctype="multipart/form-data">
            @csrf
            @include ('departments.form', ['formMode' => 'create'])
        </form>
    </div>
</x-app-layout>
