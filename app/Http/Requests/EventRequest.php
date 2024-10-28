<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Adjust authorization as needed
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'event_start_date' => 'required|date',
            'event_end_date' => 'required|date|after_or_equal:event_start_date',
            'event_title' => 'required|string|max:255',
            'event_description' => 'required|string',
            'category' => 'required|array',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'event_start_date.required' => 'The event start date is required.',
            'event_end_date.required' => 'The event end date is required.',
            'event_end_date.after_or_equal' => 'The event end date must be after or equal to the start date.',
            'event_title.required' => 'The event title is required.',
            'event_description.required' => 'The event description is required.',
            'category.required' => 'The category is required.',
            'category.json' => 'The category must be a valid JSON string.',
        ];
    }
}
