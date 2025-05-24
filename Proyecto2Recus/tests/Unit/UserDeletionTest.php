<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_is_deleted()
    {
        // Crear un usuario
        $user = User::factory()->create();

        // Eliminar el usuario
        $user->delete();

        // Verificar que el usuario ha sido eliminado de la base de datos
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
