<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialOrder extends Model
{
    protected $fillable = ['user_id', 'item_name', 'is_ordered', 'admin_comment'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
