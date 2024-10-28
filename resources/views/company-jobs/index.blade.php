<x-app-layout>
    <div class="mt-3">
        <h5 class="text-center mt-5">Company Jobs</h5>
        <div class="mt-3">
            <a href="{{ route('company-jobs.create') }}" class="btn btn-primary">Add Job</a>
        </div>

        <div class="mt-3">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Job Code</th>
                        <td>Role</td>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($companyJobs as $companyJob)
                        <tr>
                            <td>{{ $companyJob->job_code }}</td>
                            <td>{{ $companyJob->job_title }}</td>
                            <td>
                                <a href="{{ route('company-jobs.edit', $companyJob->company_job_id) }}"
                                    class="btn btn-primary">Edit</a>
                                <form method="POST" action="{{ route('company-jobs.destroy', $companyJob->company_job_id) }}"
                                    accept-charset="UTF-8" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm(&quot;Are you sure?&quot;)">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
