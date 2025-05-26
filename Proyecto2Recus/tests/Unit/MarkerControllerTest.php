<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Marker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MarkerControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_all_markers()
    {
        // Hace como si el usuario se autentificara
        $user = User::factory()->create();
        $this->actingAs($user);

        // crear 3 marcadores
        Marker::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        // comprobar peticion i si el numero de marcadores es 3
        $response = $this->getJson('/api/markers');

        $response->assertStatus(200);
        $response->assertJsonCount(3); // Verifica que haya 3 resultados
    }
}
