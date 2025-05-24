<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Nette\Utils\Random;

class markers_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            ["name" => "New York", "description" => "La ciudad que nunca duerme", "lat" => 40.7128, "lng" => -74.0060],
            ["name" => "Tokyo", "description" => "La metrópolis futurista", "lat" => 35.6895, "lng" => 139.6917],
            ["name" => "Paris", "description" => "La ciudad del amor", "lat" => 48.8566, "lng" => 2.3522],
            ["name" => "London", "description" => "Histórica y moderna a la vez", "lat" => 51.5074, "lng" => -0.1278],
            ["name" => "Barcelona", "description" => "Arte, arquitectura y playa", "lat" => 41.3851, "lng" => 2.1734],
            ["name" => "Dubai", "description" => "Lujo en el desierto", "lat" => 25.276987, "lng" => 55.296249],
            ["name" => "Sydney", "description" => "La joya de Australia", "lat" => -33.8688, "lng" => 151.2093],
            ["name" => "Rome", "description" => "Cuna del Imperio Romano", "lat" => 41.9028, "lng" => 12.4964],
            ["name" => "Istanbul", "description" => "Puente entre Europa y Asia", "lat" => 41.0082, "lng" => 28.9784],
            ["name" => "Singapore", "description" => "La ciudad jardín del futuro", "lat" => 1.3521, "lng" => 103.8198],
            ["name" => "Los Angeles", "description" => "La capital del cine", "lat" => 34.0522, "lng" => -118.2437],
            ["name" => "San Francisco", "description" => "Puentes y colinas", "lat" => 37.7749, "lng" => -122.4194],
            ["name" => "Amsterdam", "description" => "Canales y bicicletas", "lat" => 52.3676, "lng" => 4.9041],
            ["name" => "Berlin", "description" => "Historia y modernidad", "lat" => 52.5200, "lng" => 13.4050],
            ["name" => "Hong Kong", "description" => "Skylines y cultura", "lat" => 22.3193, "lng" => 114.1694],
            ["name" => "Buenos Aires", "description" => "Pasión por el tango", "lat" => -34.6037, "lng" => -58.3816],
            ["name" => "Lima", "description" => "Sabor y cultura andina", "lat" => -12.0464, "lng" => -77.0428],
            ["name" => "Cape Town", "description" => "Belleza natural en África", "lat" => -33.9249, "lng" => 18.4241],
            ["name" => "Seoul", "description" => "Tecnología y tradición", "lat" => 37.5665, "lng" => 126.9780],
            ["name" => "Bangkok", "description" => "Caos encantador", "lat" => 13.7563, "lng" => 100.5018],
            ["name" => "Moscow", "description" => "Grandeza imperial", "lat" => 55.7558, "lng" => 37.6173],
            ["name" => "Toronto", "description" => "Diversidad y desarrollo", "lat" => 43.651070, "lng" => -79.347015],
            ["name" => "Vancouver", "description" => "Naturaleza y urbanismo", "lat" => 49.2827, "lng" => -123.1207],
            ["name" => "Madrid", "description" => "Corazón de España", "lat" => 40.4168, "lng" => -3.7038],
            ["name" => "Lisbon", "description" => "Encanto atlántico", "lat" => 38.7169, "lng" => -9.1399],
            ["name" => "Mexico City", "description" => "Gigante cultural", "lat" => 19.4326, "lng" => -99.1332],
            ["name" => "Rio de Janeiro", "description" => "Playas y carnaval", "lat" => -22.9068, "lng" => -43.1729],
            ["name" => "Athens", "description" => "Cuna de la civilización", "lat" => 37.9838, "lng" => 23.7275],
            ["name" => "Vienna", "description" => "Capital musical", "lat" => 48.2082, "lng" => 16.3738],
            ["name" => "Prague", "description" => "Encanto medieval", "lat" => 50.0755, "lng" => 14.4378],
            ["name" => "Copenhagen", "description" => "Diseño y sostenibilidad", "lat" => 55.6761, "lng" => 12.5683],
            ["name" => "Dublin", "description" => "Pubs e historia", "lat" => 53.3498, "lng" => -6.2603],
            ["name" => "Helsinki", "description" => "Minimalismo nórdico", "lat" => 60.1699, "lng" => 24.9384],
            ["name" => "Stockholm", "description" => "Belleza escandinava", "lat" => 59.3293, "lng" => 18.0686],
            ["name" => "Oslo", "description" => "Entre fiordos y arte", "lat" => 59.9139, "lng" => 10.7522],
            ["name" => "Reykjavik", "description" => "Puerta a la naturaleza", "lat" => 64.1466, "lng" => -21.9426],
            ["name" => "Marrakesh", "description" => "Misterio y mercados", "lat" => 31.6295, "lng" => -7.9811],
            ["name" => "Casablanca", "description" => "Cultura marroquí costera", "lat" => 33.5731, "lng" => -7.5898],
            ["name" => "Hanoi", "description" => "Caos y cultura vietnamita", "lat" => 21.0285, "lng" => 105.8542],
            ["name" => "Kuala Lumpur", "description" => "Rascacielos y tradición", "lat" => 3.1390, "lng" => 101.6869],
            ["name" => "Jakarta", "description" => "Megaciudad indonesia", "lat" => -6.2088, "lng" => 106.8456],
            ["name" => "Taipei", "description" => "Tecnología y comida", "lat" => 25.032969, "lng" => 121.565418],
            ["name" => "Manila", "description" => "Energía filipina", "lat" => 14.5995, "lng" => 120.9842],
            ["name" => "Bogotá", "description" => "Altitud y cultura", "lat" => 4.7110, "lng" => -74.0721],
            ["name" => "Santiago", "description" => "Entre montañas y modernidad", "lat" => -33.4489, "lng" => -70.6693],
            ["name" => "Quito", "description" => "Al pie de los Andes", "lat" => -0.1807, "lng" => -78.4678],
            ["name" => "Lagos", "description" => "Potencia africana emergente", "lat" => 6.5244, "lng" => 3.3792],
            ["name" => "Nairobi", "description" => "Urbano y salvaje", "lat" => -1.2921, "lng" => 36.8219],
            ["name" => "Doha", "description" => "Lujo en el Golfo", "lat" => 25.276987, "lng" => 51.5200],
            ["name" => "Riyadh", "description" => "Capital del reino", "lat" => 24.7136, "lng" => 46.6753],
        ];

        foreach ($cities as $city) {
            DB::table('markers')->insert([
                'name' => $city['name'],
                'description' => $city['description'],
                'lat' => $city['lat'],
                'lng' => $city['lng'],
                'zoom' => mt_rand(140, 160) / 10, // 7.0 - 14.0
                'pitch' => mt_rand(0, 80),
                'bearing' => mt_rand(-180, 180),
                'marker_list_id' => 999,
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }


        $faker = Faker::create();

        foreach ($cities as $city) {
            DB::table('markers')->insert([
                'name' => $city['name'],
                'description' => $faker->sentence,
                'lat' => $faker->latitude(),
                'lng' => $faker->longitude(),
                'zoom' => $faker->randomFloat(1, 10, 18),
                'pitch' => $faker->numberBetween(0, 80),
                'bearing' => $faker->numberBetween(-180, 180),
                'user_id' => $faker->numberBetween(0, 50), 
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
