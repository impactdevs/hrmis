<x-app-layout>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-center mb-6">Click on the <i class="bi bi-eye"></i> to see the
                        form details</h2>
                    <button class="btn btn-primary" type="button" data-bs-toggle="offcanvas"
                        data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom"><i
                            class="bi bi-plus"></i>Create</button>
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    #</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Form Name</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Created At</th>
                                <th scope="col"
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @if (filled($forms))
                                @foreach ($forms as $form)
                                    <tr>
                                        <td
                                            class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100 text-left">
                                            {{ $form->uuid }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-left">
                                            {{ $form->name }}</td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-left">Active
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-300 text-left">
                                            {{ $form->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 text-sm font-medium text-left">
                                            <a href="{{ route('form-builder.show', $form->uuid) }}"
                                                class="text-blue-600 dark:text-blue-400 hover:underline">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="{{ url('forms', $form->uuid) }}" id="copyLink"
                                                class="text-blue-600 dark:text-blue-400 hover:underline">
                                                <i class="bi bi-link-45deg"></i>
                                            </a>
                                            <a href="#" class="text-blue-600 dark:text-blue-400 hover:underline">
                                                <i class="bi bi-qr-code"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="offcanvas offcanvas-end border" tabindex="-1" id="offcanvasBottom"
                        aria-labelledby="offcanvasBottomLabel">
                        <div class="offcanvas-header">
                            <h5 class="offcanvas-title" id="offcanvasBottomLabel">Add A Form</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"
                                aria-label="Close"></button>
                        </div>
                        <div class="offcanvas-body small">
                            <form action="{{ route('form-builder.store') }}" method="POST">
                                @csrf

                                <div class="mb-4">
                                    <label for="form_name"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300">Form
                                        Name</label>
                                    <input type="text" name="name" id="form_name"
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        required>
                                </div>

                                <div class="mt-4">
                                    <button type="submit"
                                        class="inline-flex items-center px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-md shadow-md transition duration-300 ease-in-out">
                                        Add
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


@push('script')
    <script>
        function copyToClipboard(text) {
            var dummy = document.createElement("textarea");
            document.body.appendChild(dummy);
            dummy.value = text;
            dummy.select();
            document.execCommand("copy");
            document.body.removeChild(dummy);
            alert("Copied to clipboard: " + text);
        }
    </script>
@endpush
