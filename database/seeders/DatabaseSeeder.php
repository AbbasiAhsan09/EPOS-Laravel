<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserRolesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(StoreSeeder::class);    
        $this->call(ConfigurationSeeder::class);
        $this->call(PartyGroupSeeder::class);
    }
}
