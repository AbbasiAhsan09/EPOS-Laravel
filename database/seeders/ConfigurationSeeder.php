<?php

namespace Database\Seeders;

use App\Models\Configuration;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;

class ConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       $config = [
        'app_title' => 'TradeWisePOS',
        'logo' => null,
        'address' => 'demo stree',
        'phone' => '0311-309202',
        'ntn' => '11',
        'ptn' => '11',
        'show_ntn' => true,
        'show_ptn' => false,
        'inventory_tracking' => true,
        'mutltiple_sales_order' => false,
        'start_date' => date('Y-m-d' , time()),
        'contract_duration' => 12,
        'invoice_message' => 'kch bh',
        'inv_dev_message' => 'kch bh',
        'dev_contact' => '03113092942',
        'added_by' => 1,
        'store_id' => 1
       ];

       Configuration::create($config);
    }
}
