@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container">

    <div class="row row-customized">
        <div class="col">
            <h1 class="page-title">Products</h1>
        </div>
        <div class="col">
            <div class="btn-grp">
             
                <div class="row .row-customized">
                    <div class="col-lg-8">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)">
                          </div>
                      
                    </div>
                    <div class="col-lg-4">
                    <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">New Product</button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>ID</th>
            <th>Brand</th>
            <th>Category</th>
            <th>Product</th>
            <th>Code</th>
            <th>UOM</th>
            <th>Added On</th>
           
            @if (Auth::user()->role_id ==1)
            <th>Actions</th>
            @endif
        </thead>
        <tbody>
           @foreach ($items as $key => $item)
           <tr>
            <td>{{$key+1}}</td>
            <td>{{$item->brand}}</td>
            <td>{{$item->categories->category}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->barcode}}</td>
            <td>{{$item->uom  ? $item->uoms->uom  : 'Default' }}</td>
            <td>{{date('d, M Y | h:m A',strtotime($item->created_at))}}</td>
       
            
            @if (Auth::user()->role_id ==1)
            <td>
                <div class="s-btn-grp">
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#deleteModal{{$item->id}}">
                        <i class="fa fa-trash"></i>
                    </button>
                 
                </div>
            </td>
            @endif
        </tr>
{{-- Delete Modal --}}
        <!-- Modal -->
        <div class="modal fade" id="deleteModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
              <div class="modal-content ">
                <div class="modal-header">
                  <h5 class="modal-title" id="newStoreModalLabel">Delete Product</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete {{$item->name}}?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{route('delete.product',$item->id)}}" method="POST">
                        @csrf
                        @method('delete')
                        <button class=" btn" type="button">No</button>
                        <button class="btn btn-primary" type="submit">Yes</button>
                    </form>
                </div>
              </div>
            </div>
        </div>
{{-- Delete MOdal --}}
          <!-- Modal -->
  <div class="modal fade" id="newStoreModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Update Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('update.product',$item->id)}}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Name *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="product" required value="{{$item->name}}">
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Category *</label>
                        <div class="input-group input-group-outline">
                            <select name="category" id="" class="form-control">
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{$cat->id}}" {{$cat->id == $item->category ? 'selected' : ''}}>{{$cat->category}}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                   
                    <div class="col-lg-3">
                        <label for="">Code *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="code" required value="{{$item->barcode}}">
                        </div>
                    </div>
                    
                    <div class="col-lg-3">
                        <label for="">Brand *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="brand" required value="{{$item->brand}}">
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="">Arrtribute *</label>
                        <div class="input-group input-group-outline">

                        <select name="arrt" id="" class="form-control" disabled >
                            <option value="">None</option>
                            @foreach ($arrt as $art)
                                <option value="{{$art->id}}">{{$art->arrtribute}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">UOM *</label>
                        <div class="input-group input-group-outline">
                            
                        <select name="uom" id="" class="form-control" {{Auth::user()->role_id != 1 ? 'disabled' : ''}}>
                            <option value="0" {{$item->uom == 0 ? 'selected' : ''}}>Default</option>
                      
                            @foreach ($uom as $unit_of_m)
                            <option value="{{$unit_of_m->id}}" {{$unit_of_m->id == $item->uom ? 'selected' : ''}}>{{$unit_of_m->uom}}</option>
                             @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label for="">TAX % *</label>
                        <div class="input-group input-group-outline">
                            <input type="number" step="0.01" class="form-control" name="tax" required value="{{$item->taxes}}" min="0">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label for="">MRP *</label>
                        <div class="input-group input-group-outline">
                            <input type="number" step="0.01" class="form-control" name="mrp" value="{{$item->mrp}}" required  min="0">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label for="">Trade Price *</label>
                        <div class="input-group input-group-outline">
                            <input type="number" step="0.01" class="form-control" name="tp" value="{{$item->tp}}" required  min="0">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Low Stock Alert </label>
                        <div class="input-group input-group-outline">
                        <input type="number" class="form-control" name="low_stock" required value="{{$item->low_stock}}"  min="0" onfocus="focused(this)" onfocusout="defocused(this)">
                          </div>
                      
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label">Description </label>
                        <div class="input-group input-group-outline">
                            <textarea name="description" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)" rows="3">{{$item->description}}</textarea>
                            
                          </div>
                    </div>
                </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Update</button>
        </div>
    </form>
      </div>
    </div>
  </div>
  {{-- Modal --}}
           @endforeach
        </tbody>
    </table>
</div>
</div>

  
  <!-- Modal -->
  <div class="modal fade" id="newStoreModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Add New Product</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('add.product')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Name *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="product" required >
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Category *</label>
                        <div class="input-group input-group-outline">
                            <select name="category" id="" class="form-control">
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{$cat->id}}">{{$cat->category}}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                   
                    <div class="col-lg-3">
                        <label for="">Code *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="code" required>
                        </div>
                    </div>
                    
                    <div class="col-lg-3">
                        <label for="">Brand *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="brand" required>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <label for="">Arrtribute *</label>
                        <div class="input-group input-group-outline">

                        <select name="arrt" id="" class="form-control" disabled>
                            <option value="">None</option>
                            @foreach ($arrt as $arrt)
                                <option value="{{$arrt->id}}">{{$arrt->arrtribute}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">UOM *</label>
                        <div class="input-group input-group-outline">

                        <select name="uom" id="" class="form-control">
                            <option value="0">Default</option>
                            @foreach ($uom as $uom)
                                <option value="{{$uom->id}}">{{$uom->uom}}</option>
                            @endforeach
                        </select>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">TAX % *</label>
                        <div class="input-group input-group-outline">
                            <input type="number" step="0.01" class="form-control" name="tax" required value="17" min="0">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">MRP *</label>
                        <div class="input-group input-group-outline">
                            <input type="number" step="0.01" class="form-control" name="mrp" value="0" required  min="0">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Trade Price *</label>
                        <div class="input-group input-group-outline">
                            <input type="number" step="0.01" class="form-control" name="tp" value="0" required  min="0">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Low Stock Alert </label>
                        <div class="input-group input-group-outline">
                        <input type="number" class="form-control" name="low_stock" required value="20"  min="0" onfocus="focused(this)" onfocusout="defocused(this)">
                          </div>
                      
                    </div>
                    <div class="col-lg-3">
                        <label class="form-label">Opening Inventory </label>
                        <div class="input-group input-group-outline">
                        <input type="number" class="form-control" name="opening_stock" required value="0"  min="0" onfocus="focused(this)" onfocusout="defocused(this)">
                          </div>
                      
                    </div>
                    <div class="col-lg-12">
                        <label class="form-label">Description </label>
                        <div class="input-group input-group-outline">
                            <textarea name="description" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)" rows="3"></textarea>
                            
                          </div>
                    </div>
                </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn brn-primary">Save</button>
        </div>
    </form>
      </div>
    </div>
  </div>
  {{-- Modal --}}
@endsection