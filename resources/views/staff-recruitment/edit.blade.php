<x-app-layout>
    <h5 class="text-center mt-5">STAFF RECRUITMENT REQUEST FORM</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('recruitments.update', $staffRecruitment->staff_recruitment_id) }}"
            accept-charset="UTF-8" class="form-horizontal" enctype="multipart/form-data">
            @method('PUT')
            @csrf
            @include ('staff-recruitment.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
