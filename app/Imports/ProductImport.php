<?php

namespace App\Imports;

use App\Models\Fields;
use App\Models\Inventory;
use App\Models\MOU;
use App\Models\ProductCategory;
use App\Models\Products;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use RealRashid\SweetAlert\Facades\Alert;

class ProductImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {

        if (array_key_exists('category', $row) && array_key_exists('product', $row) && array_key_exists('code', $row) && array_key_exists('mrp', $row) && array_key_exists('tp', $row) && array_key_exists('taxes', $row) && array_key_exists('discount', $row) && array_key_exists('description', $row)) {


            $field = Fields::firstOrCreate(['name' => $row["field"]]);
            $category = ProductCategory::firstOrCreate(['category' => $row["category"], 'parent_cat' => $field->id]);

            if(!empty(trim($row["units"])) && (!empty($row["base_unit_value"]) && ($row["base_unit_value"]) > 1)){
               $uom = MOU::firstOrCreate(['uom' => 'PKT', 'base_unit' => $row["units"], 'base_unit_value' => $row["base_unit_value"]]);
            }

            $item = new Products();
            $item->name = $row["product"];
            $item->category = $category->id;
            $item->barcode = $row["code"];
            $item->mrp = $row["mrp"];
            $item->brand = !empty($row["brand"]) ? $row["brand"] : ' ';
            $item->tp = $row["tp"];
            $item->discount = $row["discount"];
            $item->taxes = $row["taxes"];
            $item->low_stock = !empty($row["alert_stock"]) ? $row["alert_stock"] : 1;
            $item->store_id =  1;
            $item->uom =  isset($uom) ? $uom->id : 0;
            $item->description = $row["description"];
            $item->opening_stock = $row["in_hand"];
            $item->save();

           

            if(!empty($row["in_hand"]) && $row["in_hand"] > 0){
            $qty = ($item->uom ? ($row["in_hand"] * $uom->base_unit_value) : $row["in_hand"]);
            Inventory::updateOrCreate(['item_id' => $item->id,'is_opnening_stock' => 0],
            ['stock_qty' => $qty]);

            }
            


            return $item;
        }
    }
}
