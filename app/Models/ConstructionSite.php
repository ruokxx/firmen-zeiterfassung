<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConstructionSite extends Model
{
    protected $fillable = ['name', 'status'];

    public function timeEntries()
    {
        return $this->hasMany(TimeEntry::class);
    }
}
