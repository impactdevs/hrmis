
<x-app-layout>

    <div class="py-12 bg-gray-50 dark:bg-gray-900">
        <div class="max-w-10xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="text-center">
                    <h2 class="text-2xl font-bold text-center mb-6">Click on the <i class="bi bi-eye"></i> to see the
                        Application Details</h2>
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
                                            <a href="{{ route('forms.entries', $form->uuid) }}"
                                                class="text-blue-600 dark:text-blue-400 hover:underline">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ url('forms/survey', [$form->uuid, auth()->user()->id]) }}"
                                                class="text-blue-600 dark:text-blue-400 hover:underline" id="copyLink">
                                                <i class="bi bi-link-45deg"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
