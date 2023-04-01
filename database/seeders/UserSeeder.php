<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Ahsan Abbasi',
            'email' => 'ahsanabbasi@gmail.com',
            'password' => Hash::make('1234567'),
            'role_id' => 1,
            'phone' => '03113092942'
        ]);
    }
}
