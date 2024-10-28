<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $table = 'leave_types';

    protected $primaryKey = 'leave_type_id';

    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    protected $fillable = [
        'leave_type_id',
        'leave_type_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveType) {
            $leaveType->leave_type_id = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
