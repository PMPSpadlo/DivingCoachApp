<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingAthlete extends Model
{
    use HasFactory;

    protected $fillable = [
        'training_id', 'athlete_id'
    ];

    public function training()
    {
        return $this->belongsTo(Training::class);
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}
