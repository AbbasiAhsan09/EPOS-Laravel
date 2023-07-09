<?php

namespace Database\Seeders;

use App\Models\Stores;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StoreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $store = [
            'store_name' => 'Main',
            'store_phone' => '00',
            'store_location' => 'address',
            'type' => 'site',
            'store_supervisor' => 1,
            'status' => 1,
            'domain' => 'tradewise.com',
            'email' => 'contact@tradewise.com',
            'phone' => '03200681969',
        ];

        Stores::create($store);

    }
}
