<?php

namespace App\Imports;

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
       
        if(array_key_exists('category' , $row) && array_key_exists('product' , $row) && array_key_exists('code' , $row) && array_key_exists('mrp', $row) && array_key_exists('tp', $row) && array_key_exists('taxes' , $row) && array_key_exists('discount' , $row) && array_key_exists('description' , $row)){
            $checkCategory = ProductCategory::where('category',$row["category"])->first();
            if(!$checkCategory){
                $category = new ProductCategory();
                $category->category = $row["category"];
                // $category->store_id  = 1;
                $category->save();
            }
    
            $item = new Products();
            $item->name = $row["product"];
            $item->category = ($checkCategory ? $checkCategory->id : $category->id);
            $item->barcode = $row["code"];
            $item->mrp = $row["mrp"];
            $item->tp = $row["tp"];
            $item->discount = $row["discount"];
            $item->taxes = $row["taxes"];
            $item->store_id =  1;
            $item->description = $row["description"];
            $item->save();
            return $item;
        
        
        }
        

        
    }
}
