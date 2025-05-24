<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Friend>
 */
class FriendFactory extends Factory
{
    protected $model = Friend::class;

    public function definition()
    {
        return [
            'user_id' => \App\Models\User::factory(), // Si tienes una relación con el modelo User
            'friend_id' => \App\Models\User::factory(), // Similar si hay relación con User
            // Otros campos de tu tabla `friends`
        ];
    }
}
