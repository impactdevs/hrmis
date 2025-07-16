<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Support\Str;

class Whistleblower extends Model
{

     use HasUuids; // Laravel 9+ feature
    protected $table = 'whistleblowers';
    protected $primaryKey = 'whistleblower_id'; // Only if using UUID as PK
    public $incrementing = false; // Only if using UUID as PK
    protected $keyType = 'string';


    protected $fillable = [
        'whistleblower_id',
        'employee_name',
        'employee_department',
        'employee_email',
        'employee_telephone',
        'job_title',
        'submission_type',
        'description',
        'individuals_involved',
        'evidence_id',
        'issue_reported',
        'resolution',
        'confidentiality_statement'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->whistleblower_id = $model->whistleblower_id ?? (string) Str::uuid();
        });
    }

    public function evidence()
    {
        return $this->hasMany(Evidence::class, 'whistleblower_id', 'whistleblower_id');
    }
}

