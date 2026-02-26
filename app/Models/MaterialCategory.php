<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Get the materials for the category.
     */
    public function materials()
    {
        return $this->hasMany(Material::class , 'category_id');
    }

    /**
     * Check if all materials in this category have stock > warning threshold.
     */
    public function isComplete(): bool
    {
        // If there are no materials, technically it's not "complete" or we can default to true. Let's default to true.
        if ($this->materials->isEmpty()) {
            return false;
        }

        foreach ($this->materials as $material) {
            if ($material->stock_count <= $material->low_stock_threshold) {
                return false;
            }
        }

        return true;
    }
}
