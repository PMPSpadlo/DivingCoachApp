<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'location', 'date', 'club_id', 'judge_panel', 'type', 'level'
    ];

    public function scopeUpcoming($query)
    {
        return $query->where('date', '>', Carbon::now());
    }

    public function scopePast($query)
    {
        return $query->where('date', '<', Carbon::now());
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function results()
    {
        return $this->hasMany(CompetitionResult::class);
    }
}
