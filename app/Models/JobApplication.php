<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class JobApplication extends Model
{
    // ── Status constants ──────────────────────────────────────────────────────
    const STATUS_PENDING     = 'pending';
    const STATUS_SHORTLISTED = 'shortlisted';
    const STATUS_INTERVIEWED = 'interviewed';
    const STATUS_OFFERED     = 'offered';
    const STATUS_HIRED       = 'hired';
    const STATUS_REJECTED    = 'rejected';

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_SHORTLISTED,
            self::STATUS_INTERVIEWED,
            self::STATUS_OFFERED,
            self::STATUS_HIRED,
            self::STATUS_REJECTED,
        ];
    }

    // ── Fillable ──────────────────────────────────────────────────────────────
    protected $fillable = [
        'company_job_id',
        'post_applied', 'reference_number', 'full_name',
        'date_of_birth', 'email', 'telephone',
        'nationality', 'nin', 'home_district', 'sub_county', 'village', 'residency_type',
        'present_department', 'present_post', 'date_of_appointment_present_post', 'terms_of_employment',
        'marital_status',
        'employment_record',
        'education_training',
        'criminal_convicted', 'criminal_details',
        'availability', 'salary_expectation',
        'references', 'recommender_name', 'recommender_title',
        'academic_documents', 'cv', 'other_documents',
        'status', 'rejection_reason',
        'score', 'score_breakdown', 'meets_criteria', 'criteria_failures', 'scored_at',
    ];

    protected $casts = [
        'date_of_birth'                    => 'date',
        'date_of_appointment_present_post' => 'date',
        'criminal_convicted'               => 'boolean',
        'salary_expectation'               => 'decimal:2',
        'employment_record'                => 'array',
        'education_training'               => 'array',
        'references'                       => 'array',
        'academic_documents'               => 'array',
        'other_documents'                  => 'array',
        'score_breakdown'                  => 'array',
        'criteria_failures'                => 'array',
        'meets_criteria'                   => 'boolean',
        'scored_at'                        => 'datetime',
    ];

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (!empty($filters['company_job_id'])) {
            $query->where('company_job_id', $filters['company_job_id']);
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $query->where(function (Builder $q) use ($s) {
                $q->where('reference_number', 'like', "%{$s}%")
                  ->orWhere('full_name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }
        return $query;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function companyJob()
    {
        return $this->belongsTo(CompanyJob::class, 'company_job_id', 'company_job_id');
    }
}