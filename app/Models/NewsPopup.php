<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsPopup extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'type',
        'start_at',
        'expires_at',
        'display_type',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reads()
    {
        return $this->hasMany(NewsPopupRead::class);
    }

    public function scopeActive($query)
    {
        $now = now();
        return $query->where('is_active', true)
            ->where(function ($q) use ($now) {
                $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
            })
            ->where(function ($q) use ($now) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', $now);
            });
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isDismissedByUser(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        $read = $this->reads()->where('user_id', $user->id)->first();
        if (!$read) {
            return false;
        }

        if ($this->display_type === 'once_per_day') {
            return $read->last_seen_date && $read->last_seen_date->isToday();
        }

        if ($this->display_type === 'always') {
            return false;
        }

        // 'once_per_user'
        return true;
    }
}
