<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Attendance extends Model
{
    use HasFactory;

    protected $table = "attendances";

    // Primary key is attendance_id
    protected $primaryKey = 'attendance_id';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    // The attributes that are mass assignable
    protected $fillable = [
        'attendance_id',
        'employee_id',
        'attendance_date', // Corrected this line
        'clock_in'
    ];

    // Cast attributes to specific types
    protected $casts = [
        'attendance_date' => 'date',
    ];

    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new attendance
        static::creating(function ($attendance) {
            $attendance->attendance_id = (string) Str::uuid();
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
