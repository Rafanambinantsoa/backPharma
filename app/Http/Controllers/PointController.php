<?php

namespace App\Http\Controllers;

use App\Models\evenement;
use App\Models\Point;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PointController extends Controller
{
    public function sommePoint(User $user)
    {
        $points = Point::where('user_id', $user->id)->get();
        $somme = 0;
        foreach ($points as $point) {
            $somme += $point->point;
        }
        return response()->json([
        "points" => $somme
        ]);
    }
    public function actualisationPoint()
    {
        // Récupérer les événements cloturés
        $events = Evenement::where('status', 1)->get();

        // Pour chaque événement cloturé, ajouter 2 points à chaque utilisateur qui a participé à l'événement
        foreach ($events as $event) {
            // Récupérer les présences avec les informations utilisateur associées
            $users = Presence::with('usera')
                ->where('evenement_id', $event->id)
                ->where('firstPresence', true)
                ->where('secondPresence', true)
                ->get();

            foreach ($users as $user) {
                // Vérifier si l'utilisateur a déjà des points pour cet événement
                $existingPoints = Point::where('user_id', $user->user_id)
                    ->where('event_id', $event->id)
                    ->exists();

                // Si l'utilisateur n'a pas encore de points pour cet événement, on lui en ajoute 2
                if (!$existingPoints) {
                    Point::create([
                        'user_id' => $user->user_id,
                        'event_id' => $event->id,
                        'point' => 2
                    ]);
                }
            }
        }

        return response([
            "message" => "done"
        ]);
    }

    public function showUserPoint(User $user)
    {
        return response()->json($user->points);
    }

    public function addPoint($user)
    {
        $user = User::find($user);
        if (!$user) {
            return response()->json([
                'message' => "User n'existe pas "
            ]);
        }
        $point = Point::where('user_id', $user->id)->first();
        if (!$point) {
            Point::create([
                'user_id' => $user->id,
                'point' => 2
            ]);

            return response()->json([
                'message' => "Point ajouté avec succès"
            ]);
        }
        $point->update([
            'point' => $point->point + 2
        ]);

        return response()->json($point, 201);
    }
}
