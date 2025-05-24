<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_be_created_and_has_role_assigned()
    {
        $role = Role::create(['name' => 'user']);

        // Autenticación simulada
        $this->actingAs(User::factory()->create());

        $requestData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'username' => 'testuser',
            'desc' => 'Descripción de prueba',
            'password' => 'secret123',
            'role_id' => $role->id,
        ];

        $response = $this->postJson('/api/users', $requestData); // Asegurate que esta ruta sea correcta

        $response->assertStatus(201); // Cambiar a 200 si tu controlador devuelve 200 en vez de 201
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);

        $user = User::where('email', 'test@example.com')->first();
        $this->assertTrue($user->hasRole('user'));
    }
}
