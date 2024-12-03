<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingRequest extends FormRequest
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
            'training_title' => 'required|string|max:255',
            'training_description' => 'required|string',
            'training_location' => 'required|string|max:255',
            'training_start_date' => 'required|date|after_or_equal:1920-01-01|before_or_equal:2100-12-31',
            'training_end_date' => 'required|date|after_or_equal:training_start_date|after_or_equal:1920-01-01|before_or_equal:2100-12-31',
            'training_category' => 'required|array',
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'training_title.required' => 'The training title is required.',
            'training_description.required' => 'The training description is required.',
            'training_location.required' => 'The training location is required.',
            'training_start_date.required' => 'The training start date is required.',
            'training_start_date.after_or_equal' => 'The training start date must be after or equal to 01-01-1920.',
            'training_start_date.before_or_equal' => 'The training start date must be before or equal to 31-12-2100.',
            'training_end_date.required' => 'The training end date is required.',
            'training_end_date.after_or_equal' => 'The training end date is must be after or equal to the start date or make sure the start date is after or equal to 01-01-1920.',
            'training_end_date.before_or_equal' => 'The training end date must be before or equal to 31-12-2100.',
            'training_category.required' => 'The training category is required.',
            'training_category.array' => 'The training category must be a valid array.',
        ];
    }
}
