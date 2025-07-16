<x-app-layout>
    

    <div class="container mt-4">
        <form method="POST" action="{{ route('whistleblower.store') }}" enctype="multipart/form-data">
            @csrf
            @include('whistleblower.form', ['formMode' => 'create'])
            <div class="form-group">
                <button type="submit" class="btn btn-success">Submit Report</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addBtn = document.getElementById('add-evidence');
            const tableBody = document.querySelector('#evidence-table tbody');

            addBtn.addEventListener('click', () => {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td><input type="text" name="evidence_name[]" class="form-control"></td>
                    <td><input type="email" name="evidence_email[]" class="form-control"></td>
                    <td><input type="file" name="evidence_document[]" class="form-control"></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-row">Remove</button></td>
                `;
                tableBody.appendChild(newRow);
            });

            tableBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-row')) {
                    e.target.closest('tr').remove();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
