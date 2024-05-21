<?php

namespace App\Http\Controllers;

use App\Http\Resources\kimRes;
use App\Http\Resources\ParticipantRes;
use App\Http\Resources\PresentCollection;
use App\Models\evenement;
use App\Models\Presence;
use App\Models\Reservation;
use App\Models\User;
use Dompdf\Dompdf;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class InvitationController extends Controller

{
    //A controller who send An email to all User that an event is created
    public function EnvoieInvitation($id)
    {
        $event = evenement::find($id);
        if (!$event) {
            return response()->json([
                'message' => 'Evenement non trouvé'
            ], 404);
        }
        //envoie email a tous les utilisateurs sauf a l'admin qui a une role de admin
        $users = User::where('role', '!=', 'admin')->get();

        foreach ($users as $user) {
            try {
                Mail::send('mails.invitationv2', ['evenement' => $event, 'user' => $user], function ($message) use ($user) {
                    $message->to($user->email);
                    $message->subject('Nouvel événement');
                });
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Erreur lors de l\'envoie de l\'invitation',
                    'error' => $e->getMessage()
                ], 500);
            }
        }

        return response()->json([
            'message' => 'Invitation envoyée avec succès'
        ], 200);
    }

    //une methode pour le scan d'un user pour afficher son information grace a son Badgetoken
    public function showUserInformation($token)
    {
        $user = User::where('badgeToken', $token)->first();
        return response(
            $user,
            200
        );
    }

    //une methode pour la  premiere presence 
    public function firstPresence($event_id, $user_id)
    {
        //verification si l'utilisateur a reserver sa place
        if(!Reservation::where('user_id', $user_id)->where('event_id', $event_id)->exists()){
            return response()->json([
                'message' => "Vous n'avez pas réservé votre place pour cet événement"
            ], 400);
        }


        if (Presence::where('user_id', $user_id)->where('evenement_id', $event_id)->exists()) {
            return response()->json([
                'message' => 'Première présence déjà enregistrée'
            ], 400);
        }

        Presence::create([
            'user_id' => $user_id,
            'evenement_id' => $event_id,
            'firstPresence' => true,
            'secondPresence' => false
        ]);

        return response()->json([
            'message' => 'Première présence enregistrée'
        ], 200);
    }

    //une methode pour la deuxieme presence
    public function secondPresence($event_id, $user_id)
    {
        $presence = Presence::where('user_id', $user_id)->where('evenement_id', $event_id)->first();
        if (!$presence) {
            return response()->json([
                'message' => 'Première présence non enregistrée'
            ], 404);
        }

        if ($presence->secondPresence) {
            return response()->json([
                'message' => 'Deuxième présence déjà enregistrée'
            ], 400);
        }

        $presence->update([
            'secondPresence' => true
        ]);

        return response()->json([
            'message' => 'Deuxième présence enregistrée'
        ], 200);
    }

    //recuperer la liste de tous les invites  present 100%
    public function getListPresence($event_id)
    {
        // Récupérer les présences avec les informations utilisateur associées
        $presences = Presence::with('usera')
            ->where('evenement_id', $event_id)
            ->where('firstPresence', true)
            ->where('secondPresence', true)
            ->paginate(5);

        // Formater la réponse JSON
        $formattedResponse = [
            'data' => $presences->map(function ($presence) {
                // Extraire uniquement les informations nécessaires de l'utilisateur
                $user = $presence->usera;
                return [
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ];
            }),
            // Ajouter les informations de pagination
            'pagination' => [
                'current_page' => $presences->currentPage(),
                'from' => $presences->firstItem(),
                'to' => $presences->lastItem(),
                'per_page' => $presences->perPage(),
                'total' => $presences->total(),
                'prev_page_url' => $presences->previousPageUrl(),
                'next_page_url' => $presences->nextPageUrl(),
            ],
        ];

        return response()->json($formattedResponse);
    }

    public function getListAbsence($event_id)
    {
        // Récupérer tous les utilisateurs de l'application sauf le rôle admin
        $usrs = User::where('role', '!=', 'admin')->get();

        // Récupérer tous les utilisateurs qui ont fait le premier scan
        $presences = Presence::where('evenement_id', $event_id)
            ->where('firstPresence', true)
            ->where('secondPresence', false)
            ->get();

        // Comparer les deux tableaux pour récupérer les absents
        $absents = $usrs->diff($presences);

        // Paginer les résultats
        $currentPage = request()->query('page', 1); // Récupérer le numéro de la page actuelle depuis la requête
        $perPage = 5;
        $offset = ($currentPage - 1) * $perPage;
        $currentPageItems = array_slice($absents->all(), $offset, $perPage);
        $absentsPaginated = new LengthAwarePaginator($currentPageItems, count($absents), $perPage, $currentPage);

        // Obtenir l'URL complète pour la page suivante
        $nextPageUrl = $absentsPaginated->currentPage() < $absentsPaginated->lastPage() ?
            url()->current() . '?page=' . ($absentsPaginated->currentPage() + 1) : null;

        // Obtenir l'URL complète pour la page précédente
        $prevPageUrl = $absentsPaginated->currentPage() > 1 ?
            url()->current() . '?page=' . ($absentsPaginated->currentPage() - 1) : null;

        // Ajouter les URL complètes à la réponse JSON
        $response = [
            'data' => $absentsPaginated->items(),
            'next_page_url' => $nextPageUrl,
            'prev_page_url' => $prevPageUrl
        ];
        return response()->json($response, 200);
    }

    //recuperer la liste de tous les invites qui sont venus au debut du soirer mais qui n'ont pas attendus jusqu'au contre scan (appele)
    public function getListPresenceFirst($event_id)
    {
        $presences = Presence::with('usera')->where('evenement_id', $event_id)->where('firstPresence', true)->where('secondPresence', false)->paginate(5);
        // Formater la réponse JSON
        $formattedResponse = [
            'data' => $presences->map(function ($presence) {
                // Extraire uniquement les informations nécessaires de l'utilisateur
                $user = $presence->usera;
                return [
                    'firstname' => $user->firstname,
                    'lastname' => $user->lastname,
                    'email' => $user->email,
                    'phone' => $user->phone,
                ];
            }),
            // Ajouter les informations de pagination
            'pagination' => [
                'current_page' => $presences->currentPage(),
                'from' => $presences->firstItem(),
                'to' => $presences->lastItem(),
                'per_page' => $presences->perPage(),
                'total' => $presences->total(),
                'prev_page_url' => $presences->previousPageUrl(),
                'next_page_url' => $presences->nextPageUrl(),
            ],
        ];

        return response()->json($formattedResponse);
    }

    public function sendSingleInvitation(evenement $event, User $user)
    {
        try {
            Mail::send('mails.invitationv2', ['evenement' => $event , 'user' => $user], function ($message) use ($user) {
                $message->to($user->email);
                $message->subject('Nouvel événement');
            });
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'envoie de l\'invitation',
                'error' => $e->getMessage()
            ], 500);
        }
        return response()->json([
            'message' => 'Invitation envoyée avec succès'
        ], 200);
    }

    public function getAllEvent()
    {
        $events = evenement::all();
        return response()->json($events);
    }

    public function sendQrAllUser()
    {
        $users = User::where('role', '!=', 'admin')->get();

        foreach ($users as $user) {
            try {
                // Générer le QR code à partir du badgeToken de l'utilisateur
                $qrCode = QrCode::size(200)->generate($user->badgeToken);

                // Générer le contenu HTML du PDF avec le QR code
                $pdfContent = '<h1>Bonjour ' . $user->firstname . ',</h1>';
                $pdfContent .= '<p>Voici votre QR Code :</p>';
                $pdfContent .= '<img src="data:image/png;base64,' . base64_encode($qrCode) . '">';
                $pdfContent .= '<p>Merci,<br>Votre équipe</p>';

                // Créer une instance de Dompdf
                $dompdf = new Dompdf();
                $dompdf->loadHtml($pdfContent);

                // Générer le PDF
                $dompdf->render();

                // Envoyer le PDF par courriel
                Mail::send([], [], function ($message) use ($user, $dompdf) {
                    $message->to($user->email)
                        ->subject('Votre QR Code')
                        ->html('Veuillez trouver ci-joint votre PDF avec le code QR.')
                        ->attachData($dompdf->output(), 'QR_Code.pdf', [
                            'mime' => 'application/pdf',
                        ]);
                });
            } catch (Exception $e) {
                return response()->json([
                    'message' => 'Erreur lors de l\'envoie de l\'invitation',
                    'error' => $e->getMessage()
                ], 500);
            }
        }
        return response()->json([
            'message' => 'Invitation envoyée avec succès'
        ], 200);
    }

    public function sendQrToUser(User $user)
    {
        try {
            // Générer le QR code à partir du badgeToken de l'utilisateur
            $qrCode = QrCode::size(200)->generate($user->badgeToken);

            // Générer le contenu HTML du PDF avec le QR code
            $pdfContent = '<h1>Bonjour ' . $user->firstname . ',</h1>';
            $pdfContent .= '<p>Voici votre QR Code :</p>';
            $pdfContent .= '<img src="data:image/png;base64,' . base64_encode($qrCode) . '">';
            $pdfContent .= '<p>Merci,<br>Votre équipe</p>';

            // Créer une instance de Dompdf
            $dompdf = new Dompdf();
            $dompdf->loadHtml($pdfContent);

            // Générer le PDF
            $dompdf->render();

            // Envoyer le PDF par courriel
            Mail::send([], [], function ($message) use ($user, $dompdf) {
                $message->to($user->email)
                    ->subject('Votre QR Code')
                    ->html('Veuillez trouver ci-joint votre PDF avec le code QR.')
                    ->attachData($dompdf->output(), 'QR_Code.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });

            return response()->json([
                'message' => 'PDF avec le code QR envoyé avec succès à ' . $user->email
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de l\'envoie du PDF avec le code QR à ' . $user->email,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
