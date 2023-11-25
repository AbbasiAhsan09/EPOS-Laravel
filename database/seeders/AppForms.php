<?php

namespace Database\Seeders;

use App\Models\AppForms as ModelsAppForms;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AppForms extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $forms = ['product','sales','purchase_order','purchase_invoice','category','field','parties'];
        $check = ModelsAppForms::all();
        if(!$check){
            foreach ($forms as $key => $value) {
                ModelsAppForms::create(['name' => $value]);
            }
        }
    }
}
