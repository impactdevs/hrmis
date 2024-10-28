<div class="py-12">
    @if ($form->sections->isEmpty())
        <h1>This form has no form fields</h1>
    @else
            {{-- Hidden form ID --}}
            <input type="hidden" name="form_id" value="{{ $form->uuid }}">

            {{-- Iterate through sections and create an accordion --}}
            @for ($i = 0; $i < count($form->sections); $i++)
                {{-- Create an accordion for each section --}}
                <div class="border border-5 border-primary">
                    {{-- Section title --}}
                    <div class="m-2 d-flex flex-row justify-content-between">
                        <p class="h6">
                            <a class="btn btn-primary" data-bs-toggle="collapse" href="#section{{ $i }}"
                                role="button" aria-expanded="false" aria-controls="section{{ $i }}">
                                {{ $i + 1 }}.{{ $form->sections[$i]->section_name }}
                            </a>
                        </p>
                    </div>

                    {{-- Description --}}
                    <p class="h6">
                        {{ $form->sections[$i]->section_description ?? '' }}
                    </p>

                    {{-- If there are no fields --}}
                    @if ($form->sections[$i]->fields->isEmpty())
                        <div class="m-2">
                            <p>This section has no form fields</p>
                        </div>
                    @else
                        @foreach ($form->sections[$i]->fields as $key => $field)
                            @php
                                $condional_id = null;
                                $trigger_value = null;
                                // Conditional field
                                if ($field->properties && isset($field->properties[0])) {
                                    $condional_id = $field->properties[0]->conditional_visibility_field_id;
                                    $trigger_value = $field->properties[0]->conditional_visibility_operator;
                                }
                            @endphp
                            <div class="form-group question" id="question_{{ $field->id }}"
                                data-radio-field="{{ $condional_id }}" data-trigger-value="{{ $trigger_value }}"
                               >
                                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
                                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                        <div class="p-6 text-gray-900 dark:text-gray-100">
                                            @if ($field->type === 'radio')
                                                <div class="mb-3 d-flex flex-column justify-content-between">
                                                    <label for="{{ $field->id }}"
                                                        class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                        {{ $field->label }}</label>
                                                </div>
                                                @foreach (explode(',', $field->options) as $option)
                                                <div class="m-4">
                                                    <input type="{{ $field->type }}"
                                                        id="{{ $field->id }}_{{ $loop->index }}"
                                                        name="{{ $field->id }}"
                                                        value="{{ $option }}"
                                                        @if (in_array(trim($option), (array) old($field->id, $formattedResponses[$field->id] ?? []))) checked @endif>
                                                    <label for="{{ $field->id }}_{{ $loop->index }}" class="ml-2">{{ $option }}</label>
                                                </div>
                                            @endforeach


                                            @elseif ($field->type === 'checkbox')
                                            <div class="mb-3 d-flex flex-column justify-content-between">
                                                <label for="{{ $field->id }}" class="form-label">{{ $i + 1 }}.{{ $key + 1 }}. {{ $field->label }}</label>
                                            </div>
                                            @foreach (explode(',', $field->options) as $option)
                                                <div class="m-4">
                                                    <input type="checkbox"
                                                        id="{{ $field->id }}_{{ $loop->index }}"
                                                        name="{{ $field->id }}[]"
                                                        value="{{ $option }}"
                                                        @if (in_array(trim($option), (array) old($field->id, $formattedResponses[$field->id] ?? []))) checked @endif>
                                                    <label for="{{ $field->id }}_{{ $loop->index }}" class="ml-2">{{ $option }}</label>
                                                </div>
                                            @endforeach

                                            @elseif ($field->type === 'textarea')
                                                <div class="mb-3 d-flex flex-column justify-content-between">
                                                    <div class="d-flex justify-content-between mb-2">
                                                        <label for="{{ $field->id }}"
                                                            class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                            {{ $field->label }}</label>
                                                    </div>
                                                    <textarea id="{{ $field->id }}" name="{{ $field->id }}"
                                                        class="form-control ms-2">{{ old($field->id, $formattedResponses[$field->id] ?? '') }}</textarea>
                                                </div>
                                            @else
                                                <div class="mb-3 d-flex flex-column justify-content-between">
                                                    <label for="{{ $field->id }}"
                                                        class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                        {{ $field->label }}</label>
                                                    <input type="{{ $field->type }}" id="{{ $field->id }}"
                                                        name="{{ $field->id }}" class="form-control ms-2"
                                                        value="{{ old($field->id, $formattedResponses[$field->id] ?? '') }}">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            @endfor

            {{-- Submit button --}}
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
                <div class="form-group">
                    <input class="btn btn-primary" type="submit" value="{{ $formMode === 'edit' ? 'Update' : 'Submit' }}">
                </div>
            </div>
    @endif
</div>
