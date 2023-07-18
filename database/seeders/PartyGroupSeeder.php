<?php

namespace Database\Seeders;

use App\Models\PartyGroups;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PartyGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = ['Customer','Vendor'];
        foreach ($groups as $key => $group) {
            PartyGroups::create(['group_name' => $group]);
        }
    }
}
