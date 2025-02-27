<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'birth_date', 'gender', 'phone', 'email', 'club_id', 'notes', 'age_category'
    ];

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function age()
    {
        return Carbon::parse($this->birth_date)->age;
    }

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function trainings()
    {
        return $this->belongsToMany(Training::class, 'training_athletes');
    }

    public function competitionResults()
    {
        return $this->hasMany(CompetitionResult::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function competitions()
    {
        return $this->belongsToMany(Competition::class, 'competition_results', 'athlete_id', 'competition_id');
    }
}
