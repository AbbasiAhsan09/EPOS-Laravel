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
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        session()->forget('filter');
        $uom = MOU::filterByStore()->get();
        $categories = ProductCategory::all();
        $items = Products::with('categories','uoms')
        ->when($request->has('filter') && $request->filter != null ,  function($query) use ($request){
            $query->where('name', 'LIKE', '%'.$request->filter.'%');
            session()->put('filter', $request->filter);
        })
        ->byUser()
        ->paginate(20)->withQueryString();
        // dd($items);
        // dd($items);
        $arrt = ProductArrtributes::all();
        $dynamicFields = AppForms::where("name",'product')
        ->with("fields")->whereHas("fields", function($query){
            $query->filterByStore();
        })->first();
        // dd($dynamicFields);
        return view('items.index',compact('items','dynamicFields','uom','categories','arrt'));
    }

    public function store(Request $request)
    {
        
        try {
            // dd($request->all());
            $validate = $request->validate([
                'code' => 'required|unique:products,barcode,id,store_id',
            ]);
            if($validate){
                $product = new Products();
                $product->name = $request->product;
                $product->barcode = $request->code;
                $product->uom = $request->uom;
                $product->category = $request->has("category") && !empty($request->category) ? $request->category : $this->get_common_field_category_ids()["category_id"];
                $product->mrp = (int)$request->mrp ?? 0;
                $product->low_stock = (int)$request->low_stock ?? 0;
                $product->tp = (int)$request->tp ?? 0;
                $product->taxes = (int)$request->tax ?? 0;
                $product->store_id = Auth::user()->store_id;
                $product->img = $request->img;
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
                    $image->move(public_path($public_path),$fileName);
                    $product->img = $fileName; // Assign the file name to the product image attribute
                }

                $product->save();

                 // 
                if(isset($request->dynamicFields) && count($request->dynamicFields)){
                    foreach ($request->dynamicFields as $key => $value) {
                        if(!empty($value)){
                        $form = AppForms::where("name",'product')->first();
                        foreach ($value as $key => $field_value) {
                        $form_field = AppFormFields::where('form_id',$form->id)->where('name',$key)->filterByStore()->first();
                            if($form_field){
                                AppFormFieldsData::create(['form_id' => $form->id, 'field_id' => $form_field->id, 
                                'value' => $field_value, 'related_to' => $product->id,
                                'store_id' => Auth::user()->store_id ?? null]);
                            }
                        }
                        }
                    }
                }
             //

    
                if($product){
                    $uom = MOU::find($request->uom);
    
                    $inventory = Inventory::firstOrCreate([
                        'is_opnening_stock' => 0,
                        'item_id' => $product->id
                    ]);
    
                    $inventory->update([
                        'stock_qty' => ($inventory->stock_qty + ($product->opening_stock * (isset($uom->base_unit_value) ? $uom->base_unit_value : 1)))
                    ]);
                }
    
                toast('Product Added!','success');
                return redirect()->back();
            }else{
                toast('Product Code Cannot Be Duplicated!','error');
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
            $product->uom = $request->uom;
            $product->category = $request->has("category") && !empty($request->category) ? $request->category : $this->get_common_field_category_ids()["category_id"];
            $product->mrp = (int)$request->mrp ?? 0;
            $product->low_stock = (int)$request->low_stock ?? 0;
            $product->opening_stock = !empty($request->opening_stock) ? $request->opening_stock : 0;
            $product->tp = (int)$request->tp ?? 0;
            $product->taxes = (int)$request->tax ?? 0;
            $product->opening_stock_unit_cost = (int)$request->opening_stock_unit_cost ?? 0;
            $product->check_inv = isset($request->check_inv) && $request->check_inv ? true : false;
            // $product->store_id = 1;
            // Product IMage Logic
            if ($request->hasFile('image')) {
                $image = $request->file('image');
            
                // Generating a unique name for the file
                $fileName = time() . '_' . uniqid() . '.' . $image->getClientOriginalExtension();
                // dd($fileName);
                $public_path = "images/" . Auth::user()->store_id . "/products";
                $image->move(public_path($public_path),$fileName);
                $product->img = $fileName; // Assign the file name to the product image attribute
            }
           
            $product->brand = $request->brand;
            $product->description = $request->description;
            $product->save();

            if($product){
                $newQty = ((int)($product->opening_stock)) ? (int)($product->opening_stock) : 0;
                $diffQty = $newQty - $oldQty;
                $uom = MOU::find($request->uom);
                $inventory = Inventory::firstOrCreate([
                    'is_opnening_stock' => 0,
                    'item_id' => $product->id
                ]);

                $inventory->update([
                    'stock_qty' => ($inventory->stock_qty + ($diffQty * (isset($uom->base_unit_value) ? $uom->base_unit_value : 1)))
                ]);
            }

            

            toast('Product Updated!','info');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function destroy(int $id)
    {
       
        try {
            $item = Products::find($id);
            $item->delete();
            
            toast('Product Delete!','error');
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    static function get_common_field_category_ids(){
        try {
            $config = ConfigHelper::getStoreConfig();

            if(!$config){
                toast('Not configured the store yet contact support','error');
            }

            $field = Fields::firstOrCreate([
                'name' => "General",
            ],
            [
                'store_id' => $config["store_id"] 
            ]
            );

            $category = ProductCategory::firstOrCreate([
                'category' => "General",
                'parent_cat' => $field->id,
            ],
            [
                'store_id' => $config["store_id"] 
            ]
            );

            if($field && $category){
                return ["category_id" => $category->id, 'field_id' => $field->id];
            }

            return false;


        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // API CONTROLLER FOR PRODUCTS
    public function getProductApi($exact,$param,$storeId)
    {
        try {
           if($exact == 1){
            $item = Products::where(function($query) use ($param){
                $query->where('barcode' , $param)->orWhere('id',$param);
            })->with('uoms','categories.field')->where('store_id',$storeId)->first();
            
            return response()->json($item);
           }else{
            $items = Products::where(function($qyer) use($param){
                $qyer->where('name' , 'LIKE' , "%$param%")  
                ->orWhere('brand', 'LIKE' , "%$param%")
                ->orWhereHas('categories', function($query) use($param){
                    $query->where('category','LIKE', "%$param%")->filterByStore();
                })
                ->orWhereHas('categories.field', function($query) use($param){
                    $query->where('name','LIKE', "%$param%")->filterByStore();
                });
            })->where('store_id',$storeId)
            ->with('uoms','categories.field')
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
            Alert::toast('Product Imported Successfuly!','success');
        // dd($request->file('file'));

        } catch (\Throwable $th) {
            throw $th;
            return redirect()->back();
            Alert::alert('Error', $th->getMessage(),'error');
        }
    }
}
