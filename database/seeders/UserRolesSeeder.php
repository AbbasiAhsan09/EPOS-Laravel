<?php

namespace Database\Seeders;

use App\Models\UserRoles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Admin',
            'Sales',
            'Manager',
            'Moderator',
        ];
        foreach ($roles as $key => $value) {
            UserRoles::create(['role_name' => $value, 'status' => 1]);
        }
    }
}
