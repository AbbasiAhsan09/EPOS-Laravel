@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">

    <div class="row row-customized">
        <div class="col">
            <h1 class="page-title">Users</h1>
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
                    <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">New User</button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            @if (Auth::user()->userroles->role_name == 'SuperAdmin')
            <th>Role</th>
            @endif
            <th>Store</th>
            <th>Status</th>
          
            <th>Actions</th>
        </thead>
        <tbody>
           @foreach ($users as $key => $item)
           <tr>
         
            <td>{{$item->id}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->email}}</td>
            <td>{{$item->userroles->role_name ?? ""}}</td>
            @if (Auth::user()->userroles->role_name == 'SuperAdmin')
            <td>{{($item->store ? $item->store->store_name : 'TradeWise')}}</td>
            @endif
            <td>
                @if ($item->isActive == 1)
                  <div class="badge badge-sm bg-gradient-success">Active</div>
              @else
              <div class="badge badge-sm bg-gradient-danger">Blocked</div>
                  
              @endif
            </td>
            
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
        </tr>
{{-- Delete Modal --}}
        <!-- Modal -->
        <div class="modal fade" id="deleteModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
            <div class="modal-dialog">
              <div class="modal-content ">
                <div class="modal-header">
                  <h5 class="modal-title" id="newStoreModalLabel">Delete User</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete {{$item->name}}?</p>
                </div>
                <div class="modal-footer">
                    <form action="{{route('delete.user',$item->id)}}" method="POST">
                    @csrf
                @method('delete')
                        <button type="button" class="btn">No</button>
                        <button type="submit" class="btn btn-primary">Yes</button>
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
          <h5 class="modal-title" id="newStoreModalLabel">Update User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
        <form action="{{route('edit.user',$item->id)}}" method="POST">
            @csrf
            @method('put')
            <div class="row">
                <div class="col-lg-6">
                    <label for="">Name *</label>
                    <div class="input-group input-group-outline">
                        <input type="text" class="form-control" name="name" required value="{{$item->name}}" >
                    </div>
                </div>
                <div class="col-lg-6">
                    <label for="">Email *</label>
                    <div class="input-group input-group-outline">
                        <input type="text" class="form-control" name="email" required value="{{$item->email}}" >
                    </div>
                </div>
                <div class="col-lg-6">
                    <label for="">Phone *</label>
                    <div class="input-group input-group-outline">
                        <input type="text" class="form-control" name="phone" required value="{{$item->phone}}" >
                    </div>
                </div>
                
               
                <div class="col-lg-6">
                    <label for="">Role *</label>
                    <div class="input-group input-group-outline">
                        <select name="role_id" id="" class="form-control">
                            <option value="">Select Role</option>
                            @foreach ($user_roles as $us_role)
                               @if ($us_role->id !== 1)
                               <option value="{{$us_role->id}}" {{$us_role->id == $item->role_id ? 'selected' : ''}}>{{$us_role->role_name}}</option>
                               @endif
                            @endforeach
                        </select>
                    </div>
                </div>

                @if (Auth::user()->userroles->role_name == 'SuperAdmin')
                <div class="col-lg-6">
                    <label for="">Store *</label>
                    <div class="input-group input-group-outline">
                        <select name="business_id" id="" class="form-control">
                            <option value="">Select Store</option>
                            @foreach ($stores as $store)
                                <option value="{{$store->id}}">{{$store->store_name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                

                <div class="col-lg-6">
                    <label for="">Status *</label>
                    <div class="input-group input-group-outline">
                        <select name="status" id="" class="form-control">
                            <option value="0" {{$item->isActive == false ? 'selected' : '' }}>Blocked</option>
                            <option value="1" {{$item->isActive == true ? 'selected' : '' }}>Active</option>
                        </select>
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
            <form action="{{route('add.user')}}" method="POST">
                @csrf
                @method('post') 
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Name *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="name" required  >
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Email *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="email" required >
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Phone *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="phone" required  >
                        </div>
                    </div>
    
                    <div class="col-lg-6">
                        <label for="">Password *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="password" required  >
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Role *</label>
                        <div class="input-group input-group-outline">
                            <select name="role_id" id="" class="form-control" >
                                <option value="">Select Role</option>
                                @foreach ($user_roles as $us_role)
                                    <option value="{{$us_role->id}}" >{{$us_role->role_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if (Auth::user()->userroles->role_name == 'SuperAdmin')
                    <div class="col-lg-6">
                        <label for="">Store *</label>
                        <div class="input-group input-group-outline">
                            <select name="business_id" id="" class="form-control">
                                <option value="">Select Store</option>
                                @foreach ($stores as $store)
                                    <option value="{{$store->id}}">{{$store->store_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @endif
    
               
                </div>
                
           
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
      </div>
    </div>
  </div>
  {{-- Modal --}}
@endsection