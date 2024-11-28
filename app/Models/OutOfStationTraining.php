<?php

namespace App\Models;

use App\Models\Scopes\OutOfStationScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;

#[ScopedBy([OutOfStationScope::class])]
class OutOfStationTraining extends Model
{
    use HasFactory;

    // Specify the table if it doesn't follow Laravel's naming convention
    protected $table = 'out_station_trainings';

    //primary key is employee_id
    // Specify the primary key
    protected $primaryKey = 'training_id';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    // The attributes that are mass assignable
    protected $fillable = [
        'training_id',
        'destination',
        'travel_purpose',
        'relevant_documents',
        'departure_date',
        'return_date',
        'sponsor',
        'hotel',
        'email',
        'tel',
        'my_work_will_be_done_by',
        'training_request_status',
        'rejection_reason',
        'user_id',
    ];

    // If you want to use casts for certain attributes
    protected $casts = [
        'relevant_documents' => 'array',
        'my_work_will_be_done_by' => 'array',
        'training_request_status' => 'array',
        'departure_date' => 'date',
        'return_date' => 'date',
    ];

    // Model boot method
    protected static function boot()
    {
        parent::boot();

        // Automatically generate a UUID when creating a new Employee
        static::creating(function ($training) {
            $training->training_id = (string) Str::uuid();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
