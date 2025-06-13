<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyJob extends Model
{
    use HasFactory;

    protected $table = 'company_jobs';

    protected $primaryKey = 'company_job_id';

    public $incrementing = false;

    protected $fillable = [
        'company_job_id',
        'job_code',
        'job_title',
        'will_become_active_at',
        'will_become_inactive_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'will_become_active_at' => 'datetime',
        'will_become_inactive_at' => 'datetime',
    ];
    

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($companyJob) {
            $companyJob->company_job_id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    // add isActive attribute, should return just true or false
    public function getIsActiveAttribute()
    {
        return $this->will_become_active_at && $this->will_become_active_at <= now() &&
               (!$this->will_become_inactive_at || $this->will_become_inactive_at > now());
    }
}
