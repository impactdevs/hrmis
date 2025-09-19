<x-app-layout>
    <h5 class="text-center mt-5">Position Type</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('positions.store') }}" accept-charset="UTF-8" class="form-horizontal"
            enctype="multipart/form-data">
            @csrf
            @include ('positions.form', ['formMode' => 'create'])
        </form>
    </div>
</x-app-layout>
