<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        "event_id",
        "user_id"
    ];

    public function users()
    {
        return $this->belongsTo(User::class , "user_id");
    }
    public function event()
    {
        return $this->belongsTo(evenement::class , "event_id");
    }
}
