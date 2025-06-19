<x-app-layout>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="mt-3">
        <form method="POST" action="{{ route('departments.update', $department->department_id) }}" accept-charset="UTF-8"
            class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include ('departments.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
