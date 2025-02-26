<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'address', 'city', 'country', 'phone', 'email', 'description', 'website', 'active', 'owner_id'
    ];

    public function hasTrainer($userId)
    {
        return $this->users()->where('user_id', $userId)->wherePivot('role', 'trainer')->exists();
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function athletes()
    {
        return $this->hasMany(Athlete::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'club_user')->withPivot('role', 'is_active');
    }

    public function trainings()
    {
        return $this->hasMany(Training::class);
    }

    public function competitions()
    {
        return $this->hasMany(Competition::class);
    }
}
