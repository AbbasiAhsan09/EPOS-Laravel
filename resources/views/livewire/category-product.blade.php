<div class="col-lg-4">
 <div class="row">
    <div class="col-lg-6">
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
    
    <div class="col-lg-6">
       
        <div class="input-group input-group-outline">
           
            <select name="product"  class="form-control" >
             
                <option value="">All Products</option>
                @foreach ($live_products as $product)
                    <option value="{{$product->id}}" {{$selected_product != null && $selected_product == $product->id ? 'selected' : ''  }}>{{$product->name}}</option>
                @endforeach
     
    
            </select>
        
          </div>
    </div>
 </div>
</div>