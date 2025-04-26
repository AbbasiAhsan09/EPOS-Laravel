<?php

namespace App\Http\Controllers;

use App\Http\Trait\InventoryTrait;
use App\Models\Inventory;
use App\Models\ProductUnit;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    use InventoryTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function show(Inventory $inventory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function edit(Inventory $inventory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Inventory $inventory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Inventory  $inventory
     * @return \Illuminate\Http\Response
     */
    public function destroy(Inventory $inventory)
    {
        //
    }

    public function checkInventory($item_id, $is_base_unit)
    {
        try {
          return  response()->json($this->checkAvaialableInventory($item_id , $is_base_unit));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }


    public function check_inventory_by_item(Request $request) {
       try{

        $required_qty = $request->query('qty');
        $unit_id = $request->query('unit_id');
        $item_id = $request->query('product');
        $low_stock = false;
        $avl_stock = 0;
        if($request->query('product')){
            $result = InventoryReportController::inventory_report($request);
        
            $result = $result->first();

            if($unit_id){
                $product_unit = ProductUnit::where("unit_id", $unit_id)->where("product_id", $item_id)->first();
                // dump($product_unit, $unit_id, $item_id);
                if($product_unit){
                    $required_qty = $product_unit->conversion_multiplier * $required_qty;
                    $avl_stock = $result->avl_qty / $product_unit->conversion_multiplier;
                }
            }else{
                $avl_stock = $result->avl_qty;
                $low_stock = $result->avl_qty < $required_qty ? true : false;
            }

            if($result){
                if($required_qty > $result->avl_qty){
                    $low_stock = true;
                }
            }
            $result = json_decode(json_encode($result), true);
            // dump($result);
            $result['low_stock'] = $low_stock;
            $result['avl_stock'] = number_format((float)$avl_stock, 2, '.', '');;

            return response()->json($result);
        }

        return response()->json(null);

       }catch (\Throwable $th) {
            throw $th;
        }
    }
}
