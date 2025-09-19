<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AppraisalStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->hasAnyRole(['Staff', 'Head of Division', 'HR', 'Executive Secretary']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'review_type' => 'nullable|string|in:probation,annual,end_of_contract,other',
            'review_type_other' => 'nullable|string|max:255|required_if:review_type,other',
            'appraisal_start_date' => 'nullable|date|before_or_equal:appraisal_end_date',
            'appraisal_end_date' => 'nullable|date|after_or_equal:appraisal_start_date',
            'contract_id' => 'nullable|uuid|exists:employee_contracts,id',
            'job_compatibility' => 'nullable|string|in:yes,no',
            'if_no_job_compatibility' => 'nullable|string|max:1000|required_if:job_compatibility,no',
            'unanticipated_constraints' => 'nullable|string|max:2000',
            'personal_initiatives' => 'nullable|string|max:2000',
            'training_support_needs' => 'nullable|string|max:2000',
            
            // Performance ratings
            'appraisal_period_rate' => 'nullable|array',
            'appraisal_period_rate.*.planned_activity' => 'nullable|string|max:500',
            'appraisal_period_rate.*.output_results' => 'nullable|string|max:500',
            'appraisal_period_rate.*.supervisee_score' => 'nullable|numeric|between:1,5',
            'appraisal_period_rate.*.superviser_score' => 'nullable|numeric|between:1,5',
            
            // Personal attributes assessment
            'personal_attributes_assessment' => 'nullable|array',
            'personal_attributes_assessment.*.appraisee_score' => 'nullable|numeric|between:1,5',
            'personal_attributes_assessment.*.appraiser_score' => 'nullable|numeric|between:1,5',
            'personal_attributes_assessment.*.agreed_score' => 'nullable|numeric|between:1,5',
            
            // Performance planning
            'performance_planning' => 'nullable|array',
            'performance_planning.*.description' => 'nullable|string|max:500',
            'performance_planning.*.performance_target' => 'nullable|string|max:500',
            'performance_planning.*.target_date' => 'nullable|date|after:today',
            
            // Text assessments
            'employee_strength' => 'nullable|string|max:2000',
            'employee_improvement' => 'nullable|string|max:2000',
            'superviser_overall_assessment' => 'nullable|string|max:2000',
            'panel_recommendations' => 'nullable|string|max:2000',
            'supervisor_recommendations' => 'nullable|string|max:2000',
            'panel_comment' => 'nullable|string|max:2000',
            'overall_assessment_and_comments' => 'nullable|string|max:2000',
            'panel_recommendation' => 'nullable|string|max:2000',
            'overall_assessment' => 'nullable|string|max:2000',
            'executive_secretary_comments' => 'nullable|string|max:2000',
            
            // Document uploads
            'relevant_documents' => 'nullable|array',
            'relevant_documents.*.title' => 'nullable|string|max:255',
            'relevant_documents.*.proof' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,gif|max:10240', // 10MB max
            
            // Draft status
            'is_draft' => 'nullable|string|in:draft,not_draft',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'review_type.in' => 'Please select a valid review type.',
            'review_type_other.required_if' => 'Please specify the other review type when "Other" is selected.',
            'appraisal_start_date.before_or_equal' => 'Appraisal start date must be before or equal to the end date.',
            'appraisal_end_date.after_or_equal' => 'Appraisal end date must be after or equal to the start date.',
            'if_no_job_compatibility.required_if' => 'Please explain why the job is not compatible when "No" is selected.',
            'contract_id.exists' => 'Selected contract is invalid.',
            
            // Score validation messages
            '*.supervisee_score.between' => 'Supervisee scores must be between 1 and 5.',
            '*.superviser_score.between' => 'Supervisor scores must be between 1 and 5.',
            '*.appraisee_score.between' => 'Appraisee scores must be between 1 and 5.',
            '*.appraiser_score.between' => 'Appraiser scores must be between 1 and 5.',
            '*.agreed_score.between' => 'Agreed scores must be between 1 and 5.',
            
            // File validation messages
            'relevant_documents.*.proof.mimes' => 'Documents must be PDF, DOC, DOCX, JPG, JPEG, PNG, or GIF files.',
            'relevant_documents.*.proof.max' => 'Document files must not exceed 10MB.',
            
            // Date validation messages
            'performance_planning.*.target_date.after' => 'Performance target dates must be in the future.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'review_type_other' => 'other review type',
            'if_no_job_compatibility' => 'job incompatibility explanation',
            'unanticipated_constraints' => 'unanticipated constraints',
            'personal_initiatives' => 'personal initiatives',
            'training_support_needs' => 'training and support needs',
            'employee_strength' => 'employee strengths',
            'employee_improvement' => 'areas for improvement',
            'superviser_overall_assessment' => 'supervisor overall assessment',
            'executive_secretary_comments' => 'executive secretary comments',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Clean up empty arrays and null values
        $data = $this->all();
        
        // Clean up performance ratings
        if (isset($data['appraisal_period_rate']) && is_array($data['appraisal_period_rate'])) {
            $data['appraisal_period_rate'] = array_filter($data['appraisal_period_rate'], function($item) {
                return !empty(array_filter($item, function($value) {
                    return !is_null($value) && $value !== '';
                }));
            });
        }
        
        // Clean up performance planning
        if (isset($data['performance_planning']) && is_array($data['performance_planning'])) {
            $data['performance_planning'] = array_filter($data['performance_planning'], function($item) {
                return !empty(array_filter($item, function($value) {
                    return !is_null($value) && $value !== '';
                }));
            });
        }
        
        $this->replace($data);
    }
}
