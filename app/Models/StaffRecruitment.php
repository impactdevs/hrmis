<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StaffRecruitment extends Model
{
    use HasFactory;

    // Specify the table if it doesn't follow Laravel's naming convention
    protected $table = 'staff_recruitments';

    //primary key is employee_id
    // Specify the primary key
    protected $primaryKey = 'staff_recruitment_id';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    // The attributes that are mass assignable
    protected $fillable = [
        'staff_recruitment_id',
        'position',
        'department_id',
        'number_of_staff',
        'date_of_recruitment',
        'sourcing_method',
        'employment_basis',
        'justification',
        'approval_status'
    ];

    // If you want to use casts for certain attributes
    protected $casts = [
        'date_of_recruitment' => 'date',
    ];


    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new Employee
        static::creating(function ($staffRecruitment) {
            $staffRecruitment->staff_recruitment_id = (string) Str::uuid();
        });

    }

    //every staff recruitment belongs to a department
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

}
