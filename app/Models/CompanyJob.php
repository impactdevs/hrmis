<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class CompanyJob extends Model
{
    protected $primaryKey  = 'company_job_id';
    public    $incrementing = false;
    protected $keyType     = 'string';

    protected $fillable = [
        'job_code',
        'job_title',
        'job_description',
        'public_token',
        'will_become_active_at',
        'will_become_inactive_at',
        // Criteria
        'criteria_min_qualification',
        'criteria_min_experience_years',
        'criteria_min_age',
        'criteria_max_age',
        'criteria_required_keywords',
        // Weights
        'weight_qualification',
        'weight_experience',
        'weight_keyword_match',
        'weight_age_fit',
    ];

    protected $casts = [
        'will_become_active_at'          => 'datetime',
        'will_become_inactive_at'        => 'datetime',
        'criteria_required_keywords'     => 'array',
        'criteria_min_experience_years'  => 'integer',
        'criteria_min_age'               => 'integer',
        'criteria_max_age'               => 'integer',
        'weight_qualification'           => 'integer',
        'weight_experience'              => 'integer',
        'weight_keyword_match'           => 'integer',
        'weight_age_fit'                 => 'integer',
    ];

    // Qualification hierarchy used for comparison
    const QUALIFICATION_LEVELS = [
        'Certificate' => 1,
        'Diploma'     => 2,
        'Degree'      => 3,
        'Masters'     => 4,
        'PhD'         => 5,
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $job) {
            if (empty($job->company_job_id)) {
                $job->company_job_id = (string) Str::uuid();
            }
            if (empty($job->public_token)) {
                $job->public_token = (string) Str::uuid();
            }
        });
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query
            ->where(function (Builder $q) {
                $q->whereNull('will_become_active_at')
                  ->orWhere('will_become_active_at', '<=', now());
            })
            ->where(function (Builder $q) {
                $q->whereNull('will_become_inactive_at')
                  ->orWhere('will_become_inactive_at', '>=', now());
            });
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function applicationLink(): string
    {
        return route('apply.show', $this->public_token);
    }

    public function regenerateToken(): self
    {
        $this->update(['public_token' => (string) Str::uuid()]);
        return $this;
    }

    public function statusLabel(): string
    {
        if ($this->will_become_active_at?->isFuture()) return 'upcoming';
        if ($this->will_become_inactive_at?->isPast()) return 'closed';
        return 'active';
    }

    /**
     * True only when this job has a deadline and it has passed. Jobs with no
     * deadline set never "close" via this check — used to decide whether a
     * rejection notice can go out now or must wait.
     */
    public function applicationsClosed(): bool
    {
        return $this->will_become_inactive_at?->isPast() ?? false;
    }

    public function hasCriteria(): bool
    {
        return !empty($this->criteria_min_qualification)
            || !is_null($this->criteria_min_experience_years)
            || !is_null($this->criteria_min_age)
            || !is_null($this->criteria_max_age)
            || !empty($this->criteria_required_keywords);
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class, 'company_job_id', 'company_job_id');
    }
}