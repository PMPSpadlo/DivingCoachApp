<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'club_id', 'athlete_id', 'amount', 'payment_date', 'transaction_id', 'currency', 'status', 'payment_method'
    ];

    public function club()
    {
        return $this->belongsTo(Club::class);
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }
}
