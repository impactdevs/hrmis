<x-app-layout>
    <h5 class="text-center mt-5">Edit Training</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('trainings.update', $training->training_id) }}" accept-charset="UTF-8"
            class="form-horizontal" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            @include ('trainings.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
