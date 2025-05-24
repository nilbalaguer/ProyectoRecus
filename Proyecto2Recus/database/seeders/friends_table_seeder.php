<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Nette\Utils\Random;

class friends_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    $faker = Faker::create();

    foreach (range(1, 200) as $index) {
        // Generar IDs aleatorios para sender y receiver
        $sender = mt_rand(1, 50);
        $reciver = mt_rand(1, 50);

        // Evitar que sender y receiver sean iguales
        if ($sender == $reciver) {
            $sender += 1;

            if ($sender > 50)
                $sender = 1;
        }

        // Verificar que los IDs generados existan en la tabla 'users'
        if (DB::table('users')->where('id', $sender)->exists() && DB::table('users')->where('id', $reciver)->exists()) {
            DB::table('friends')->insert([
                'request_status' => $faker->boolean(),
                'sender_user_id' => $sender,
                'reciver_user_id' => $reciver
            ]);
        } else {
            // Si no existen, podrías omitir la inserción o registrar un log para revisar
            dump("Error: Los usuarios con ID $sender o $reciver no existen.");
        }
    }
}

}
