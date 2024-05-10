<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListReservationRess;
use App\Models\Reservation;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    //Liste des reservations par evenement
    public function getListReservation($event_id)
    {
        $reservations = Reservation::where("event_id", $event_id)->with("users", "event")->get();
        if (!$reservations) return response(["message" => "Aucune reservation pour cet evenement"]);

        return response(ListReservationRess::collection($reservations));
    }


    //Ajout d'une reservation a un evenement
    public function addReservation(Request $request,  $event_id)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
        ]);

        if ($validator->fails()) {
            return  response(["message" => "Veuiller renseigner une adresse email valide "]);
        }
        $user_id = User::where("email",  $request->email)->first();
        if (!$user_id) {
            return response(["message" => "Cet utilisateur n'existe pas " , "status" => "error"]);
        }

        if (Reservation::where("event_id", $event_id)->where("user_id", $user_id->id)->first()) {
            return response(["message" => "Vous avez deja fait une reservation pour cet evenement" , "status" => "warning"]);
        }

        try {
            Reservation::create([
                "event_id" => $event_id,
                "user_id" => $user_id->id
            ]);
            return response()->json([
                "message" => "Reservation fait avec succes" ,
                "status" => "success"
            ]);
        } catch (Exception $err) {
            return response(["Erreur" => $err]);
        }
    }
}
