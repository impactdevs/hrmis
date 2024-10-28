<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
