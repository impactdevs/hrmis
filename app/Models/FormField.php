<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'label', 'type', 'options', 'repeater_options'];

    //cast repeater options to array
    protected $casts = [
        'repeater_options' => 'array', // Automatically convert JSON to array
    ];

    // each form field can have 0 or more properties
    public function properties()
    {
        return $this->hasMany(Condition::class, 'field_id');
    }

    //each form form field belongs to a form section
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
}
