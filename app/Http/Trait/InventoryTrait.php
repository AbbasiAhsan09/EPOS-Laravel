<?php
namespace App\Http\Trait;

use App\Models\Configuration;
use App\Models\Inventory;
use App\Models\Products;

trait InventoryTrait
{


  
    public $allowInventoryCheck = true;
    public $allowLowInventory = false;


    
    // public function _construct(){
    //     $config = Configuration::latest()->first();
    //     $this->allowInventoryCheck = $config->allow_inventory_check;
    //     $this->allowLowInventory = $config->allow_low_inventory;
    // }
        //Check Inventory Available 
        public function checkAvaialableInventory($item, $is_base_unit = 0)
        {
            
            $product = Inventory::where('item_id', $item)->where( 'is_opnening_stock', 0)->first();
            if($product){
                if(!$is_base_unit){
                    $product_details = Products::where('id', $item)->with('uoms')->first();
                    $qty = $product->stock_qty / (isset($product_details->uoms->base_unit_value) ? $product_details->uoms->base_unit_value : 1 );
                    return $qty;
                }else{
                    return $product->stock_qty;
                }
            }
            return false;
    
        }
    
        
        // Manage Inventory on Order Reflection
        public function subtractInventoryWithOrder($item, $qty, $is_base_unit)
        {
            // dd(12);
          $inventory = Inventory::where('item_id' , $item )
          ->where('is_opnening_stock' , 0)->first();
            if($inventory){
                $product = Products::where('id',$item)->with('uoms')->first();
                $qty = ($is_base_unit ? ($qty) : ((isset($product->uoms->base_unit_value ) ? $product->uoms->base_unit_value : 1 ) * $qty));
                $inventory->stock_qty = $inventory->stock_qty - $qty;
                $inventory->save();
                return true;
            }
            return false;
        }

        // Mange INventory on Update order
        public function updateInventoryOnUpdateOrder($item , $oldQty, $reqQty, $is_base_unit_req = true, $is_base_unit_old = true)
        {
            $product = Products::where('id',$item)->with('uoms')->first();
            $reqQty = ($is_base_unit_req ? $reqQty : ($reqQty * (isset($product->uoms->base_unit_value) ? $product->uoms->base_unit_value : 1 )));
            $oldQty = (isset($oldQty) ?  ($is_base_unit_old ? $oldQty : ($oldQty * (isset($product->uoms->base_unit_value) ? $product->uoms->base_unit_value : 1 ))) : 0);
                $diffQty = $reqQty - $oldQty;
                $inventory = Inventory::where('item_id' , $item)
                ->where('is_opnening_stock' , 0)->first();
                if(!$inventory){
                    $inventory = Inventory::FirstOrCreate(['is_opnening_stock' => 0 ,'item_id' => $item]);
                }
                $inventory->stock_qty = $inventory->stock_qty - $diffQty;
                if($inventory->save()){
                    return true;
                }
            return false;

        }

        // Manage Inventory of Deleted Items in order
        public function deletedItemsOnOrderUpdate($details)
        {
            if(isset($details) && count($details)){
                foreach ($details as $key => $detail) {
                    $inventory = Inventory::where('item_id' , $detail->item_id)
                    ->where('is_opnening_stock' , 0)->first();
                    $product = Products::where('id' , $detail->item_id)->with('uoms')->first();
                    $qty = ($detail->is_base_unit ? $detail->qty : $detail->qty * (isset($product->uoms->base_unit_value) ? $product->uoms->base_unit_value : 1 ));
                    $inventory->stock_qty = $inventory->stock_qty + $qty;
                    $inventory->save();
                }
            }
        } 



}