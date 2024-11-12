<?php

namespace App\Models;

use App\Models\Scopes\TrainingScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([TrainingScope::class])]
class Training extends Model
{
    use HasFactory;

    protected $table = 'trainings';

    protected $primaryKey = 'training_id';

    public $incrementing = false;


    protected $fillable = [
        'training_id',
        'training_title',
        'training_description',
        'training_location',
        'training_start_date',
        'training_end_date',
        'training_category',
        'approval_status',
        'rejection_reason',
        'user_id',
    ];

    protected $casts = [
        'training_category' => 'array',
        'training_start_date' => 'date',
        'training_end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($training) {
            $training->training_id = (string) \Illuminate\Support\Str::uuid();
        });
    }


}
