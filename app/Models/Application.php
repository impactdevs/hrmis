<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory;

    // Specify the table if it doesn't follow Laravel's naming convention
    protected $table = 'job_applications';

    //primary key is employee_id
    // Specify the primary key
    protected $primaryKey = 'application_id';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    // The attributes that are mass assignable
    protected $fillable = [
        'application_id',
        'company_job_id',
        'entry_id',
    ];

    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new Employee
        static::creating(function ($application) {
            $application->application_id = (string) Str::uuid();
        });
    }

    public function entry()
    {
        return $this->belongsTo(Entry::class, 'entry_id', 'id');
    }

    public function job()
    {
        return $this->belongsTo(CompanyJob::class, 'company_job_id', 'company_job_id');
    }
}
