<?php

namespace Database\Seeders;

use App\Models\UnitType;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $unit_types = [
            'Count' => [
                [
                    'name' => 'Piece',
                    'symbol' => 'pc',
                    'is_base' => true,
                    'conversion_factor' => 1,
                    'conversion_factor_base' => 1,
                    'is_active' => true,
                    'description' => 'Piece',
                    'childred' => [
                        [
                            'name' => 'Box',
                            'symbol' => 'bx',
                            'is_base' => false,
                            'is_active' => true,
                            'description' => 'Box',
                            'children' => [
                                [
                                    'name' => 'Carton',
                                    'symbol' => 'ct',
                                    'is_base' => false,
                                    'is_active' => true,
                                    'description' => 'Carton',
                                ],
                            ]
                        ],
                        [
                            'name' => 'Dozen',
                            'symbol' => 'dz',
                            'is_base' => false,
                            'default_conversion_factor' => 12,
                            'is_active' => true,
                            'description' => 'Dozen',
                            'pre_defined' => true,
                        ],
                       
                    ]
                ],
               
            ]
            ];

        foreach ($unit_types as $unit_type_name => $unit_type_data) {
            $unit_type = UnitType::create([
                'name' => $unit_type_name,
                'is_active' =>  true,
            ]);
        }
      

    }
}
