<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $primaryKey = 'event_id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'event_id',
        'event_start_date',
        'event_end_date',
        'event_title',
        'event_description',
        'event_location',
        'category',
    ];

    protected $casts = [
        'category' => 'array',
        'event_start_date' => 'date',
        'event_end_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            $event->event_id = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
