<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Stmt\TryCatch;

class UnitController extends Controller
{
    public function index(Request $request)
    {
        try {

            $unit_types = UnitType::all();

            $units = Unit::filterByStore();

            if ($request->query("unit_type")) {
                $units = $units->where("unit_type_id", $request->query("unit_type"));
            }

            $units = $units->get();


            return view('units.index', compact('unit_types'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create(Request $request)
    {
        try {

            return view('unit.create');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function store(Request $request)
    {
        try {

            return redirect()->route('unit.index');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function edit(Request $request, $id)
    {
        try {

            return view('unit.edit');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function update(Request $request, $id)
    {
        try {

            return redirect()->route('unit.index');
        } catch (\Throwable $th) {
            throw $th;
        }
    }
    public function destroy(Request $request, $id)
    {
        try {

            return redirect()->route('unit.index');
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function generate_units()
    {
        try {
            $unit_types = [
                'Count' => [
                    [
                        'name' => 'Piece',
                        'symbol' => 'pc',
                        'is_base' => true,
                        'is_active' => true,
                        'default_conversion_factor' => 1,
                        'description' => 'Piece',
                        'children' => [
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

                ],
                'Weight' => [
                    [
                        'name' => 'Kilogram',
                        'symbol' => 'kg',
                        'is_base' => true,
                        'is_active' => true,
                        'pre_defined' => true,
                        'default_conversion_factor' => 1,
                        'description' => 'Kilogram',
                        'children' => [
                            [
                                'name' => 'Gram',
                                'symbol' => 'g',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Gram',
                                'default_conversion_factor' => 0.001,
                            ],
                            [
                                'name' => 'Ton',
                                'symbol' => 't',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Ton',
                                'default_conversion_factor' => 1000,
                            ],
                            [
                                'name' => 'Pound',
                                'symbol' => 'lb',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Pound',
                                'default_conversion_factor' => 0.45359237,
                            ],
                            [
                                'name' => 'Ounce',
                                'symbol' => 'oz',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Ounce',
                                'default_conversion_factor' => 0.0283495,
                            ],
                            [
                                'name' => 'Milligram',
                                'symbol' => 'mg',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Milligram',
                                'default_conversion_factor' => 0.000001,
                            ],
                            [
                                'name' => 'Stone',
                                'symbol' => 'st',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Stone',
                                'default_conversion_factor' => 6.35029318,
                            ],
                            [
                                'name' => 'Carat',
                                'symbol' => 'ct',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Carat',
                                'default_conversion_factor' => 0.0002,
                            ],
                            [
                                'name' => 'Metric Ton',
                                'symbol' => 'mt',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Metric Ton',
                                'default_conversion_factor' => 1000,
                            ],
                            [
                                'name' => 'Short Ton',
                                'symbol' => 'st',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Short Ton',
                                'default_conversion_factor' => 907.18474,
                            ],
                            [
                                'name' => 'Long Ton',
                                'symbol' => 'lt',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Long Ton',
                                'default_conversion_factor' => 1016.0469088,
                            ],
                        ]
                    ]
                ],
                'Liter' => [
                    [
                        'name' => 'Liter',
                        'symbol' => 'l',
                        'is_base' => true,
                        'is_active' => true,
                        'pre_defined' => true,
                        'default_conversion_factor' => 1,
                        'description' => 'Liter',
                        'children' => [
                            [
                                'name' => 'Milliliter',
                                'symbol' => 'ml',
                                'is_base' => false,
                                'is_active' => true,
                                'pre_defined' => true,

                                'description' => 'Milliliter',
                                'default_conversion_factor' => 0.001,
                            ],
                            
                        ]
                    ]
                ]
            ];



            foreach ($unit_types as $unit_type_name => $unit_type_data) {

                dump($unit_type_name);
                $unit_type = null;

                $unit_type = UnitType::where('name', $unit_type_name)->first();

                if (!$unit_type) {
                    $unit_type = UnitType::create([
                        'name' => $unit_type_name,
                        'is_active' =>  true,
                    ]);
                }

                dump($unit_type);
                $this->seed_units($unit_type_data, $unit_type->id, null);

            }
            
            return response()->json([
                'status' => true,
                'message' => 'Units generated successfully',
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function seed_units($units, $unit_type_id, $conversion_unit_id = null)
    {
        try {

            foreach ($units as $key => $unit_data) {
                $unit = Unit::firstOrCreate(
                    [
                        'name' => $unit_data['name'],
                        'symbol' => $unit_data['symbol'],
                        'is_base' => $unit_data['is_base'],
                        'pre_defined' => $unit_data['pre_defined'] ?? false,
                        'unit_type_id' => $unit_type_id ?? null,
                        'conversion_unit_id' => $conversion_unit_id  ?? null,
                        'store_id' => Auth::user()->store_id,
                    ],
                    [
                        'default_conversion_factor' => $unit_data['default_conversion_factor'] ?? 1,
                        'is_active' => $unit_data['is_active'],
                        'description' => $unit_data['description'] ?? null,
                        'created_by' => auth()->user()->id,
                    ]
                );

                if (isset($unit_data['children']) && is_array($unit_data['children']) && count($unit_data['children']) > 0) {

                    $this->seed_units($unit_data['children'], $unit_type_id, $unit->id);
                }
            }
        } catch (\Throwable $th) {

            throw $th;
        }
    }

    public function get_units_by_unit_type($unit_type_id, $store_id)
    {
        try {

            $units = Unit::where('unit_type_id', $unit_type_id)
                ->where('store_id', $store_id)
                ->where("is_active", true)
                ->with("conversion_unit")
                ->get();

            return response()->json([
                'status' => true,
                'units' => $units,
            ]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
