<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WorkFromHome extends Model
{
    use HasFactory;

    protected $table = 'work_from_homes';

    protected $primaryKey = 'work_from_home_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'employee_id',
        'work_from_home_id',
        'work_from_home_start_date',
        'work_from_home_end_date',
        'work_from_home_reason',
        'work_from_home_attachments',
        'work_location',
        'task_id',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($work_from_home) {
            $work_from_home->work_from_home_id = (string) \Illuminate\Support\Str::uuid();
        });
    }
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'work_from_home_id', 'work_from_home_id');
    }
}
