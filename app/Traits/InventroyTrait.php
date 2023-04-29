<?php
namespace App\Traits;
use App\Models\Inventory;

trait InventoryTrait
{



    //Check Inventory Available 
    public function checkAvaialableInventory($item)
    {
        $product = Inventory::find(['item_id' => $item, 'is_opnening_stock' => 0]);
        if($product){
            return $product->stock_qty;
        }
        return 0;

    }

    // Manage Inventory on Order Reflection
    public function subtractInventoryWithOrder($item, $qty,  )
    {
      
    }



}