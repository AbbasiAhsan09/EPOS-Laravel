<?php

namespace App\Http\Livewire;

use App\Models\ProductCategory;
use App\Models\Products;
use Livewire\Component;

class CategoryProduct extends Component
{
    public $selected_category = null;
    public $live_products;
    public  $selected_product= null;

    public function mount()
    {
        $this->live_products = [];
        $this->change_category();
    }
    
    public function change_category()
    {
        $this->live_products = Products::where('category', $this->selected_category)->get();    
    }

    public function render()
    {
        
        $categories = ProductCategory::all();
        
        return view('livewire.category-product',compact('categories'));
    }
}
