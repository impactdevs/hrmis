<?php

namespace App\Models;

use App\Models\Scopes\AttendanceScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([AttendanceScope::class])]
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

    protected $fillable = [
        'attendance_id',
        'staff_id',
        'access_date_and_time',
        'access_date',
        'access_time',
    ];

    protected $casts = [
        'access_date_and_time' => 'datetime',
        'access_date' => 'date',
        'access_time' => 'datetime:H:i:s',
    ];

        public $timestamps = false;

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
