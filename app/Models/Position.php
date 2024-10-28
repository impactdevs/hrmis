<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $table = 'positions';

    protected $primaryKey = 'position_id';

    public $incrementing = false;

    protected $fillable = [
        'position_id',
        'position_name',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($position) {
            $position->position_id = (string) \Illuminate\Support\Str::uuid();
        });
    }
}
