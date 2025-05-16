<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    use HasFactory;

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_appointment_present_post' => 'date',
        'education_history' => 'array',
        'uce_details' => 'array',
        'uace_details' => 'array',
        'employment_record' => 'array',
        'references' => 'array',
        'present_salary' => 'decimal:2',
        'salary_expectation' => 'decimal:2',
        'academic_documents' => 'array',
        'other_documents' => 'array',
    ];

    protected $fillable = [
        // Section 1
        'post_applied',
        'reference_number',
        'full_name',
        'date_of_birth',
        'email',
        'telephone',

        // Section 2
        'nationality',
        'home_district',
        'sub_county',
        'village',
        'residency_type',

        // Section 3
        'present_department',
        'present_post',
        'date_of_appointment_present_post',
        'present_salary',
        'terms_of_employment',

        // Section 4
        'marital_status',
        'number_of_children',
        'children_ages',

        // Section 5-7
        'education_history',
        'uce_details',
        'uace_details',
        'employment_record',

        // Section 8-9
        'criminal_convicted',
        'criminal_details',
        'availability',
        'salary_expectation',

        // Section 10
        'references',
        'recommender_name',
        'recommender_title',
                   'academic_documents',
            'cv',
            'other_documents'
    ];

    public function companyJob()
    {
        return $this->belongsTo(CompanyJob::class);
    }
}
