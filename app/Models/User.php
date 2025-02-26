<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'password',
        'role',
        'profile_photo_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the full name of the user.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Check if user is a club owner.
     */
    public function isClubOwner(): bool
    {
        return $this->role === 'club_owner';
    }

    /**
     * Check if user is a trainer.
     */
    public function isTrainer(): bool
    {
        return $this->role === 'trainer';
    }

    /**
     * The clubs that the user is associated with (as a trainer or owner).
     */
    public function clubs()
    {
        return $this->belongsToMany(Club::class, 'club_user')->withPivot('role', 'is_active');
    }

    /**
     * The club where the user is the owner.
     */
    public function ownedClubs()
    {
        return $this->hasMany(Club::class, 'owner_id');
    }

    /**
     * Trainings where the user is the trainer.
     */
    public function trainings()
    {
        return $this->hasMany(Training::class, 'trainer_id');
    }

    /**
     * Competitions organized by the user's club.
     */
    public function competitions()
    {
        return $this->hasMany(Competition::class, 'club_id');
    }
}
