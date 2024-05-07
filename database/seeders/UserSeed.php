<?php

namespace Database\Seeders;

use App\Models\Presence;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory;
use Illuminate\Support\Facades\DB;

class UserSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            User::create([
                'firstname' => 'firstname ' . $i,
                'lastname' => 'lastname ' . $i,
                'email' => 'email' . $i . '@gmail.com',
                'phone' => '123456789',
                'badgeToken' => 'badgeToken' . $i,
            ]);
        }

        //generate a presence for each user expect for the role admin
        $users = User::where('role', '!=', 'admin')->get();

        foreach ($users as $user) {
            Presence::create([
                'user_id' => $user->id,
                'evenement_id' => $faker->numberBetween(1, 10),
                'firstPresence' => $faker->boolean,
            ]);
        }

    }
}
