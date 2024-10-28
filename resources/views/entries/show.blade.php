<x-app-layout>
    <div class="py-12">
        @foreach ($entry->formatted_responses as $key => $field)
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg transition-transform transform hover:scale-105">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-4 d-flex flex-column">
                            {{-- Check if there are no answers and style the question accordingly --}}
                            <h6 class="font-bold text-lg {{ empty($field) ? 'text-red-600' : 'text-gray-800' }}">
                                Question {{ $loop->iteration }}: {{ $key }}
                                @if (empty($field))
                                    <span class="text-red-500"> - No answer</span>
                                @endif
                            </h6>

                            @if (is_array($field))
                                {{-- Handle the case where $field is an array with special styling for answers --}}
                                <ul class="list-disc pl-5 mt-2">
                                    @foreach ($field as $item)
                                        @if (is_array($item))
                                            <table class="min-w-full border-collapse border border-gray-300">
                                                <tbody>
                                                    <tr class="border-b border-gray-300">
                                                        @foreach ($item as $exp)
                                                            <td class="px-4 py-2">{{ $exp }}</td>
                                                        @endforeach
                                                    </tr>
                                                </tbody>
                                            </table>
                                        @else
                                            <li class="text-blue-600 mt-2">{{ $item }}</li>
                                        @endif
                                    @endforeach
                                </ul>
                            @else
                                {{-- Handle the case where $field is not an array with special styling for answers --}}
                                <p class="text-green-600 mt-2">{{ $field }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
