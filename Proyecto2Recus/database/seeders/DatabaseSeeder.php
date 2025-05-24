<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(user_table_seeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(CreateAdminUserSeeder::class);
        $this->call(friends_table_seeder::class);
        $this->call(marker_reviews_table_seeder::class);
        $this->call(marker_list_seeder::class);
        $this->call(markers_table_seeder::class);

        //$this->call(RoleSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
