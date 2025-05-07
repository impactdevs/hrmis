 <div class="py-12">
     @if ($form->sections->isEmpty())
         <h1>This form has no form fields</h1>
     @else
         @if (isset($company_jobs))
         <div class="row mb-3">
            <div class="border border-primary p-3 rounded col-12">
                <label class="form-label">Which role are you applying for?</label>
                <select class="form-select" aria-label="Job" name="company_job_id">
                    @foreach ($company_jobs as $job)
                        <option value="{{ $job->company_job_id }}">
                            {{ $job->job_code . '-' . $job->job_title }}
                        </option>
                    @endforeach
                </select>
            </div>
         </div>

         @endif


         {{-- iterate through sections and create an accordion --}}
         @for ($i = 0; $i < count($form->sections); $i++)
             {{-- hidden user_id field --}}
             {{-- create an accordion for each section --}}
             <div class="row">
                 <div class="border border-5 border-primary col-12">
                     {{-- section title --}}
                     <div class="m-2 d-flex flex-row justify-between">
                         <p class="h6">
                             <a class="btn btn-primary" data-bs-toggle="collapse" href="#section{{ $i }}"
                                 role="button" aria-expanded="false" aria-controls="section{{ $i }}">
                                 {{ $i + 1 }}.{{ $form->sections[$i]->section_name }}
                             </a>
                         </p>
                     </div>

                     {{-- description --}}
                     <p class="h6">
                         {{ $form->sections[$i]->section_description ?? '' }}
                     </p>

                     {{-- if there are no fields --}}
                     @if ($form->sections[$i]->fields->isEmpty())
                         <div class="m-2">
                             <p>This section has no form fields</p>
                         </div>
                     @else
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
                                 data-radio-field="{{ $condional_id }}" data-trigger-value="{{ $trigger_value }}"
                                 style="@if ($condional_id != null) display:none; @endif">
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
                                                             name="{{ $field->id }}" value="{{ $option }}">
                                                         <label for="{{ $field->id }}_{{ $loop->index }}"
                                                             class="ml-2">{{ $option }}</label>
                                                     </div>
                                                 @endforeach
                                             @elseif ($field->type === 'checkbox')
                                                 <div class="mb-3 d-flex flex-column justify-content-between">
                                                     <label for="{{ $field->id }}"
                                                         class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                         {{ $field->label }}</label>
                                                 </div>
                                                 @foreach (explode(',', $field->options) as $option)
                                                     <div class="m-4">
                                                         <input type="{{ $field->type }}"
                                                             id="{{ $field->id }}_{{ $loop->index }}"
                                                             name="{{ $field->id }}[]" value="{{ $option }}">
                                                         <label for="{{ $field->id }}_{{ $loop->index }}"
                                                             class="ml-2">{{ $option }}</label>
                                                     </div>
                                                 @endforeach
                                             @elseif ($field->type === 'select')
                                                 <div class="mb-3 d-flex flex-column justify-content-between">
                                                     <label for="{{ $field->id }}"
                                                         class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                         {{ $field->label }}</label>

                                                     {{-- select --}}
                                                     <select name="{{ $field->id }}" id="{{ $field->id }}"
                                                         class="form-select">
                                                         <option value="">-- Select --</option>
                                                         @foreach (explode(',', $field->options) as $option)
                                                             <option value="{{ $option }}">
                                                                 {{ $option }}
                                                             </option>
                                                         @endforeach
                                                     </select>
                                                 </div>
                                             @elseif ($field->type === 'textarea')
                                                 <div class="mb-3 d-flex flex-column justify-content-between">
                                                     <div class="d-flex justify-content-between mb-2">
                                                         <label for="{{ $field->id }}"
                                                             class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                             {{ $field->label }}</label>
                                                     </div>

                                                     <textarea id="{{ $field->id }}" name="{{ $field->id }}" rows="15"
                                                         placeholder="The responses here should be comma separated"></textarea>

                                                 </div>
                                             @elseif ($field->type === 'file')
                                                 <div class="mb-3 d-flex flex-column justify-content-between">
                                                     <div class="d-flex justify-content-between mb-2">
                                                         <label for="{{ $field->id }}"
                                                             class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                             {{ $field->label }}</label>
                                                     </div>
                                                     <input type="file"name="{{ $field->id }}"
                                                         id="{{ $field->id }}" accept=".pdf">

                                                 </div>
                                             @elseif ($field->type === 'repeater')
                                                 <div class="mb-3 d-flex flex-column justify-content-between">
                                                     <label for="{{ $field->id }}"
                                                         class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                         {{ $field->label }}</label>
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
                                                                             name="{{ $field->id }}[0][{{ $option['field'] }}]">
                                                                     </td>
                                                                 @endforeach
                                                                 <td><span class="remove-btn">Remove</span></td>
                                                             </tr>
                                                         </tbody>
                                                     </table>
                                                     <button type="button" id="add-repeater">Add More</button>
                                                 </div>
                                             @else
                                                 <div class="mb-3 d-flex flex-column justify-content-between">
                                                     <label for="{{ $field->id }}"
                                                         class="form-label">{{ $i + 1 }}.{{ $key + 1 }}.
                                                         {{ $field->label }}</label>
                                                     <input type="{{ $field->type }}" id="{{ $field->id }}"
                                                         name="{{ $field->id }}" class="form-control w-75 ms-2">
                                                 </div>
                                             @endif


                                         </div>
                                     </div>
                                 </div>
                             </div>
                         @endforeach
                     @endif
                 </div>
             </div>
 </div>
 @endfor
 {{-- hidden form_id --}}
 <input type="hidden" name="form_id" value="{{ $form->uuid }}">
 {{-- submit button --}}
 <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 pt-3">
     <div class="d-flex justify-content-center">
         <div class="form-group me-5">
             <button class="btn btn-primary" type="submit" name="action" value="submit">
                 {{ $formMode === 'edit' ? 'Update' : 'Submit' }}
             </button>
         </div>

         {{-- @if ($formMode === 'create')
             <div class="form-group">
                 <button class="btn btn-primary" type="submit" name="action" value="draft">
                     Save as Draft
                 </button>
             </div>
         @endif --}}
     </div>
     @endif

 </div>
