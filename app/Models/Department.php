<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $primaryKey = 'department_id';

    //string
    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'department_id',
        'department_name',
        'department_head',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($department) {
            $department->department_id = (string) \Illuminate\Support\Str::uuid();
        });
    }

    //a department has a department head(user)
    public function user()
    {
        return $this->belongsTo(User::class, 'department_head', 'id');
    }
}
