<?php

namespace App\Http\Livewire;

use App\Models\Fields;
use App\Models\ProductCategory;
use App\Models\Products;
use Livewire\Component;

class CategoryProduct extends Component
{
    public $selected_category = null;
    public $categories = [];
    public $selected_field = null;
    public $live_products;
    public  $selected_product= null;
    public $col = 4;
    public function mount()
    {
        $this->live_products = [];
        $this->change_category();
        $this->changeField();
    }

    public function changeField()
    {
        $this->categories = ProductCategory::where('parent_cat', $this->selected_field)->get();
    }
    
    public function change_category()
    {
        $this->live_products = Products::where('category', $this->selected_category)->get();    
    }

    public function render()
    {
        
        
        $fields = Fields::all();
        
        return view('livewire.category-product',compact('fields'));
    }
}
