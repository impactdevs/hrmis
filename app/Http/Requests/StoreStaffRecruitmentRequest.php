<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRecruitmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        // Set this to true if you want to allow all users to make the request
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'position' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,department_id',
            'number_of_staff' => 'required|integer|min:1',
            'date_of_recruitment' => 'required|date|after:today', // Ensures the recruitment date is in the future
            'sourcing_method' => 'required|in:internal,external', // Either internal or external
            'employment_basis' => 'required|in:Contract,Part-time,Fulltime', // Contract, Part-time, or Fulltime
            'justification' => 'required|string|min:10', // Justification must be at least 10 characters
            'funding_budget' => 'required|in:governement of uganda,project',
        ];
    }

    /**
     * Get the custom validation messages for the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'position.required' => 'The position to be filled is required.',
            'department_id.required' => 'Please select a department/unit.',
            'department_id.exists' => 'The selected department does not exist.',
            'number_of_staff.required' => 'The total number of staff required is required.',
            'number_of_staff.integer' => 'The number of staff must be an integer.',
            'number_of_staff.min' => 'At least one staff member is required.',
            'date_of_recruitment.required' => 'The recruitment date is required.',
            'date_of_recruitment.date' => 'Please provide a valid date.',
            'date_of_recruitment.after' => 'The recruitment date must be a future date.',
            'sourcing_method.required' => 'Please select a sourcing method.',
            'sourcing_method.in' => 'The sourcing method must be either internal or external.',
            'employment_basis.required' => 'Please select an employment basis.',
            'employment_basis.in' => 'The employment basis must be Contract, Part-time, or Fulltime.',
            'justification.required' => 'Please provide a justification for the recruitment.',
            'justification.min' => 'The justification must be at least 10 characters long.',
            'funding_budget.required' => 'Please select a funding budget.',
            'funding_budget.in' => 'The funding budget must be either governement of uganda or project.',
        ];
    }
}
