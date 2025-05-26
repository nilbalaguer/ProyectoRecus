<?php

namespace Database\Factories;

use App\Models\Marker;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarkerFactory extends Factory
{
    protected $model = Marker::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->city,
            'description' => $this->faker->sentence,
            'lat' => $this->faker->latitude,
            'lng' => $this->faker->longitude,
            'zoom' => $this->faker->randomFloat(1, 10, 18),
            'pitch' => $this->faker->numberBetween(0, 80),
            'bearing' => $this->faker->numberBetween(-180, 180),
            'marker_list_id' => null, // o algo como MarkerList::factory()
            'user_id' => $this->faker->numberBetween(1, 50),
        ];
    }
}
