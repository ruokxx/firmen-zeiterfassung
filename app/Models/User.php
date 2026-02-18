<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'is_active',
        'role',
        'address',
        'mobile_number',
        'language',
        'must_change_password',
        'google_calendar_url',
        'trello_url',
        'trello_id',
        'trello_token',
        'trello_token_secret',
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
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getIsAdminAttribute(): bool
    {
        return in_array($this->role, ['admin', 'chef']);
    }

    public function getIsSuperAdminAttribute(): bool
    {
        return $this->role === 'admin';
    }

    // Keep existing method for compatibility, but check for both roles
    public function isAdmin(): bool
    {
        return $this->is_admin; // Uses the accessor above
    }

    public function workDays()
    {
        return $this->hasMany(WorkDay::class);
    }

    public function documents()
    {
        return $this->hasMany(UserDocument::class);
    }
}
