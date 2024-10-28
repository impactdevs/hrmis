<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Form Settings') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('form-settings.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <label for="title" class="form-label">Title</label>
                        <select class="form-select form-select-lg mb-3" aria-label="Title" name="title">
                            <option value="">Select title</option> {{-- Default option --}}
                            @foreach ($form->sections as $section)
                                {{-- iterate through all the fields in that section --}}
                                @foreach ($section->fields as $field)
                                    <option value="{{ $field->id }}"
                                        @if (filled($form->setting) && $form->setting->title == $field->id) selected @endif>
                                        {{ $field->label }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>

                        <label for="subtitle" class="form-label">Sub-Title</label>
                        <select class="form-select form-select-lg" aria-label="Sub title" name="subtitle">
                            <option value="">Select sub-title</option> {{-- Default option --}}
                            @foreach ($form->sections as $section)
                                {{-- iterate through all the fields in that section --}}
                                @foreach ($section->fields as $field)
                                    <option value="{{ $field->id }}"
                                        @if (filled($form->setting) && $form->setting->subtitle == $field->id) selected @endif>
                                        {{ $field->label }}
                                    </option>
                                @endforeach
                            @endforeach
                        </select>

                        <input type='hidden' name="form_id" value="{{ $form->uuid }}">
                        <div class="form-check mt-4">
                            @if (filled($form->setting))
                                <input class="form-check-input" type="checkbox" id="published" name="is_published"
                                    value="1" {{ $form->setting->is_published == 1 ? 'checked' : '' }}>
                            @else
                                <input class="form-check input" type="checkbox" id="published" name="is_published"
                                    value="1">
                            @endif
                            <label class="form-check-label" for="published">
                                Published
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
