<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class evenement extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'description',
        'date',
        'heure',
        'lieu',
        'user_id',
        'status'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'presences', 'evenement_id', 'user_id');
    }
}
