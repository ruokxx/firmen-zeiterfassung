<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'unit', 'stock_count', 'low_stock_threshold', 'category_id'];

    protected $casts = [
        'stock_count' => 'float',
        'low_stock_threshold' => 'float',
    ];

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class , 'category_id');
    }

    public function transactions()
    {
        return $this->hasMany(MaterialTransaction::class);
    }
}
