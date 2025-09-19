<x-app-layout>
    <h5 class="text-center mt-5">Edit Leave</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('leaves.update', ['leave' => $leave->leave_id]) }}" accept-charset="UTF-8"
            class="form-horizontal" enctype="multipart/form-data" id="leaveForm">
            @method('PUT')
            @csrf
            @include ('leaves.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
