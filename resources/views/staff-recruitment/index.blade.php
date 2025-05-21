<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
            <h1 class="text-2xl font-bold text-gray-900">Recruitment Requests</h1>
            @can('can apply recruitment')
                <a href="{{ route('recruitments.create') }}" class="btn bg-gradient-to-r from-blue-600 to-blue-400 hover:from-blue-700 hover:to-blue-500 text-white font-medium rounded-lg px-5 py-2.5 transition-all">
                    <i class="bi bi-plus-lg mr-2"></i>New Application
                </a>
            @endcan
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6">
                @if ($rectrutmentRequests->isEmpty())
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="mb-4 text-blue-400 text-6xl">
                            <i class="bi bi-folder2-open"></i>
                        </div>
                        <h3 class="text-xl font-medium text-gray-900 mb-2">No recruitment requests found</h3>
                        <p class="text-gray-500">Get started by creating a new recruitment application</p>
                    </div>
                @else
                    <!-- Responsive Table Container -->
                    <div class="overflow-x-auto rounded-lg border border-gray-100">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Position</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Department</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approval Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach ($rectrutmentRequests as $recruitment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $recruitment->position }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $recruitment->department->department_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        @if (!is_null($recruitment->approval_status))
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($recruitment->approval_status as $key => $status)
                                                    <span @class([
                                                        'px-2 py-1 rounded-full text-xs font-medium',
                                                        'bg-green-100 text-green-800' => $status === 'approved',
                                                        'bg-yellow-100 text-yellow-800' => $status === 'pending',
                                                        'bg-red-100 text-red-800' => $status === 'rejected',
                                                    ])>
                                                        {{ $key }}: {{ ucfirst($status) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-600 text-xs font-medium">Pending Review</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-3">
                                            <a href="{{ route('recruitments.show', $recruitment->staff_recruitment_id) }}" 
                                               class="text-blue-600 hover:text-blue-900"
                                               title="View">
                                                <i class="bi bi-eye text-lg"></i>
                                            </a>
                                            <a href="{{ route('recruitments.edit', $recruitment->staff_recruitment_id) }}" 
                                               class="text-gray-600 hover:text-gray-900"
                                               title="Edit">
                                                <i class="bi bi-pencil-square text-lg"></i>
                                            </a>
                                            <form action="{{ route('recruitments.destroy', $recruitment->staff_recruitment_id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this application?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                    <i class="bi bi-trash text-lg"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4 px-4">
                        {{ $rectrutmentRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .bg-gradient-to-r {
            background-image: linear-gradient(to right, var(--tw-gradient-from), var(--tw-gradient-to));
        }
        
        .shadow-lg {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .transition-colors {
            transition: background-color 0.2s ease-in-out;
        }
    </style>
</x-app-layout>