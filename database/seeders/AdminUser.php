<?php

namespace Database\Seeders;

use App\Models\Billet;
use App\Models\evenement;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'firstname' => 'test',
            'lastname' => 'test',
            'email' => 'test@gmail.com',
            'phone' => '123456789',
            'password' => Hash::make('admin'),
            'role' => 'admin',
            'badgeToken' => 'test',
        ]);

        User::create([
            'firstname' => 'Organisateur',
            'lastname' => 'Organisateur',
            'email' => 'akutagawakarim@gmail.com',
            'phone' => '123456789',
            'password' => Hash::make('karimkarim'),
            'role' => 'organisateur',
            'badgeToken' => 'organisateur',
        ]);

        User::create([
            'firstname' => 'CLient',
            'lastname' => 'CLient',
            'email' => 'tsukasashishiosama@gmail.com',
            'phone' => '123456789',
            'password' => Hash::make('karimkarim'),
            'role' => '0',
            'badgeToken' => 'client'
        ]);

        //Evenement with faker  
        for ($i = 0; $i < 10; $i++) {
            evenement::create([
                'titre' => 'Evenement ' . $i,
                'description' => 'Description ' . $i,
                'date' => '2021-12-12',
                'heure' => '12:00',
                'lieu' => 'Lieu ' . $i,
                'user_id' => 2,
            ]);
        }


    
        
    }
}
