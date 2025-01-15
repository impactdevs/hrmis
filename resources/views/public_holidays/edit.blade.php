<x-app-layout>
    <h5 class="text-center mt-5">Edit Public Holiday</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('public_holidays.update', $holiday->id) }}" accept-charset="UTF-8"
            class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include ('public_holidays.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
