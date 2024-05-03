<?php

namespace App\Http\Controllers;

use App\Mail\EssaieEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailCOntroller extends Controller
{
    public function index(){
        $data = [
            'subject' => 'Je suis le subject mon pote' , 
            'body' => 'et je suis le body'
        ];

        $kim =  Mail::to('tsukasashishiosama@gmail.com')
        ->send(new EssaieEmail($data));

        if(!$kim) {
            return response()->json([
                'message' => 'esd email not sent'
            ]);
        }

        return response()->json([
            'message' => 'esd email sent'
        ]);

    }
}
