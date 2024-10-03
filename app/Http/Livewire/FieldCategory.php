<?php

namespace App\Http\Livewire;

use App\Models\Fields;
use App\Models\ProductCategory;
use Livewire\Component;

class FieldCategory extends Component
{
    public $selectedField = null, $live_categories = [], $selectedCategory = null, $required = true;

    public function changeField()
    {
        $this->live_categories = ProductCategory::where('parent_cat', $this->selectedField)->get();
        
    }

    
    public function render()
    {
        $fields = Fields::byUser()->get();
        if ($this->selectedCategory) {
            $this->selectedField = Fields::find(ProductCategory::find($this->selectedCategory)->parent_cat)->id;
            $this->changeField();
        }
        return view('livewire.field-category',compact('fields'));
    }
}
