<?php

namespace App\Models;

use App\Models\Scopes\EmployeeScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'employee_id';
    }

    // The attributes that are mass assignable
    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'gender',
        'middle_name',
        'title',
        'staff_id',
        'position_id',
        'nin',
        'date_of_entry',
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
        'user_id',
        'national_id_photo',
        'contract_documents',
        'entitled_leave_days',
    ];

    // If you want to use casts for certain attributes
    protected $casts = [
        'qualifications_details' => 'array', // Automatically convert JSON to array
        'contract_documents' => 'array',
        'date_of_entry' => 'date',
        'date_of_birth' => 'date',
    ];

    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new Employee
        static::creating(function ($employee) {
            $employee->employee_id = (string) Str::uuid();

            // Normalize email to lowercase on creation
            if (isset($employee->email)) {
                $employee->email = strtolower(trim($employee->email));
            }
        });

        // Normalize email to lowercase when updating
        static::saving(function ($employee) {
            if (isset($employee->email) && $employee->isDirty('email')) {
                $employee->email = strtolower(trim($employee->email));
            }
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

    //calculate retirement years remaining, every employee retires at 60
    public function retirementYearsRemaining()
    {
        if (empty($this->date_of_birth)) {
            return "no date of birth specified";
        }

        $today = now()->timezone('UTC'); // Ensure today is in UTC
        $age = $today->diff($this->date_of_birth); // Get full date difference

        $yearsRemaining = 60 - $age->y; // 60 is retirement age
        $monthsRemaining = 12 - $age->m; // Calculate remaining months in current year

        // If we have negative months remaining (i.e., we're already in a new year), adjust the year.
        if ($monthsRemaining === 12) {
            $yearsRemaining++;
            $monthsRemaining = 0;
        }

        return "{$yearsRemaining} years, {$monthsRemaining} months"; // Return formatted string
    }


    //each employee has 0 or more leave rosters
    public function leaveRoster()
    {
        return $this->hasMany(LeaveRoster::class, 'employee_id', 'employee_id');
    }

    public function totalLeaveDays()
    {
        $totalDays = 0;

        // Get all approved leaves EXCEPT maternity leave
        $leaves = Leave::where('is_cancelled', false)
            ->where('user_id', $this->user_id)
            ->whereYear('created_at', Carbon::now()->year) // Current year only
            ->whereHas('leaveCategory', function ($query) {
                // Exclude maternity leave from the balance calculation
                $query->where('leave_type_name', '!=', 'Maternity');
            })
            ->where(function ($query) {
                // Check if any approval role has approved the leave
                $query->whereJsonContains('leave_request_status', ['Executive Secretary' => 'approved'])
                    ->orWhereJsonContains('leave_request_status', ['Head of Division' => 'approved'])
                    ->orWhereJsonContains('leave_request_status', ['HR' => 'approved'])
                    ->orWhereJsonContains('leave_request_status', ['fully_approved' => true]);
            })
            ->get();

        // Calculate the total leave days excluding weekends and public holidays
        $publicHolidays = PublicHoliday::pluck('holiday_date')->toArray();
        $publicHolidays = array_map(function ($date) {
            return Carbon::parse($date)->toDateString();
        }, $publicHolidays);

        $totalDays = $leaves->sum(function ($leave) use ($publicHolidays) {
            return Carbon::parse($leave->start_date)
                ->diffInDaysFiltered(function (Carbon $date) use ($publicHolidays) {
                    return !$date->isWeekend() && !in_array($date->toDateString(), $publicHolidays);
                }, Carbon::parse($leave->end_date));
        });

        return $totalDays;
    }

    //get the total leave roster days for an employee where booking_approval_status is Approved
    public function totalLeaveRosterDays()
    {
        $leaves = LeaveRoster::where('employee_id', $this->employee_id)->get();
        $totalDays = $leaves->sum(function ($leave) {
            return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date));
        });
        return $totalDays;
    }

    //overall roster days for an employee
    public function overallRosterDays()
    {
        $today = Carbon::today();
        // Get all leave roster entries for this employee where end_date is in the future
        // and where the leave_roster_id does NOT exist in the leaves table (leave_roster_id column)
        $leaves = LeaveRoster::where('employee_id', $this->employee_id)
            ->whereDate('end_date', '>=', $today)
            ->where(function ($query) {
                $query->whereDoesntHave('leave')
                    ->orWhereHas('leave', function ($q) {
                        $q->where('is_cancelled', false);
                    });
            })
            ->get();

        // Fetch all public holidays as an array of dates
        $publicHolidays = PublicHoliday::pluck('holiday_date')->toArray();

        $publicHolidays = array_map(function ($date) {
            return Carbon::parse($date)->toDateString();
        }, $publicHolidays);

        $totalDays = $leaves->sum(function ($leave) use ($publicHolidays) {
            return Carbon::parse($leave->start_date)
                ->diffInDaysFiltered(function (Carbon $date) use ($publicHolidays) {
                    return !$date->isWeekend() && !in_array($date->toDateString(), $publicHolidays);
                }, Carbon::parse($leave->end_date));
        });
        return $totalDays;
    }

    public function leaveDaysConsumedPerMonth()
    {
        // Get all leave records for the employee (assuming $this->user_id is the employee ID)
        $leaves = Leave::where('user_id', $this->user_id)->get();

        // Initialize an array to hold the total leave days consumed per year and per month
        $daysConsumedPerYearMonth = [];

        // Iterate over each leave record
        foreach ($leaves as $leave) {
            // Start date and end date of the leave
            $startDate = Carbon::parse($leave->start_date);
            $endDate = Carbon::parse($leave->end_date);

            // Loop through the months the leave spans (could span multiple months)
            while ($startDate->lte($endDate)) {
                // Get the year and month of the current leave date
                $year = $startDate->year;
                $month = $startDate->month;

                // Calculate the number of days consumed in the current month
                if ($startDate->month == $endDate->month) {
                    // If the leave starts and ends in the same month, calculate the days between the two dates
                    $daysInMonth = $startDate->diffInDays($endDate) + 1;
                } else {
                    // If the leave spans across multiple months
                    if ($startDate->month == $month) {
                        // For the first month, calculate days until the end of the month
                        $daysInMonth = $startDate->endOfMonth()->diffInDays($startDate) + 1;
                    } else {
                        // For the last month, calculate days from the start of the month to the end date
                        $daysInMonth = $startDate->diffInDays($endDate) + 1;
                    }
                }

                // Initialize the year if it does not exist in the array
                if (!isset($daysConsumedPerYearMonth[$year])) {
                    $daysConsumedPerYearMonth[$year] = [];
                }

                // Initialize the month if it does not exist for the given year
                if (!isset($daysConsumedPerYearMonth[$year][$month])) {
                    $daysConsumedPerYearMonth[$year][$month] = 0;
                }

                // Add the calculated days to the corresponding month in the correct year
                $daysConsumedPerYearMonth[$year][$month] += $daysInMonth;

                // Move to the next month
                $startDate->addMonth();
            }
        }

        // Return the nested array of total leave days consumed per year and month
        return $daysConsumedPerYearMonth;
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class, 'employee_id', 'employee_id')->withoutGlobalScopes();
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Define the relationship with the User model
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function qualifications()
    {
        return $this->hasMany(EmployeeQualification::class, 'employee_id', 'employee_id');
    }

    /**
     * Synchronize qualifications from JSON column to qualifications table
     * This method helps maintain consistency between both systems
     */
    public function syncQualificationsToTable()
    {
        // Clear existing qualifications in the table
        $this->qualifications()->delete();

        // If there are qualifications in the JSON column, migrate them
        if (filled($this->qualifications_details)) {
            foreach ($this->qualifications_details as $qualData) {
                $this->qualifications()->create([
                    'qualification' => $qualData['qualification'] ?? $qualData['title'] ?? null,
                    'institution' => $qualData['institution'] ?? null,
                    'year_obtained' => $qualData['year_obtained'] ?? null,
                    'proof' => $qualData['proof'] ?? null,
                ]);
            }
        }
    }

    /**
     * Get qualifications from the new table and format them like the JSON column
     * This provides backward compatibility for code that expects the old format
     */
    public function getQualificationsFromTableAttribute()
    {
        return $this->qualifications->map(function ($qualification, $index) {
            return [
                'qualification' => $qualification->qualification,
                'institution' => $qualification->institution,
                'year_obtained' => $qualification->year_obtained,
                'proof' => $qualification->proof,
            ];
        })->toArray();
    }


}
