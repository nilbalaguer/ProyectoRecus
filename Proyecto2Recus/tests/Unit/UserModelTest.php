<?php

namespace Tests\Unit;

use App\Models\User;
use Hash;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_create_user_model(): void
    {
        $user = User::create([
            'username' => 'nilbalaguer',
            'name' => 'Nil Balaguer',
            'email' => 'nilbalaguer@ibf.cat',
            'password' => Hash::make('1234'),
            'last_lng' => 0,
            'last_lat' => 0,
            'desc' => 'hola',
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'nilbalaguer',
            'email' => 'nilbalaguer@ibf.cat',
        ]);

        $this->assertTrue(Hash::check('1234', $user->password));
    }
}
