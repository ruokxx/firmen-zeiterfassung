<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsPopupRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'news_popup_id',
        'user_id',
        'dismissed_at',
        'last_seen_date',
    ];

    protected $casts = [
        'dismissed_at' => 'datetime',
        'last_seen_date' => 'date',
    ];

    public function popup()
    {
        return $this->belongsTo(NewsPopup::class, 'news_popup_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
