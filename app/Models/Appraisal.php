<?php

namespace App\Models;

use App\Models\Scopes\AppraisalScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([AppraisalScope::class])]
class Appraisal extends Model
{
    use HasFactory;

    // Specify the table if it doesn't follow Laravel's naming convention
    protected $table = 'appraisals';

    //primary key is employee_id
    // Specify the primary key
    protected $primaryKey = 'appraisal_id';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    // The attributes that are mass assignable
    protected $fillable = [
        'appraisal_id',
        'entry_id',
        'employee_id',
        'appraisal_type',
        'approval_status',
        'rejection_reason'
    ];

    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new Employee
        static::creating(function ($appraisal) {
            $appraisal->appraisal_id = (string) Str::uuid();
        });
    }

    public function entry()
    {
        return $this->belongsTo(Entry::class, 'entry_id', 'id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
