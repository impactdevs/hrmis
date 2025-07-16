<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    protected $primaryKey = 'task_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'task_id',
        'work_from_home_id',
        'task_start_date',
        'task_end_date',
        'description',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($task) {
            $task->task_id = (string) Str::uuid();
        });
    }

    public function workFromHome()
    {
        return $this->belongsTo(WorkFromHome::class, 'work_from_home_id', 'work_from_home_id');
    }

    
}

