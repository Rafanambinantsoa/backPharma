<?php

namespace App\Http\Controllers;

use App\Models\evenement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class MobileController extends Controller
{
    public function getEventsEncours()
    {
        $event  = evenement::where("status", 0)->get();

        return response($event);
    }

    public function login(Request $request)
    {
        $attrs = Validator::make($request->all(),  [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($attrs->fails()) {
            return response([
                'message' => 'Please check the infos',
                // 'mistake' => $attrs->errors()
            ], 403);
        }

        if (!Auth::attempt($request->all())) {
            return response([
                'message' => 'Veuillez verifier vos credentielles'
            ], 403);
        }

        if (Auth::attempt($request->all())) {
            $user = User::where("email", $request->email)->first();
            if ($user->role == "0") {
                return response([
                    'message' => "success",
                    'token' => auth()->user()->createToken('token')->plainTextToken

                ], 200);
            } else {
                return response([
                    'message' => "Votre compte n'as pas le droit de se connecter ici",
                ], 403);
            }
        }
    }
}
