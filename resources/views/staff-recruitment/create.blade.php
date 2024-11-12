<x-app-layout>
    <h5 class="text-center mt-5">STAFF RECRUITMENT REQUEST FORM</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('recruitments.store') }}" accept-charset="UTF-8" class="form-horizontal"
            enctype="multipart/form-data">
            @csrf
            @include ('staff-recruitment.form', ['formMode' => 'create'])
        </form>
    </div>
</x-app-layout>
