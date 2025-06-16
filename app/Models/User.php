<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use App\Scopes\EmployeeScope;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'agreed_to_data_usage'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'agreed_to_data_usage' => 'boolean',
        ];
    }

    //a user is related to an employee by email
    public function employee()
    {
        return $this->hasOne(Employee::class, 'email', 'email')->withoutGlobalScopes();
    }

    //check if admin or executive sec
    public function getisAdminOrSecretaryAttribute()
    {
        return $this->hasRole('HR') || $this->hasRole('Executive Secretary') || $this->hasRole('Assistant Executive Secretary') || $this->hasRole('Head of Division');
    }

}
