<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*
        $user = User::firstOrCreate([
            'id' => 999999,
            'name' => 'David',
            'username' => 'dherrera',
            'email' => 'admin@demo.com',
            'password' => bcrypt('12345678'),
            'desc' => 'Soy admin',
        ]);
        */

        $user = User::firstOrCreate([
            'id' => 100000,
            'name' => 'Nil',
            'username' => 'nilbalaguer',
            'email' => 'nilbalaguerfp@ibf.com',
            'password' => bcrypt('12345678'),
            'desc' => 'Soy admin',
        ]);

        $role = Role::create(['name' => 'admin']);
        $role2 = Role::create(['name' => 'user']);
        $permissions = [
            'post-list',
            'post-create',
            'post-edit',
            'post-delete',
            'post-list'
        ];
        $role2->syncPermissions($permissions);

        $permissions = Permission::pluck('id','id')->all();

        $role->syncPermissions($permissions);

        $user->assignRole([$role->id]);

        $user = User::create([
            'id' => 9888888,
            'name' => 'User',
            'username' => 'user',
            'email' => 'user@demo.com',
            'password' => bcrypt('12345678'),
            'desc' => 'Soy admin',
        ]);

        $user->assignRole([$role2->id]);

    }
}
