<?php

namespace App\Http\Controllers;

use App\Helpers\ConfigHelper;
use App\Imports\ProductImport;
use App\Models\AppFormFields;
use App\Models\AppFormFieldsData;
use App\Models\AppForms;
use App\Models\Fields;
use App\Models\Inventory;
use App\Models\MOU;
use App\Models\ProductArrtributes;
use App\Models\ProductCategory;
use App\Models\Products;
use App\Models\ProductUnit;
use App\Models\Unit;
use App\Models\UnitType;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use PhpParser\Node\Stmt\TryCatch;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        session()->forget('filter');
        $uom = MOU::filterByStore()->get();
        $categories = ProductCategory::all();
        $items = Products::with('categories', 'uoms')
            ->when($request->has('filter') && $request->filter != null,  function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request->filter . '%');
                session()->put('filter', $request->filter);
            })
            ->byUser()
            ->paginate(20)->withQueryString();
        // dd($items);
        // dd($items);
        $arrt = ProductArrtributes::all();
        $dynamicFields = AppForms::where("name", 'product')
            ->with("fields")->whereHas("fields", function ($query) {
                $query->filterByStore();
            })->first();
        // dd($dynamicFields);
        return view('items.index', compact('items', 'dynamicFields', 'uom', 'categories', 'arrt'));
    }


    public function create($product_id = null)
    {
        try {

            $isEditMode = false;
            $product = null;
            if (isset($product_id) && !empty($product_id)) {
                $product = Products::where("id", $product_id)->with('product_units.unit.conversion_unit')->filterByStore()->first();
                if ($product) {
                    $isEditMode = true;
                }
            }
            // dd($product->unit_type_id);

            $unit_types = UnitType::all();

            return view('items.form', compact('product', 'isEditMode', 'unit_types'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function store(Request $request)
    {

        try {
            // dd($request->all());
            // dd($request['unit_conversion']["default"]);
            $validate = $request->validate([
                'code' => 'required|unique:products,barcode,id,store_id',
            ]);
            if ($validate) {
                $product = new Products();
                $product->name = $request->product;
                $product->barcode = $request->code;
                $product->uom = $request->uom ?? 1;
                $product->category = $request->has("category") && !empty($request->category) ? $request->category : $this->get_common_field_category_ids()["category_id"];
                $product->mrp = (int)$request->mrp ?? 0;
                $product->low_stock = (int)$request->low_stock ?? 0;
                $product->tp = (int)$request->tp ?? 0;
                $product->taxes = (int)$request->tax ?? 0;
                $product->store_id = Auth::user()->store_id;
                $product->img = $request->img;
                $product->unit_type_id = isset($request->unit_type_id) && !empty($request->unit_type_id) ? $request->unit_type_id : null;
                $product->brand = $request->brand;
                $product->description = $request->description;
                $product->opening_stock = (int)$request->opening_stock ?? 0;
                $product->opening_stock_unit_cost = (int)$request->opening_stock_unit_cost ?? 0;
                $product->check_inv = isset($request->check_inv) && $request->check_inv ? true : false;
                // Product IMage Logic
                if ($request->hasFile('image')) {
                    $image = $request->file('image');

                    // Generating a unique name for the file
                    $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                    // dd($fileName);
                    $public_path = "images/" . Auth::user()->store_id . "/products";
                    $image->move(public_path($public_path), $fileName);
                    $product->img = $fileName; // Assign the file name to the product image attribute
                }

                $product->save();

                // 
                if (isset($request->dynamicFields) && count($request->dynamicFields)) {
                    foreach ($request->dynamicFields as $key => $value) {
                        if (!empty($value)) {
                            $form = AppForms::where("name", 'product')->first();
                            foreach ($value as $key => $field_value) {
                                $form_field = AppFormFields::where('form_id', $form->id)->where('name', $key)->filterByStore()->first();
                                if ($form_field) {
                                    AppFormFieldsData::create([
                                        'form_id' => $form->id,
                                        'field_id' => $form_field->id,
                                        'value' => $field_value,
                                        'related_to' => $product->id,
                                        'store_id' => Auth::user()->store_id ?? null
                                    ]);
                                }
                            }
                        }
                    }
                }

                if ($request->unit_type_id) {
                    if (isset($request->unit_conversion) && count($request->unit_conversion)) {

                        ProductUnit::where("product_id", $product->id)->delete();
                        foreach ($request->unit_conversion as $key => $value) {

                            if (!empty($value)) {
                                if ($key === 'default') {
                                    $product->update([
                                        'default_unit_id' => $value
                                    ]);
                                } else {
                                    if ($value["id"]) {
                                        $unit = Unit::find($value["id"]);
                                        if ($unit) {
                                            ProductUnit::create([
                                                'product_id' => $product->id,
                                                'unit_id' => $unit->id,
                                                'conversion_rate' => $value["qty"],
                                                'unit_cost' => $value["cost"],
                                                'unit_rate' => $value["rate"],
                                                'unit_barcode' => isset($value["barcode"]) && $value["barcode"] ? $value["barcode"] : null,
                                                'description' => isset($value["description"]) && $value["description"] ? $value["description"] : null,
                                                'default' => isset($request['unit_conversion']["default"]) && ($request['unit_conversion']["default"] == $unit->id) && (isset($value["is_active"]) && $value["is_active"]) ? 1 : 0,
                                                'is_active' => isset($value["is_active"]) && $value["is_active"] ? 1 : 0,
                                                'symbol' => $unit->symbol ?? null,
                                                'convert_to_unit_id' => $unit->conversion_unit_id ?? null,
                                                'store_id' => Auth::user()->store_id ?? null
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    $this->setProductUnitConversionMultiplier($product->id);
                } else {
                    ProductUnit::where("product_id", $product->id)->delete();
                }


                toast('Product Added!', 'success');
                return redirect()->back();
            } else {
                toast('Product Code Cannot Be Duplicated!', 'error');
                return redirect()->withErrors($validate)->back()->withInput();
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(int $id, Request $request)
    {
        try {

            $product =  Products::find($id);
            $oldQty = (int)$product->opening_stock ?? 0;
            $product->name = $request->product;
            $product->barcode = $request->code;
            $product->uom = $request->uom ?? 1;
            $product->category = $request->has("category") && !empty($request->category) ? $request->category : $this->get_common_field_category_ids()["category_id"];
            $product->mrp = (int)$request->mrp ?? 0;
            $product->low_stock = (int)$request->low_stock ?? 0;
            $product->opening_stock = !empty($request->opening_stock) ? $request->opening_stock : 0;
            $product->tp = (int)$request->tp ?? 0;
            $product->taxes = (int)$request->tax ?? 0;
            $product->opening_stock_unit_cost = (int)$request->opening_stock_unit_cost ?? 0;
            $product->check_inv = isset($request->check_inv) && $request->check_inv ? true : false;
            $product->unit_type_id = isset($request->unit_type_id) && !empty($request->unit_type_id) ? $request->unit_type_id : null;

            // $product->store_id = 1;
            // Product IMage Logic
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Generating a unique name for the file
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                // dd($fileName);
                $public_path = "images/" . Auth::user()->store_id . "/products";
                $image->move(public_path($public_path), $fileName);
                $product->img = $fileName; // Assign the file name to the product image attribute
            }

            $product->brand = $request->brand;
            $product->description = $request->description;
            $product->save();

            // if($product){
            //     $newQty = ((int)($product->opening_stock)) ? (int)($product->opening_stock) : 0;
            //     $diffQty = $newQty - $oldQty;
            //     $uom = MOU::find($request->uom);
            //     $inventory = Inventory::firstOrCreate([
            //         'is_opnening_stock' => 0,
            //         'item_id' => $product->id
            //     ]);

            //     $inventory->update([
            //         'stock_qty' => ($inventory->stock_qty + ($diffQty * (isset($uom->base_unit_value) ? $uom->base_unit_value : 1)))
            //     ]);
            // }



            if ($request->unit_type_id) {
                if (isset($request->unit_conversion) && count($request->unit_conversion)) {

                    ProductUnit::where("product_id", $product->id)->forceDelete();
                    foreach ($request->unit_conversion as $key => $value) {

                        if (!empty($value)) {
                            if ($key === 'default') {
                                $product->update([
                                    'default_unit_id' => $value
                                ]);
                            } else {
                                if ($value["id"]) {
                                    $unit = Unit::where('id', $value["id"])->with("conversion_unit")->first();

                                    if ($unit) {
                   
                                        ProductUnit::create([
                                            'product_id' => $product->id,
                                            'unit_id' => $unit->id,
                                            'conversion_rate' => $value["qty"],
                                            'unit_cost' => $value["cost"],
                                            'unit_rate' => $value["rate"],
                                            'unit_barcode' => isset($value["barcode"]) && $value["barcode"] ? $value["barcode"] : null,
                                            'description' => isset($value["description"]) && $value["description"] ? $value["description"] : null,
                                            'default' => isset($request['unit_conversion']["default"]) && ($request['unit_conversion']["default"] == $unit->id) && (isset($value["is_active"]) && $value["is_active"]) ? 1 : 0,
                                            'is_active' => isset($value["is_active"]) && $value["is_active"] ? 1 : 0,
                                            'symbol' => $unit->symbol ?? null,
                                            'convert_to_unit_id' => $unit->conversion_unit_id ?? null,
                                            'store_id' => Auth::user()->store_id ?? null,
                                            'conversion_multiplier' => 1,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
                $this->setProductUnitConversionMultiplier($product->id);
            } else {
                ProductUnit::where("product_id", $product->id)->forceDelete();
            }
           
            toast('Product Updated!', 'info');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function getBaseUnitMultiplier($unit, $qty = 1)
    {
        $multiplier = $qty *1;
    
        while ($unit && $unit->conversion_unit) {
            // dump($unit->conversion_unit);
            $conversionQty = $unit->default_conversion_factor ?? 1;
            $multiplier *= $conversionQty;
            $unit = $unit->conversion_unit;
        }
        // dump($unit);
        return $multiplier;
    }

//     function setProductUnitConversionMultiplier($product_id)
// {
//     try {
//         $productUnits = ProductUnit::where('product_id', $product_id)
//             ->with('unit') // for is_base and id
//             ->get()
//             ->keyBy('unit_id');

//         // Recursive function to calculate conversion multiplier
//         $calculateMultiplier = function ($unitId) use (&$calculateMultiplier, $productUnits) {
//             if (!isset($productUnits[$unitId])) {
//                 return 1;
//             }

//             $productUnit = $productUnits[$unitId];

//             // If it's a base unit, multiplier is 1
//             if ($productUnit->unit && $productUnit->unit->is_base) {
//                 return 1;
//             }

//             // If it has no convert_to_unit_id, we assume it's base as well
//             if (!$productUnit->convert_to_unit_id) {
//                 return 1;
//             }

//             $parentMultiplier = $calculateMultiplier($productUnit->convert_to_unit_id);
//             return $productUnit->conversion_rate * $parentMultiplier;
//         };

//         DB::beginTransaction();

//         foreach ($productUnits as $unitId => $productUnit) {
//             $multiplier = $calculateMultiplier($unitId);

//             // Update conversion_multiplier
//             $productUnit->conversion_multiplier = $multiplier;
//             $productUnit->save();
//         }

//         DB::commit();
//     } catch (\Throwable $th) {
//         DB::rollBack();
//         throw $th;
//     }
// }

function setProductUnitConversionMultiplier($product_id)
{
    try {
        $productUnits = ProductUnit::where('product_id', $product_id)
            ->with('unit') // for is_base and id
            ->get()
            ->keyBy('unit_id');

        // Recursive function to calculate conversion multiplier
        $calculateMultiplier = function ($unitId) use (&$calculateMultiplier, $productUnits) {
            if (!isset($productUnits[$unitId])) {
                return 1;
            }

            $productUnit = $productUnits[$unitId];

            // If it's a base unit, multiplier is 1
            if ($productUnit->unit && $productUnit->unit->is_base) {
                return 1;
            }

            if($productUnit->unit && $productUnit->unit->pre_defined&& $productUnit->unit->default_conversion_factor) {
                return $productUnit->unit->default_conversion_factor;
            }
            // If it has no convert_to_unit_id, we assume it's base as well
            if (!$productUnit->convert_to_unit_id) {
                return 1;
            }

            $parentMultiplier = $calculateMultiplier($productUnit->convert_to_unit_id);
            return $productUnit->conversion_rate * $parentMultiplier;
        };

        DB::beginTransaction();

        foreach ($productUnits as $unitId => $productUnit) {
            $multiplier = $calculateMultiplier($unitId);
            $divider = $multiplier != 0 ? 1 / $multiplier : 0;

            // Update conversion_multiplier and conversion_divider
            $productUnit->conversion_multiplier = $multiplier;
            $productUnit->conversion_divider = $divider;
            $productUnit->save();
        }

        DB::commit();
    } catch (\Throwable $th) {
        DB::rollBack();
        throw $th;
    }
}


    public function destroy(int $id)
    {

        try {
            $item = Products::find($id);
            $item->delete();

            toast('Product Delete!', 'error');
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    static function get_common_field_category_ids()
    {
        try {
            $config = ConfigHelper::getStoreConfig();

            if (!$config) {
                toast('Not configured the store yet contact support', 'error');
            }

            $field = Fields::firstOrCreate(
                [
                    'name' => "General",
                ],
                [
                    'store_id' => $config["store_id"]
                ]
            );

            $category = ProductCategory::firstOrCreate(
                [
                    'category' => "General",
                    'parent_cat' => $field->id,
                ],
                [
                    'store_id' => $config["store_id"]
                ]
            );

            if ($field && $category) {
                return ["category_id" => $category->id, 'field_id' => $field->id];
            }

            return false;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // API CONTROLLER FOR PRODUCTS
    public function getProductApi($exact, $param, $storeId)
    {
        try {
            if ($exact == 1) {
                $item = Products::where(function ($query) use ($param) {
                    $query->where('barcode', $param)->orWhere('id', $param);
                })->with([
                    'uoms',
                    'categories.field',
                    'product_units' => function ($query) {
                        $query->where('is_active', true); // Filter only active product units
                    },
                    'product_units.unit.conversion_unit' // Still eager-load nested unit + conversion_unit
                ])->where('store_id', $storeId)->first();

                return response()->json($item);
            } else {
                $items = Products::where(function ($qyer) use ($param) {
                    $qyer->where('name', 'LIKE', "%$param%")
                        ->orWhere('brand', 'LIKE', "%$param%")
                        ->orWhereHas('categories', function ($query) use ($param) {
                            $query->where('category', 'LIKE', "%$param%")->filterByStore();
                        })
                        ->orWhereHas('categories.field', function ($query) use ($param) {
                            $query->where('name', 'LIKE', "%$param%")->filterByStore();
                        });
                })->where('store_id', $storeId)
                    ->with('uoms', 'categories.field', 'product_units.unit.conversion_unit')
                    ->get();

                return response()->json($items);
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }



    public function importCsv(Request $request)
    {
        try {
            // dd($request->file('file'));
            Excel::import(new ProductImport, $request->file('file'));

            return redirect()->back();
            Alert::toast('Product Imported Successfuly!', 'success');
            // dd($request->file('file'));

        } catch (\Throwable $th) {
            throw $th;
            return redirect()->back();
            Alert::alert('Error', $th->getMessage(), 'error');
        }
    }
}
