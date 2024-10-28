<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;

    // a section can have 0 or more fields
    public function fields()
    {
        return $this->hasMany(FormField::class, 'section_id', 'id');
    }
}
