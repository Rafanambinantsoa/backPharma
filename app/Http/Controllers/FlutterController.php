<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlutterController extends Controller
{
    public function loginApi(Request $request){
        $attrs = $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);
        if(!Auth::attempt($attrs)){
            return response([
                'message' => 'Wrong credentials'
            ] , 403);
        }

        if(Auth::attempt($attrs)){
            if(auth()->user()->role == "0" ){
                return response([
                    'message' => "Votre compte n'as pas le droit de se connecter ici"
                ],401);
            }
        }

        return response([
            'user' => auth()->user()  ,
            'token' => auth()->user()->createToken('token')->plainTextToken
        ],200);
    }

    public function getUser(){
        return response([
            'user'=> auth()->user()
        ],200);
    }

    ///Desing flutter
    // https://www.youtube.com/watch?v=ExKYjqgswJg&ab_channel=TheFlutterWay
}
