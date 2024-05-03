<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use SimpleSoftwareIO\QrCode\Facades\QrCode;



class EssaieCOntroller extends Controller
{
   public function index()
   {   // Contenu du PDF
      $pdfContent = '<h1>Bonjour</h1>';

      // Générer le contenu du code QR
      $qrCodeContent = uniqid();
      $qrCode = QrCode::size(200)->generate($qrCodeContent);

      // Combiner le contenu du PDF et le code QR
      $pdfContent .= '<img src="data:image/png;base64,' . base64_encode($qrCode) . '">';

      // Créer une instance de l'objet PDF
      $pdf = app('dompdf.wrapper');
      $pdf->loadHTML($pdfContent);

      // Générer le PDF en tant que chaîne binaire
      $pdfBinary = $pdf->output();

      // Envoi du PDF par e-mail
      Mail::raw('Veuillez trouver ci-joint votre PDF avec le code QR.', function ($message) use ($pdfBinary) {
         $message->to('tsukasashishiosama@gmail.com')
            ->subject('PDF avec code QR');
         $message->attachData($pdfBinary, 'qr_code.pdf', [
            'mime' => 'application/pdf',
         ]);
      });

      return response()->json([
         'message' => 'sent'
      ]);
   }

   public function test(){
      
   }
}
