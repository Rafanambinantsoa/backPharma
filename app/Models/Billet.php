<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Billet extends Model
{
    protected $fillable = [
        'event_id' , 
        'title' ,
        'token' , 
        'isScanned',
        'user_id',
        'userBuy'
    ];


    use HasFactory;

    public function event(){
        return $this->belongsTo(evenement::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function usera(){
        return $this->belongsTo(User::class , 'userBuy');
    }
    
}
