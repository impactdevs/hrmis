<x-app-layout>
    <div class="mt-3">
        <form method="POST" action="{{ route('salary-advances.update', $salary_advance->id) }}" accept-charset="UTF-8"
            class="form-horizontal" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            @include ('salary-advances.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
