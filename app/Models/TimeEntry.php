<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeEntry extends Model
{
    protected $fillable = ['work_day_id', 'construction_site_id', 'hours'];

    public function workDay()
    {
        return $this->belongsTo(WorkDay::class);
    }

    public function constructionSite()
    {
        return $this->belongsTo(ConstructionSite::class);
    }
}
