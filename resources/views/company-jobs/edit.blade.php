<x-app-layout>
    <h5 class="text-center mt-5">Edit Job Details</h5>
    <div class="mt-3">
        <form method="POST" action="{{ route('company-jobs.update', $companyJob->company_job_id) }}" accept-charset="UTF-8"
            class="form-horizontal" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @include ('company-jobs.form', ['formMode' => 'edit'])
        </form>
    </div>
</x-app-layout>
