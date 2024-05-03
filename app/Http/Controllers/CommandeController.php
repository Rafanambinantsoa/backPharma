<?php

namespace App\Http\Controllers;

use App\Models\Billet;
use App\Models\BilletTemp;
use App\Models\Commande;
use App\Models\CommandeTemp;
use App\Models\evenement;
use App\Models\User;
use Barryvdh\DomPDF\PDF;
use Illuminate\Http\Request;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;


class CommandeController extends Controller
{
    public function cancel()
    {
        $message = "Votre paiement a été annulé";
        return redirect('http://localhost:3000/.?message=' . urlencode($message));
    }

   

    

    public function redirigerVersPageLocale()
    {
        //mutation des donne dans billetstemp en billet
        $billetsTemp = BilletTemp::all();
        foreach ($billetsTemp as $element) {
            Billet::create([
                'user_id' => $element->user_id,
                'title' => $element->title,
                'event_id' => $element->event_id,
                'prix' => $element->prix,
                'token' => $element->token , 
                'userBuy' => $element->userBuy,
            ]);
        }
        //mutation des donne dans commandetemp en commande
        $commandeTemp = CommandeTemp::all();
        foreach ($commandeTemp as $element) {
            Commande::create([
                'user_id' => $element->user_id,
                'titreEvent' => $element->titreEvent,
                'event_id' => $element->event_id,
                'quantite' => $element->quantite,
                'montantTotal' => $element->montantTotal,
                'email' => $element->email,
            ]);
        }

        // generation des pdfs pour les billets avec la quantite mentionner dans la commande
        $billets = BilletTemp::all();
        foreach ($billets as $element) {
            $pdfContent = '<h1>' . $element->title . '</h1>';
            $qrCodeContent = $element->token;
            $qrCode = QrCode::size(200)->generate($qrCodeContent);
            $pdfContent .= '<img src="data:image/png;base64,' . base64_encode($qrCode) . '">';

            $pdf = app('dompdf.wrapper');
            $pdf->loadHTML($pdfContent);
            $pdfs[] = [
                'content' => $pdf->output(),
                'filename' => 'Billets numero' . $element->id . '.pdf'
            ];
        }
        // recupere le premier l'addrese email stocker dans la commandetemp
        $email = "";
        foreach ($commandeTemp as $element) {
            $email = $element->email;
            break;
        }
        //envoie des pdfs par email 
        Mail::raw('Veuillez trouver ci-joint vos PDF avec les codes QR.', function ($message) use ($pdfs, $email) {
            $message->to($email)
                ->subject('PDFs avec codes QR');

            foreach ($pdfs as $pdf) {
                $message->attachData($pdf['content'], $pdf['filename'], [
                    'mime' => 'application/pdf',
                ]);
            }
        });




        //suppression des donnees dans commandetemp
        CommandeTemp::truncate();
        //suppression des donnees dans billetstemp
        BilletTemp::truncate();

        $parametre = uniqid() . bin2hex(random_bytes(16));
        return redirect('http://localhost:3000/?code=' . urlencode($parametre));
    }
    public function index(Request $request)
    {
        // Enregistrement de la commande et aussi du billets dans  la bd 
        $donnees = $request->data; // Récupérer les données de la requête
        $email = [$request->email];
        foreach ($donnees as $element) {
            //Decrement le nombre de billets disponible
            $event = evenement::find($element['id']);
            $event->update([
                'limitBillets' => $event->limitBillets - $element['quantity'],
                'billetsVendus' => $event->billetsVendus + $element['quantity']
            ]);
            $event->save();
            for ($i = 0; $i < $element['quantity']; $i++) {
                $randomString = bin2hex(random_bytes(16)); // Génère une chaîne hexadécimale aléatoire de 32 caractères
                $token =  uniqid() . $randomString;
                // Stocker les informations dans le tableau temporaire
                BilletTemp::create([
                    'user_id' => $element['user_id'],
                    'title' => $element['titre'],
                    'event_id' => $element['id'],
                    'prix' => $element['prix'],
                    'token' => $token , 
                    'userBuy' => $request->user_id,
                ]);

                //Generation des pdfs pour les billets
                $pdfContent = '<h1>' . $element['titre'] . '</h1>';
                $qrCodeContent = $token;
                $qrCode = QrCode::size(200)->generate($qrCodeContent);
                $pdfContent .= '<img src="data:image/png;base64,' . base64_encode($qrCode) . '">';

                $pdf = app('dompdf.wrapper');
                $pdf->loadHTML($pdfContent);
                $pdfs[] = [
                    'content' => $pdf->output(),
                    'filename' => 'Billets numero' . $i . '.pdf'
                ];
            }

            /// Insérer chaque élément dans la base de données//b n
            CommandeTemp::create([
                'user_id' => $request->user_id,
                'titreEvent' => $element['titre'],
                'event_id' => $element['id'],
                'quantite' => $element['quantity'],
                'montantTotal' => $element['quantity'] * $element['prix'],
                'email' => $request->email,
            ]);
        }

        \Stripe\Stripe::setApiKey(config('stripe.sk'));

        $session = \Stripe\Checkout\Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'mga',
                    'product_data' => [
                        'name' => 'Achats de billets avec EventPass',
                    ],
                    'unit_amount' => $request->total,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => route('success'),
            'cancel_url' => route('cancel'),
        ]);

        return response($session->url);
    }

    //Historique du client connecter
    public function historiqueParUser(User $user)
    {
        $data = $user->commandes()->get();
        return response()->json($data);
    }
    //recuperer le chiffres le revenus total d'un organisateur
    public function revenusParOrganisateur(User $user)
    {
        $data = $user->eventy()->get();
        $revenus = 0;
        foreach ($data as $element) {
            $revenus += $element->billetsVendus * $element->prix;
        }

        return response()->json($revenus);
    }

    public function success()
    {
        return response()->json(['message' => 'Payment successfully done']);
    }

    //somme de tous les montants des commandes
    public function totalCommande()
    {
      $monntanTotal = Commande::sum('montantTotal');
      $total = intval($monntanTotal); // Cast to integer
        return response()->json($total);
    }   
}
