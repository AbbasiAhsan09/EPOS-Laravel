<?php

namespace App\Http\Controllers;

use App\Imports\ProductImport;
use App\Models\Inventory;
use App\Models\MOU;
use App\Models\ProductArrtributes;
use App\Models\ProductCategory;
use App\Models\Products;
use Illuminate\Http\Request;
use Excel;
use RealRashid\SweetAlert\Facades\Alert;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        session()->forget('filter');
        $uom = MOU::all();
        $categories = ProductCategory::all();
        $items = Products::with('categories','uoms')
        ->when($request->has('filter') && $request->filter != null ,  function($query) use ($request){
            $query->where('name', 'LIKE', '%'.$request->filter.'%');
            session()->put('filter', $request->filter);
        })
        ->paginate(20)->withQueryString();
        // dd($items);
        // dd($items);
        $arrt = ProductArrtributes::all();
        return view('items.index',compact('items','uom','categories','arrt'));
    }

    public function store(Request $request)
    {
        try {
            $product = new Products();
            
            $product->name = $request->product;
            $product->barcode = $request->code;
            $product->uom = $request->uom;
            $product->category = $request->category;
            $product->mrp = $request->mrp;
            $product->low_stock = $request->low_stock;
            $product->tp = $request->tp;
            $product->taxes = $request->tax;
            $product->store_id = 1;
            $product->img = $request->img;
            $product->brand = $request->brand;
            $product->description = $request->description;
            $product->opening_stock = $request->opening_stock;
            $product->save();

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
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $product =  Products::find($id);
            $oldQty = $product->opening_stock;
            $product->name = $request->product;
            $product->barcode = $request->code;
            $product->uom = $request->uom;
            $product->category = $request->category;
            $product->mrp = $request->mrp;
            $product->low_stock = $request->low_stock;
            $product->opening_stock = $request->opening_stock;
            $product->tp = $request->tp;
            $product->taxes = $request->tax;
            $product->store_id = 1;
            $product->img = $request->img;
            $product->brand = $request->brand;
            $product->description = $request->description;
            $product->save();

            if($product){
                $newQty = $product->opening_stock;
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


    // API CONTROLLER FOR PRODUCTS
    public function getProductApi($exact,$param)
    {
        try {
           if($exact == 1){
            $item = Products::where('barcode' , $param)->orWhere('id',$param)->with('uoms','categories.field')->first();
            return response()->json($item);
           }else{
            $items = Products::where(function($qyer) use($param){
                $qyer->where('name' , 'LIKE' , "%$param%")
                ->orWhere('brand', 'LIKE' , "%$param%");
            })
            ->with('uoms','categories.field')
            ->orWhereHas('categories', function($query) use($param){
                $query->where('category','LIKE', "%$param%");
            })
            ->orWhereHas('categories.field', function($query) use($param){
                $query->where('name','LIKE', "%$param%");
            })
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
            // dd($request);
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
