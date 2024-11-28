<x-app-layout>
    <h5 class="text-center mt-5">OUT OF STATION/EXTERNAL TRAVEL CLEARANCE AND RECORD FORM</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('out-of-station-trainings.store') }}" accept-charset="UTF-8" class="form-horizontal"
            enctype="multipart/form-data">
            @csrf
            @include ('out-of-station-training.form', ['formMode' => 'create'])
        </form>
    </div>
</x-app-layout>
