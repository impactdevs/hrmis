<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Form Builder') }} -> {{ __($form->name) }}
            </h2>

            <a href="{{ route('forms.settings', $form->uuid) }}" class="btn btn-primary">
                <i class="bi bi-gear-fill"></i></i> Settings
            </a>
        </div>
    </x-slot>



    <div class="py-12">
        @if ($form->sections->isEmpty())
            <h1>This form has no form fields</h1>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">


                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSectionModal">
                    <i class="bi bi-plus"></i> Add section
                </button>

            </div>
        @else
            {{-- iterate through sections and create an accordion --}}
            @for ($i = 0; $i < count($form->sections); $i++)
                <div class="sec-sort">
                    {{-- create an accordion for each section --}}
                    <div class="border border-5 border-primary m-5">
                        {{-- section title --}}
                        <div class="m-2 d-flex flex-row justify-between">
                            <p class="h6">
                                <a class="btn btn-primary" data-bs-toggle="collapse" href="#section{{ $i }}"
                                    role="button" aria-expanded="false" aria-controls="section{{ $i }}">
                                    {{ $i + 1 }}.{{ $form->sections[$i]->section_name }}
                                </a>
                            </p>

                            <div class="d-flex justify-content-start align-items-center gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center"
                                    data-bs-toggle="modal" data-bs-target="#editSectionModal"
                                    data-id="{{ $form->sections[$i]->id }}"
                                    data-section-name="{{ $form->sections[$i]->section_name }}"
                                    data-section-description="{{ $form->sections[$i]->section_description }}">
                                    <i class="bi bi-pencil me-1"></i> Edit
                                </button>
                                <form
                                    action="{{ route('sections.destroy', ['form' => $form->id, 'section' => $form->sections[$i]->id]) }}"
                                    method="POST" class="d-inline" id="delete-form-{{ $form->sections[$i]->id }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-outline-danger btn-sm d-flex align-items-center btn-delete"
                                        data-id="{{ $form->sections[$i]->id }}">
                                        <i class="bi bi-trash me-1"></i> Delete
                                    </button>
                                </form>
                            </div>

                        </div>

                        {{-- description --}}
                        <p class="h6">
                            {{ $form->sections[$i]->section_description ?? '' }}
                        </p>

                        {{-- if there are no fields --}}
                        @if ($form->sections[$i]->fields->isEmpty())
                            <div class="m-2">
                                <p>This section has no form fields</p>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#addFieldModal" data-section-id="{{ $form->sections[$i]->id }}">
                                    <i class="bi bi-plus"></i> Add a Field
                                </button>

                            </div>
                        @else
                            <div class="sortable">
                                @foreach ($form->sections[$i]->fields as $key => $field)
                                    @php
                                        $condional_id = null;
                                        $trigger_value = null;
                                        //condional file
                                        if ($field->properties && isset($field->properties[0])) {
                                            $condional_id = $field->properties[0]->conditional_visibility_field_id;

                                            $trigger_value = $field->properties[0]->conditional_visibility_operator;
                                        }
                                    @endphp
                                    <div class="form-group question" id="question_{{ $field->id }}"
                                        data-radio-field="{{ $condional_id }}"
                                        data-trigger-value="{{ $trigger_value }}"
                                        style="@if ($condional_id != null) display:none; @endif">
                                        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
                                            <div
                                                class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                                                <div class="p-6 text-gray-900 dark:text-gray-100">
                                                    @if ($field->type === 'radio')
                                                        <div class="mb-3 d-flex flex-row justify-content-between">
                                                            <label for="{{ $field->id }}"
                                                                class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                                {{ $field->label }}</label>
                                                            <div
                                                                class="d-flex justify-content-start align-items-center gap-2">
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-sm d-flex align-items-center"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editFieldModal" data-mode="edit"
                                                                    data-id="{{ $field->id }}"
                                                                    data-label="{{ $field->label }}"
                                                                    data-type="{{ $field->type }}"
                                                                    data-options="{{ $field->options }}">
                                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                                </button>
                                                                <form
                                                                    action="{{ route('fields.destroy', ['form' => $form->id, 'field' => $field->id]) }}"
                                                                    method="POST" class="d-inline"
                                                                    id="delete-form-{{ $field->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-outline-danger btn-sm d-flex align-items-center btn-delete"
                                                                        data-id="{{ $field->id }}">
                                                                        <i class="bi bi-trash me-1"></i> Delete
                                                                    </button>
                                                                </form>
                                                                <button
                                                                    class="btn btn-outline-secondary btn-sm d-flex align-items-center"
                                                                    type="button" data-bs-toggle="offcanvas"
                                                                    data-bs-target="#offcanvasBottom"
                                                                    data-field-id="{{ $field->id }}"
                                                                    aria-controls="offcanvasBottom">
                                                                    <i class="bi bi-gear me-1"></i> Properties
                                                                </button>
                                                            </div>


                                                        </div>
                                                        @foreach (explode(',', $field->options) as $option)
                                                            <div class="m-4">
                                                                <input type="{{ $field->type }}"
                                                                    id="{{ $field->id }}_{{ $loop->index }}"
                                                                    name="{{ $field->label }}"
                                                                    value="{{ $option }}">
                                                                <label for="{{ $field->id }}_{{ $loop->index }}"
                                                                    class="ml-2">{{ $option }}</label>
                                                            </div>
                                                        @endforeach
                                                    @elseif ($field->type === 'select')
                                                        <div class="mb-3 d-flex flex-column justify-content-between">
                                                            <div class="d-flex flex-row justify-content-between">


                                                                <label for="{{ $field->id }}"
                                                                    class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                                    {{ $field->label }}</label>
                                                                <div
                                                                    class="d-flex justify-content-start align-items-center gap-2">
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm d-flex align-items-center"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editFieldModal"
                                                                        data-mode="edit"
                                                                        data-id="{{ $field->id }}"
                                                                        data-label="{{ $field->label }}"
                                                                        data-type="{{ $field->type }}"
                                                                        data-options="{{ $field->options }}">
                                                                        <i class="bi bi-pencil me-1"></i> Edit
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('fields.destroy', ['form' => $form->id, 'field' => $field->id]) }}"
                                                                        method="POST" class="d-inline"
                                                                        id="delete-form-{{ $field->id }}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-outline-danger btn-sm d-flex align-items-center btn-delete"
                                                                            data-id="{{ $field->id }}">
                                                                            <i class="bi bi-trash me-1"></i> Delete
                                                                        </button>
                                                                    </form>
                                                                    <button
                                                                        class="btn btn-outline-secondary btn-sm d-flex align-items-center"
                                                                        type="button" data-bs-toggle="offcanvas"
                                                                        data-bs-target="#offcanvasBottom"
                                                                        data-field-id="{{ $field->id }}"
                                                                        aria-controls="offcanvasBottom">
                                                                        <i class="bi bi-gear me-1"></i> Properties
                                                                    </button>
                                                                </div>

                                                            </div>
                                                            {{-- select --}}
                                                            <select name="{{ $field->id }}"
                                                                id="{{ $field->id }}" class="form-select">
                                                                <option value="">-- Select --</option>
                                                                @foreach (explode(',', $field->options) as $option)
                                                                    <option value="{{ $option }}">
                                                                        {{ $option }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    @elseif ($field->type === 'checkbox')
                                                        <div class="mb-3 d-flex flex-row justify-content-between">
                                                            <label for="{{ $field->id }}"
                                                                class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                                {{ $field->label }}</label>
                                                            <div
                                                                class="d-flex justify-content-start align-items-center gap-2">
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-sm d-flex align-items-center"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editFieldModal" data-mode="edit"
                                                                    data-id="{{ $field->id }}"
                                                                    data-label="{{ $field->label }}"
                                                                    data-type="{{ $field->type }}"
                                                                    data-options="{{ $field->options }}">
                                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                                </button>
                                                                <form
                                                                    action="{{ route('fields.destroy', ['form' => $form->id, 'field' => $field->id]) }}"
                                                                    method="POST" class="d-inline"
                                                                    id="delete-form-{{ $field->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-outline-danger btn-sm d-flex align-items-center btn-delete"
                                                                        data-id="{{ $field->id }}">
                                                                        <i class="bi bi-trash me-1"></i> Delete
                                                                    </button>
                                                                </form>
                                                                <button
                                                                    class="btn btn-outline-secondary btn-sm d-flex align-items-center"
                                                                    type="button" data-bs-toggle="offcanvas"
                                                                    data-bs-target="#offcanvasBottom"
                                                                    data-field-id="{{ $field->id }}"
                                                                    aria-controls="offcanvasBottom">
                                                                    <i class="bi bi-gear me-1"></i> Properties
                                                                </button>
                                                            </div>


                                                        </div>
                                                        @foreach (explode(',', $field->options) as $option)
                                                            <div class="m-4">
                                                                <input type="{{ $field->type }}"
                                                                    id="{{ $field->id }}_{{ $loop->index }}"
                                                                    name="{{ $option }}"
                                                                    value="{{ $option }}">
                                                                <label for="{{ $field->id }}_{{ $loop->index }}"
                                                                    class="ml-2">{{ $option }}</label>
                                                            </div>
                                                        @endforeach
                                                    @elseif ($field->type === 'textarea')
                                                        <div class="mb-3 d-flex flex-column justify-content-between">
                                                            <div class="d-flex justify-content-between mb-2">
                                                                <label for="{{ $field->id }}"
                                                                    class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                                    {{ $field->label }}</label>
                                                                <div
                                                                    class="d-flex justify-content-start align-items-center gap-2">
                                                                    <button type="button"
                                                                        class="btn btn-outline-primary btn-sm d-flex align-items-center"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#editFieldModal"
                                                                        data-mode="edit"
                                                                        data-id="{{ $field->id }}"
                                                                        data-label="{{ $field->label }}"
                                                                        data-type="{{ $field->type }}"
                                                                        data-options="{{ $field->options }}">
                                                                        <i class="bi bi-pencil me-1"></i> Edit
                                                                    </button>
                                                                    <form
                                                                        action="{{ route('fields.destroy', ['form' => $form->id, 'field' => $field->id]) }}"
                                                                        method="POST" class="d-inline"
                                                                        id="delete-form-{{ $field->id }}">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit"
                                                                            class="btn btn-outline-danger btn-sm d-flex align-items-center btn-delete"
                                                                            data-id="{{ $field->id }}">
                                                                            <i class="bi bi-trash me-1"></i> Delete
                                                                        </button>
                                                                    </form>
                                                                    <button
                                                                        class="btn btn-outline-secondary btn-sm d-flex align-items-center"
                                                                        type="button" data-bs-toggle="offcanvas"
                                                                        data-bs-target="#offcanvasBottom"
                                                                        data-field-id="{{ $field->id }}"
                                                                        aria-controls="offcanvasBottom">
                                                                        <i class="bi bi-gear me-1"></i> Properties
                                                                    </button>
                                                                </div>

                                                            </div>

                                                            <textarea id="{{ $field->id }}" name="{{ $field->id }}"></textarea>

                                                        </div>
                                                    @elseif ($field->type === 'repeater')
                                                        <div class="mb-3 d-flex flex-row justify-content-between">
                                                            <label for="{{ $field->id }}"
                                                                class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                                {{ $field->label }}</label>
                                                            <div
                                                                class="d-flex justify-content-start align-items-center gap-2">
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-sm d-flex align-items-center"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editFieldModal" data-mode="edit"
                                                                    data-id="{{ $field->id }}"
                                                                    data-label="{{ $field->label }}"
                                                                    data-type="{{ $field->type }}"
                                                                    data-options="{{ $field->options }}">
                                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                                </button>
                                                                <form
                                                                    action="{{ route('fields.destroy', ['form' => $form->id, 'field' => $field->id]) }}"
                                                                    method="POST" class="d-inline"
                                                                    id="delete-form-{{ $field->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-outline-danger btn-sm d-flex align-items-center btn-delete"
                                                                        data-id="{{ $field->id }}">
                                                                        <i class="bi bi-trash me-1"></i> Delete
                                                                    </button>
                                                                </form>
                                                                <button
                                                                    class="btn btn-outline-secondary btn-sm d-flex align-items-center"
                                                                    type="button" data-bs-toggle="offcanvas"
                                                                    data-bs-target="#offcanvasBottom"
                                                                    data-field-id="{{ $field->id }}"
                                                                    aria-controls="offcanvasBottom">
                                                                    <i class="bi bi-gear me-1"></i> Properties
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <table id="repeater-container" class="table">
                                                            <thead>
                                                                <tr>
                                                                    @foreach ($field->repeater_options as $option)
                                                                        <th>{{ $option['field'] }}</th>
                                                                    @endforeach
                                                                    <th>Action</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr class="repeater" data-index="0">
                                                                    @foreach ($field->repeater_options as $option)
                                                                        <td><input type="{{ $option['type'] }}"
                                                                                name="[{{ $field->id }}]{{ $option['field'] }}"
                                                                                required></td>
                                                                    @endforeach
                                                                    <td><span class="remove-btn">Remove</span></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <button type="button" id="add-repeater">Add More</button>
                                                    @else
                                                        <div class="mb-3 d-flex flex-row justify-content-between">
                                                            <label for="{{ $field->id }}"
                                                                class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                                {{ $field->label }}</label>
                                                            <div
                                                                class="d-flex justify-content-start align-items-center gap-2">
                                                                <button type="button"
                                                                    class="btn btn-outline-primary btn-sm d-flex align-items-center"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editFieldModal" data-mode="edit"
                                                                    data-id="{{ $field->id }}"
                                                                    data-label="{{ $field->label }}"
                                                                    data-type="{{ $field->type }}"
                                                                    data-options="{{ $field->options }}">
                                                                    <i class="bi bi-pencil me-1"></i> Edit
                                                                </button>
                                                                <form
                                                                    action="{{ route('fields.destroy', ['form' => $form->id, 'field' => $field->id]) }}"
                                                                    method="POST" class="d-inline"
                                                                    id="delete-form-{{ $field->id }}">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit"
                                                                        class="btn btn-outline-danger btn-sm d-flex align-items-center btn-delete"
                                                                        data-id="{{ $field->id }}">
                                                                        <i class="bi bi-trash me-1"></i> Delete
                                                                    </button>
                                                                </form>
                                                                <button
                                                                    class="btn btn-outline-secondary btn-sm d-flex align-items-center"
                                                                    type="button" data-bs-toggle="offcanvas"
                                                                    data-bs-target="#offcanvasBottom"
                                                                    data-field-id="{{ $field->id }}"
                                                                    aria-controls="offcanvasBottom">
                                                                    <i class="bi bi-gear me-1"></i> Properties
                                                                </button>
                                                            </div>

                                                        </div>
                                                    @endif


                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="max-w-7xl
                                mx-auto sm:px-6 lg:px-8 pt-3">
                                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                                    <div class="p-6 text-gray-900 dark:text-gray-100">
                                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                            data-bs-target="#addFieldModal"
                                            data-section-id="{{ $form->sections[$i]->id }}">
                                            <i class="bi bi-plus"></i> Add a Field
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endfor
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
                <div class="overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#addSectionModal">
                            <i class="bi bi-plus"></i> Add section
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>

    @push('script')
        <script>
            $(function() {
                $(".sortable").sortable({
                    stop: function() {
                        console.log("re-arranged.....")
                    }
                });
                //on dropping sortable item, make an update

                $(".sec-sort").sortable();
            });
        </script>
    @endpush
</x-app-layout>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasBottom" aria-labelledby="offcanvasBottomLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasBottomLabel">Question Properties</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body small">
        <form action="{{ route('fields.add-condition') }}" method="POST">
            @csrf
            <input type="hidden" name="field_id" id="field_id" value="">
            <div class="mb-3">
                <label for="conditional_field">Show this question if the answer to:</label>
                <select name="conditional_visibility_field_id" id="conditional_field" class="form-select">
                    <option value="">-- Select Field --</option>
                    @for ($i = 0; $i < count($form->sections); $i++)
                        @foreach ($form->sections[$i]->fields as $key => $field)
                            @if ($field->type === 'radio')
                                <option value="{{ $field->id }}">{{ $i + 1 }}.{{ $key + 1 }}.
                                    {{ $field->label }}</option>
                            @endif
                        @endforeach
                    @endfor
                </select>
            </div>
            <div class="mb-3">
                <label for="conditional_value">Is equal to:</label>
                <select name="conditional_visibility_operator" id="conditional_value" class="form-select" required>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>



{{-- modal component --}}
<x-section-modal :form="$form" mode="create" />
{{-- set the x-field-modal mode attribute dynamically --}}
<x-section-modal :form="$form" mode="edit" />


{{-- modal component --}}
<x-field-modal :section="1" mode="create" />
{{-- set the x-field-modal mode attribute dynamically --}}
<x-field-modal :section="1" mode="edit" />
