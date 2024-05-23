<?php

namespace App\Http\Controllers;

use App\Http\Resources\ListReservationPerUser;
use App\Http\Resources\ListReservationRess;
use App\Models\evenement;
use App\Models\Reservation;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    //Annuler une reservation
    public function cancelReservation($event_id, $user_id)
    {
        $reservation = Reservation::where("event_id", $event_id)->where("user_id", $user_id)->first();
        if (!$reservation) {
            return response(["message" => "Vous n'avez pas fait de reservation pour cet evenement", "status" => "warning"]);
        }

        //contrainte genre on ne peut plus annuler 2 jours avant l'evenement
        $event = evenement::find($event_id);
        $date = date("Y-m-d");
        $date_event = $event->date;
        $diff = strtotime($date_event) - strtotime($date);
        $diff = $diff / (60 * 60 * 24);
        if ($diff <= 2) {
            return response(["message" => "Vous ne pouvez plus annuler cette reservation", "status" => "warning"]);
        }
        $reservation->delete();
        return response(["message" => "success", "status" => "success"]);
    }

    //la listes de tous les reservations fait par un user 
    public function reservationPerUser(User $user)
    {
        $data = Reservation::with("event")->where("user_id", $user->id)->get();
        $truedata = ListReservationPerUser::collection($data);

        return response($truedata);
    }

    // Liste des réservations par événement avec pagination
    public function getListReservation($event_id)
    {
        // Récupérer les réservations pour l'événement spécifié avec les utilisateurs associés
        $reservations = Reservation::with("users")->where("event_id", $event_id)->paginate(5);

        $formattedResponse = [
            'data' => $reservations->map(function ($presence) {
                // Extraire uniquement les informations nécessaires de l'utilisateur
                $user = $presence->users;
                return [
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ];
            }),
            // Ajouter les informations de pagination
            'pagination' => [
                'current_page' => $reservations->currentPage(),
                'from' => $reservations->firstItem(),
                'to' => $reservations->lastItem(),
                'per_page' => $reservations->perPage(),
                'total' => $reservations->total(),
                'prev_page_url' => $reservations->previousPageUrl(),
                'next_page_url' => $reservations->nextPageUrl(),
            ],
        ];

        return response()->json($formattedResponse);
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
            return response(["message" => "Cet utilisateur n'existe pas ", "status" => "error"]);
        }

        //contrainte genre on ne peut plus reserver 2 jours avant l'evenement
        $event = evenement::find($event_id);
        $date = date("Y-m-d");
        $date_event = $event->date;
        $diff = strtotime($date_event) - strtotime($date);
        $diff = $diff / (60 * 60 * 24);
        if ($diff <= 2) {
            return response(["message" => "Vous ne pouvez plus reserver pour cet evenement", "status" => "warning"]);
        }

        if (Reservation::where("event_id", $event_id)->where("user_id", $user_id->id)->first()) {
            return response(["message" => "Vous avez deja fait une reservation pour cet evenement", "status" => "warning"]);
        }

        try {
            Reservation::create([
                "event_id" => $event_id,
                "user_id" => $user_id->id
            ]);
            return response()->json([
                "message" => "Reservation fait avec succes",
                "status" => "success"
            ]);
        } catch (Exception $err) {
            return response(["Erreur" => $err]);
        }
    }
}
