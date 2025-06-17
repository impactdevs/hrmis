<x-app-layout>
    <div class="mt-3">
        <form method="POST" action="{{ route('salary-advances.store') }}" accept-charset="UTF-8" class="form-horizontal"
            enctype="multipart/form-data">
            @csrf
            @include ('salary-advances.form', ['formMode' => 'create'])
        </form>
    </div>
</x-app-layout>
