<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'stock_count', 'low_stock_threshold', 'category_id'];

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class , 'category_id');
    }

    public function transactions()
    {
        return $this->hasMany(MaterialTransaction::class);
    }
}
