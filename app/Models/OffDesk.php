<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OffDesk extends Model
{
    use HasFactory;

    protected $table = 'off_desks';

    protected $primaryKey = 'off_desk_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'off_desk_id',
        'employee_id',
        'start_datetime',
        'end_datetime',
        'destination',
        'duty_allocated',
        'reason',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($off_desk) {
            $off_desk->off_desk_id = (string) \Illuminate\Support\Str::uuid();
        });
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
