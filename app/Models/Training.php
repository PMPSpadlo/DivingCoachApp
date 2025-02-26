<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Training extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id', 'trainer_id', 'date', 'location', 'type', 'notes'
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

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function athletes()
    {
        return $this->belongsToMany(Athlete::class, 'training_athletes');
    }
}
