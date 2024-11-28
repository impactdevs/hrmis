<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OutStationTrainingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // You can add authorization logic here if necessary
        return true; // assuming the user is authorized to submit the request
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'destination' => 'nullable|string|max:255',
            'travel_purpose' => 'nullable|string|max:255',
            'relevant_documents' => 'nullable|array',
            'departure_date' => 'required|date|after_or_equal:today', // The departure date must be today or in the future
            'return_date' => 'required|date|after:departure_date', // The return date must be after the departure date
            'sponsor' => 'nullable|string|max:500',
            'hotel' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'tel' => 'nullable|string|max:20',
            'my_work_will_be_done_by' => 'required|array|min:1', // At least one delegate must be assigned
            'training_request_status' => 'nullable|array',
            'rejection_reason' => 'nullable|string|max:500',
            'user_id' => 'nullable|exists:users,id', // Ensure the user exists in the users table
        ];
    }

    /**
     * Get the custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'departure_date.required' => 'Please specify your departure date.',
            'departure_date.after_or_equal' => 'The departure date must be today or in the future.',
            'return_date.required' => 'Please specify your return date.',
            'return_date.after' => 'The return date must be after the departure date.',
            'my_work_will_be_done_by.required' => 'You must provide at least one person to cover your work during your leave.',
            'email.email' => 'Please provide a valid email address.',
            'tel.max' => 'The phone number should not be longer than 20 characters.',
        ];
    }

    /**
     * Get the validation attributes for the request.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'destination' => 'training destination',
            'travel_purpose' => 'travel purpose',
            'departure_date' => 'departure date',
            'return_date' => 'return date',
            'sponsor' => 'training sponsor',
            'hotel' => 'hotel name',
            'email' => 'email address',
            'tel' => 'phone number',
            'my_work_will_be_done_by' => 'delegates',
            'training_request_status' => 'leave request status',
            'rejection_reason' => 'rejection reason',
            'user_id' => 'user ID',
        ];
    }
}
