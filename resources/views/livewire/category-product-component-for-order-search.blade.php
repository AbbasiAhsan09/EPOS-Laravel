<div class="col-lg-{{isset($col) ? $col : 4}}">
    {{-- @dd($col) --}}
 <div class="row">
    <div class="col-lg-4">
        <div class="input-group input-group-outline">
            <select name="field" wire:change="changeField()" wire:model="selected_field" 
            class="form-control" >
                <option value="">All Fields</option>
                @foreach ($fields as $field)
                        <option value="{{$field->id}}">{{$field->name}}</option>
                @endforeach
            </select>
          </div>
    </div>

    <div class="col-lg-4">
        <div class="input-group input-group-outline">
            <select name="category" wire:change="change_category()" wire:model="selected_category" 
            class="form-control" >
                <option value="">All Categories</option>
                @foreach ($categories as $category)
                        <option value="{{$category->id}}">{{$category->category}}</option>
                @endforeach
            </select>
          </div>
    </div>
    
    <div class="col-lg-4">
       
        <div class="input-group input-group-outline">
           
            <select name="product"  class="form-control" id="product_id" >
             
                <option value="">All Products</option>
                @foreach ($live_products as $product)
                    <option value="{{$product->barcode}}" {{$selected_product != null && $selected_product == $product->barcode ? 'selected' : ''  }}>{{$product->name}}</option>
                @endforeach
     
    
            </select>
        
          </div>
    </div>

</div>
</div>

