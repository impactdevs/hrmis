<?php

namespace App\Models;

use App\Models\Scopes\EmployeeScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;


#[ScopedBy([EmployeeScope::class])]
class Employee extends Model
{
    use HasFactory;

    // Specify the table if it doesn't follow Laravel's naming convention
    protected $table = 'employees';

    //primary key is employee_id
    // Specify the primary key
    protected $primaryKey = 'employee_id';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    // The attributes that are mass assignable
    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'title',
        'staff_id',
        'position_id',
        'nin',
        'date_of_entry',
        'contract_expiry_date',
        'department_id',
        'nssf_no',
        'home_district',
        'qualifications_details',
        'tin_no',
        'job_description',
        'email',
        'phone_number',
        'next_of_kin',
        'passport_photo',
        'date_of_birth',
        'user_id'
    ];

    // If you want to use casts for certain attributes
    protected $casts = [
        'qualifications_details' => 'array', // Automatically convert JSON to array
        'date_of_entry' => 'date',
        'contract_expiry_date' => 'date',
        'date_of_birth' => 'date',
    ];

    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new Employee
        static::creating(function ($employee) {
            $employee->employee_id = (string) Str::uuid();
        });
    }

    // Define the relationship with the Department model
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'department_id');
    }

    // Define the relationship with the Position model
    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    public function daysUntilContractExpiry()
    {
        $today = now()->timezone('UTC'); // Ensure today is in UTC
        $expiryDateString = $this->contract_expiry_date;

        // Check if expiry date is not set
        if (empty($expiryDateString)) {
            return null; // or you could return 0, or another appropriate value
        }

        // Get the date part only
        $expiryDateString = explode(' ', $expiryDateString)[0];

        // Create Carbon instance from the formatted date string, assuming the expiry date is in UTC
        $expiryDate = Carbon::createFromFormat('Y-m-d', $expiryDateString, 'UTC');

        // Calculate the difference in days
        $daysDifference = $today->diffInDays($expiryDate); // Reverse order

        // Check if the expiry date is in the future using isAfter
        if ($expiryDate->isAfter($today)) {
            return (int) $daysDifference; // Days until expiry
        } else {
            return (int) -$daysDifference; // Days expired (negative value)
        }
    }



}
