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
        'email',
        'password',
        'is_admin',
        'is_super_admin',
        'is_chef',
        'address',
        'mobile_number',
        'is_active',
        'approval_token',
        'google_calendar_url',
        'trello_url',
        'trello_token',
        'must_change_password',
        'language',
        'daily_reminder_enabled',
        'vacation_days_per_year',
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
        'password' => 'hashed',
        'is_admin' => 'boolean',
        'is_super_admin' => 'boolean',
        'is_chef' => 'boolean',
        'is_active' => 'boolean',
        'must_change_password' => 'boolean',
        'daily_reminder_enabled' => 'boolean',
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
