<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Contract extends Model
{
    protected $table = "employee_contracts";
    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';
    protected $fillable = ["start_date", "end_date", "employee_id", "contract_documents", 'description', 'supervisor'];

    protected $casts = [
        'contract_documents' => 'array',
        'start_date' => 'date',
        'end_date' => 'date'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($contract) {
            $contract->id = (string) Str::uuid();
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function getDaysUntilEndAttribute()
    {
        return floor(Carbon::now()->diffInRealDays($this->end_date, false));

    }
}
