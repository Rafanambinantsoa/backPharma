<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;
    protected $fillable = [
        'presence',
        'user_id',
        'evenement_id',
        'firstPresence',
        'secondPresence',
    ];

    public function usera(){
        return $this->belongsTo(User::class , 'user_id');
    }
}
