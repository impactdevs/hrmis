<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class JobApplication extends Model
{
    use HasFactory;

    protected $casts = [
        'date_of_birth' => 'date',
        'date_of_appointment_present_post' => 'date',
        'education_history' => 'array',
        'employment_record' => 'array',
        'education_training' => 'array',
        'references' => 'array',
        'salary_expectation' => 'decimal:2',
        'academic_documents' => 'array',
        'other_documents' => 'array',
        'cv' => 'array',
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
        'nin',
        'residency_type',

        // Section 3
        'present_department',
        'present_post',
        'date_of_appointment_present_post',
        'terms_of_employment',

        // Section 4
        'marital_status',

        // Section 5-7
        'employment_record',

        // Section 8-9
        'criminal_convicted',
        'criminal_details',
        'availability',
        'salary_expectation',

        'education_training',

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



    public function scopeFilter(\Illuminate\Database\Eloquent\Builder $query, array $filters): \Illuminate\Database\Eloquent\Builder
    {
        // Filter by company_job_id → filter reference_number by prefix
        if (!empty($filters['company_job_id'])) {
            $query->whereExists(function ($subQuery) use ($filters) {
                $subQuery->select(DB::raw(1))
                    ->from('company_jobs')
                    ->where('company_job_id', $filters['company_job_id'])
                    ->whereRaw('job_applications.reference_number LIKE CONCAT(company_jobs.job_code, \'%\')');
            });
        }

        // Filter by date range
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        // General search across reference_number and full_name
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%");
            });
        }

        // Individual filters (if passed)
        if (!empty($filters['reference_number'])) {
            $query->where('reference_number', 'like', "%{$filters['reference_number']}%");
        }

        if (!empty($filters['full_name'])) {
            $query->where('full_name', 'like', "%{$filters['full_name']}%");
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['position'])) {
            $query->where('position', 'like', "%{$filters['position']}%");
        }

        // Add more fields below as needed
        // if (!empty($filters['field_name'])) {
        //     $query->where('field_name', $filters['field_name']);
        // }

        return $query;
    }
}
