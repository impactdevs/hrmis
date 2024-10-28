<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'status', 'uuid'];

    // Specify the table if it doesn't follow Laravel's naming convention
    protected $table = 'forms';

    //primary key is employee_id
    // Specify the primary key
    protected $primaryKey = 'form_id';

    // Indicate that the primary key is not an auto-incrementing integer
    public $incrementing = false;

    // Specify the type of the primary key
    protected $keyType = 'string';

    // a form has one setting
    public function setting()
    {
        return $this->hasOne(FormSetting::class, 'form_id', 'uuid');
    }

    //section
    public function sections()
    {
        return $this->hasMany(Section::class, 'form_id', 'uuid');
    }
}
