<x-app-layout>
    <h5 class="text-center mt-5">Edit Request</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('out-of-station-trainings.update', $training->training_id) }}"
            accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            @include ('out-of-station-training.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
