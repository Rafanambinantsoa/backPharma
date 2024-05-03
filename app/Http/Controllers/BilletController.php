<?php

namespace App\Http\Controllers;

use App\Http\Resources\ParticipantRes;
use App\Models\Billet;
use App\Models\evenement;
use Illuminate\Http\Request;

class BilletController extends Controller
{
    public function showInformation($token){
        $billet = Billet::where('token', $token)->first();
        return response([
            "ticket" => $billet
        ],200);

    }

    public function scannerUnBillet($token , Request $request)
    {
        $billet = Billet::where('token', $token)->first();
        if($request->idOrgan != $billet->user_id){
            return response([
                'message' => 'Vous n\'avez pas le droit de valider ce billet',
            ], 401);
        }

        if($billet->isScanned == 1){
            return response([
                'message' => 'Ce billets est deja Scanner',
            ], 409);
        }

        $billet->isScanned = true;
        $billet->save();
        return response([
            'message' => 'Billet scanné avec succès',
            // 'billet' => $billet
        ], 200);
    }

    public function ticketParEvent(evenement $event)
    {
        $billets = $event->billets;
        return response()->json([
            'billets' => $billets
        ], 200);
    }
    //recuperer les billets d'un evenement ainsi que les informations de l'acheteur
    public function ticketParEventAvecAcheteur(evenement $event)
    {
        $kim = $event->billets()->with('usera')->get();
        return response([
            'participant' => ParticipantRes::collection($kim)
        ]);
    }
  
}
