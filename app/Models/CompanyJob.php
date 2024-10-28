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
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($companyJob) {
            $companyJob->company_job_id = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
