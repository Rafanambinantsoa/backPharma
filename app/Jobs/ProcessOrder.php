<?php

namespace App\Jobs;

use App\Models\BilletTemp;
use App\Models\Commande;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class ProcessOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $pdfs;
    private $email;
    private $commandeTemp;
    private $billetsTemp;


    /**
     * Create a new job instance.
     */
    public function __construct($pdfs, $email, $commandeTemp, $billetsTemp)
    {
        $this->pdfs = $pdfs;
        $this->email = $email;
        $this->commandeTemp = $commandeTemp;
        $this->billetsTemp = $billetsTemp;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::raw('Veuillez trouver ci-joint vos PDF avec les codes QR.', function ($message) {
            $message->to($this->email)
                ->subject('PDFs avec codes QR');
    
            foreach ($this->pdfs as $pdf) {
                $message->attachData($pdf['content'], $pdf['filename'], [
                    'mime' => 'application/pdf',
                ]);
            }
        });
    
        // Insérer des données dans la base de données
        BilletTemp::insert($this->billetsTemp);
        Commande::insert($this->commandeTemp);
    }
}
