<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    use HasFactory;

    protected $fillable = ['form_id', 'responses', 'user_id'];

    // protected $casts = [
    //     'responses' => 'json',

    // ];

    // an entry belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // an entry belongs to a form
    public function form()
    {
        return $this->belongsTo(Form::class, 'form_id', 'uuid');
    }

    public function job()
    {
        return $this->belongsTo(CompanyJob::class, 'company_job_id', 'company_job_id');
    }

    //an entry belongs to an 0 or appraisals
    public function appraisals()
    {
        return $this->hasMany(Appraisal::class, 'entry_id', 'id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'entry_id', 'id');
    }
}
