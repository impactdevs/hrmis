<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
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
        'screening_token',
        'screening_pin',
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

    // Never expose the hashed PIN if this model is ever serialized to an array/view dump.
    protected $hidden = [
        'screening_pin',
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

    /**
     * True once HR has generated a screening link (and set a PIN) for this job.
     */
    public function hasScreeningLink(): bool
    {
        return !empty($this->screening_token) && !empty($this->screening_pin);
    }

    public function screeningLink(): ?string
    {
        return $this->screening_token ? route('screening.show', $this->screening_token) : null;
    }

    /**
     * (Re)generate the screening link and set/replace the shared board PIN.
     * Regenerating invalidates any previously shared link and any sessions
     * that had verified against the old PIN.
     */
    public function setScreeningPin(string $pin): self
    {
        $this->update([
            'screening_token' => (string) Str::uuid(),
            'screening_pin'   => Hash::make($pin),
        ]);
        return $this;
    }

    public function checkScreeningPin(string $pin): bool
    {
        return $this->screening_pin && Hash::check($pin, $this->screening_pin);
    }

    public function statusLabel(): string
    {
        if ($this->will_become_active_at?->isFuture()) return 'upcoming';
        if ($this->will_become_inactive_at?->isPast()) return 'closed';
        return 'active';
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