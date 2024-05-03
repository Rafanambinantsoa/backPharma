<?php

namespace App\Http\Controllers;

use App\Models\evenement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EvenementController extends Controller
{
    //Clore un evenement
    public function cloreEvent(evenement $event)
    {
        $event->update([
            'status' => 0
        ]);
        return response()->json($event);
    }

    //Nombre d'evenement total pour chaque Organisateurs
    public function countAllEventbyUser()
    {
        $data = evenement::all()->count();
        return response()->json($data);
    }

    //Nombre d'evenement total pour chaque Organisateurs
    public function allEventbyUser($id)
    {
        $user = User::find($id);
        $data = $user->eventy()->get();
        return response()->json($data);
    }

    public function allEventbyUserPaginated($id)
    {
        $user = User::find($id);
        $data = $user->eventy()->paginate(5);
        return response()->json($data);
    }

    //Nombre d'evenement total pour chaque Organisateurs
    public function countAllEventValidebyUser($id)
    {
        $user = User::find($id);
        $data = $user->eventy()->where('status', true)->count();
        return response()->json($data);
    }

    public function cours($id)
    {
        $user = User::find($id);
        $data = $user->eventy()->where('status', 1)->get();
        return response()->json($data);
    }

    public function getEventsPaginated()
    {
        $data = evenement::paginate(5);
        return response()->json($data);
    }

    public function countCours($id)
    {
        $user = User::find($id);
        $data = $user->eventy()->where('status', 1)->get()->count();
        return response()->json($data);
    }

    public function terminer($id)
    {
        $user = User::find($id);
        $data = $user->eventy()->where('status', 0)->get();
        return response()->json($data);
    }

    public function terminerPaginated($id)
    {
        $user = User::find($id);
        $data = $user->eventy()->where('status', 0)->paginate(3);
        return response()->json($data);
    }

    public function countTerminer($id)
    {
        $user = User::find($id);
        $data = $user->eventy()->where('status', 0)->get()->count();
        return response()->json($data);
    }


    public function countAllEventNonValidebyUser($id)
    {
        $user = User::find($id);
        $data = $user->eventy()->where('status', false)->count();
        return response()->json($data);
    }

    public function countAllEvents()
    {
        $user = evenement::all()->count();
        return response()->json($user);
    }

    //Modifier un event 
    public function updateEvent(Request $request, evenement $event)
    {
        $event->update($request->all());
        return response()->json([
            'message' => 'Evenement modifier'
        ], 200);
        // return response()->json($event, 200);
    }

    //Supprimer un event
    public function deleteEvent(evenement $event)
    {
        $event->delete();
        return response()->json([
            'message' => 'Evenement supprimer'
        ], 204);
    }



    //Un Evenement Specifique
    public function oneEvent(evenement $event)
    {
        return response()->json($event);
    }

    //Tous les Evenements  en cours
    public function allEvents()
    {
        //result qui ne suis pas l'ordre
        $data = evenement::all();

        return response()->json($data);
    }

    //Tous les evenement mais en shuffle
    public function index()
    {
        //result qui ne suis pas l'ordre et aussi celui qui ont le status true et aussi celui qui ont le status true et limitebillet > 0
        $data = evenement::where('status', true)->where('limitBillets', '>', 0)->inRandomOrder()->get();
        return response()->json($data);
    }

    //Les 6 derniers ajouts
    public function latest()
    {
        //les dernier ajouter et ne prend que 6  et aussi celui qui ont le status true et limitebillet > 0
        $data = evenement::where('status', true)->where('limitBillets', '>', 0)->latest()->take(6)->get();

        return response()->json($data);
    }

    //Creation d'events
    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'titre' => 'required',
            'description' => 'required',
            'date' => 'required',
            'heure' => 'required',
            'lieu' => 'required',
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Complete all fields'
            ]);
        } else {
            $data = evenement::create([
                'titre' => $request->titre,
                'description' => $request->description,
                'date' => $request->date,
                'heure' => $request->heure,
                'lieu' => $request->lieu , 
                'user_id' => $request->user_id,
            ]);
            if (!$data) {
                return response()->json([
                    'message' => 'Erreur de connexion '
                ]);
            }
            return response()->json([
                'message' => "success",
            ]);
        }
    }
    public function test($id)
    {
        return response()->json([
            'message' => "testa" . $id,
        ]);
    }

    //Nombre de tous les event creer 
    public function countAllEvent()
    {
        $data = evenement::all()->count();
        return response($data);
    }
    //nombre des events avec des status true
    public function countAllEventValide()
    {
        $data = evenement::where('status', 1)->count();
        return response()->json($data);
    }
    //nombre des events avec des status true
    public function encours()
    {
        $data = evenement::where('status', 1)->get();
        return response()->json($data);
    }
}
