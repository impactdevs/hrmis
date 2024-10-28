<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    use HasFactory;

    protected $table = 'leaves';

    protected $primaryKey = 'leave_id';

    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    protected $fillable = [
        'leave_id',
        'user_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'leave_request_status',
        'my_work_will_be_done_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'my_work_will_be_done_by' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leave) {
            $leave->leave_id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function leaveCategory()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id', 'leave_type_id');
    }
}
