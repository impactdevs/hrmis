<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Condition extends Model
{
    use HasFactory;

    //table
    protected $table = 'field_properties';

    //fillable
    protected $fillable = [
        'field_id',
        'conditional_visibility_field_id',
        'conditional_visibility_operator',
    ];
}
