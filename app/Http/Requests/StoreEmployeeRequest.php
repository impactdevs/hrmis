<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules()
    {
        // Get the employee ID from the route
        $employeeId = $this->route('employee') ? $this->route('employee')->employee_id : null;
        return [
            'first_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'gender' => 'required',
            'title' => 'nullable|string|max:255',
            'staff_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees')->ignore($employeeId, 'employee_id'), // Specify the primary key
            ],
            'position_id' => 'nullable|uuid|exists:positions,position_id',
            'nin' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees')->ignore($employeeId, 'employee_id'),
            ],
            'date_of_entry' => 'nullable|date',
            'contract_expiry_date' => 'nullable|date|after_or_equal:date_of_entry',
            'department_id' => 'nullable|uuid|exists:departments,department_id',
            'nssf_no' => 'nullable|string|max:255',
            'home_district' => 'nullable|string|max:255',
            'qualifications_details' => 'nullable|array',
            'qualifications_details.*.title' => 'nullable|string|max:255',
            'contract_documents' => 'nullable|array',
            'contract_documents.*.title' => 'nullable|string|max:255',
            'qualifications_details.*.proof' => 'nullable|file|mimes:jpeg,png,jpg,gif,pdf|max:2048',
            'tin_no' => 'nullable|string|max:255',
            'job_description' => 'nullable|string',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees')->ignore($employeeId, 'employee_id'), // Specify the primary key
            ],
            'phone_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees')->ignore($employeeId, 'employee_id'),
            ],
            'next_of_kin' => 'nullable|string|max:255',
            'passport_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'date_of_birth' => 'nullable|date|before:today',
            'entitled_leave_days' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'staff_id.unique' => 'Staff ID must be unique.',
            'position_id.exists' => 'The selected position does not exist.',
            'nin.unique' => 'NIN must be unique.',
            'contract_expiry_date.after_or_equal' => 'Contract expiry date must be on or after the date of entry.',
            'department_id.exists' => 'The selected department does not exist.',
            'qualifications_details.array' => 'Qualifications details must be a valid array.',
            'qualifications_details.*.title.string' => 'Each qualification title must be a string.',
            'qualifications_details.*.proof.file' => 'Each proof must be a valid file.',
            'qualifications_details.*.proof.mimes' => 'Each proof must be a file of type: jpeg, png, jpg, gif, pdf.',
            'qualifications_details.*.proof.max' => 'Each proof must not exceed 2MB.',
            'contract_documents.array' => 'Qualifications details must be a valid array.',
            'contract_documents.*.title.string' => 'Each qualification title must be a string.',
            'contract_documents.*.proof.file' => 'Each proof must be a valid file.',
            'contract_documents.*.proof.mimes' => 'Each proof must be a file of type: jpeg, png, jpg, gif, pdf.',
            'contract_documents.*.proof.max' => 'Each proof must not exceed 2MB.',
            'email.email' => 'Email must be a valid email address.',
            'email.unique' => 'Email must be unique.',
            'email.required' => 'Email is required.',
            'phone_number.unique' => 'Phone number must be unique.',
            'date_of_birth.before' => 'Date of birth must be a date before today.',
            'entitled_leave_days.integer' => 'Entitled leave days must be an integer.',
        ];
    }
}
