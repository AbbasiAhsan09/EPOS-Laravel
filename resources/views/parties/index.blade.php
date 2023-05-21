@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
 
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Parties</h1>
    </div>
    <div class="col">
        <div class="btn-grp">
         
            <div class="row .row-customized">
                <div class="col-lg-8">
                    {{-- <div class="input-group input-group-outline">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)">
                      </div> --}}
                      <div class="input-group input-group-outline">
                      <select  id="filter-party" class="form-control">
                        <option value="">All Parties</option>
                        
                        @foreach ($party_groups as $party_group)
                        <option value="{{$party_group->id}}" {{ $group_id ? ($group_id == $party_group->id ? 'selected' : '') : ''}}>{{$party_group->group_name}}</option>
                        @endforeach
                      </select>
                      </div>
                </div>
                <div class="col-lg-4">
                    <button class="btn btn-outline-secondary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>
                    <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">New Party</button>

                </div>
            </div>
        </div>
    </div>
</div>
<table class="table table-responsive-sm table-sm table-striped">
    <thead>
        <th>ID</th>
        <th>Group</th>
        <th>Party Name</th>
        <th>Phone</th>
        <th>Email</th>
        <th>Status</th>
        <th>Actions</th>
    </thead>
    <tbody>
       @foreach ($parties as $item)
       <tr>
        <td>{{$item->id}}</td>
        <td>{{$item->groups->group_name}}</td>
        <td>{{$item->party_name}}</td>
        <td>{{$item->phone}}</td>
        <td>{{$item->email}}</td>
        <td>
            @if ($item->status == 1)
                <div class="badge badge-sm bg-gradient-success">Active</div>
            @elseif($item->status == 2)
                <div class="badge badge-sm bg-gradient-success">Active</div>
            @endif
        </td>
        @if (Auth::user()->role_id ==1 )
            <td>
                <div class="s-btn-grp">
                  <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" data-bs-toggle="modal" data-bs-target="#edit{{$item->id}}">
                      <i class="fa fa-edit"></i>
                  </button>
                  <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}">
                      <i class="fa fa-trash"></i>
                  </button>
               
              </div>
              </td>
            @endif
    </tr>

      <!-- Modal -->
  <div class="modal fade" id="edit{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Create New Party</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
       <div class="modal-body">
            <form action="{{route('edit.party',$item->id)}}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Party  Name *</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="party_name" value="{{$item->party_name}}" required>
                    </div>
                    </div>  
                    <div class="col-lg-6">
                        <label for="">Party  Group *</label>
                    <div class="input-group input-group-outline">
                        <select name="group_id" class="form-control" id="">
                            <option value="">Select Party Group</option>
                            @foreach ($party_groups as $pt_gp)
                                <option value="{{$pt_gp->id}}" {{$pt_gp->id == $item->group_id ? 'selected' : ''}}>{{$pt_gp->group_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div> 
                    <div class="col-lg-6">
                        <label for="">Party  Email</label>
                    <div class="input-group input-group-outline">
                      <input type="email" class="form-control" name="email" value="{{$item->email}}" required>
                    </div>
                    </div>  

                    <div class="col-lg-6">
                        <label for="">Party  Phone *</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="phone" value="{{$item->phone}}" required>
                    </div>
                    </div>  


                    <div class="col-lg-4">
                        <label for="">Party  Country *</label>
                    <div class="input-group input-group-outline">
                        <select name="country" class="form-control" id="">
                            <option value="">Select Country</option>
                        </select>
                    </div>
                    </div> 
                    
                    <div class="col-lg-4">
                        <label for="">Party  City *</label>
                    <div class="input-group input-group-outline">
                        <select name="country" class="form-control" id="">
                            <option value="">Select City</option>
                        </select>
                    </div>
                    </div> 

                    <div class="col-lg-4">
                        <label for="">Party  Website </label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" value="{{$item->website}}" name="website" required placeholder="example.com">
                    </div>
                    </div> 

                    <div class="col-lg-12">
                        <label for="">Party  Address </label>
                    <div class="input-group input-group-outline">
                      <textarea name="location"  class="form-control" id="" cols="30" rows="3">{{$item->location}}</textarea>
                    </div>
                    </div> 

                </div>
           
        </div> 
        <div class="modal-footer">
          
          <button type="submit" class="btn btn-primary">Save</button>
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
  <div class="modal fade" id="newStoreModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Create New Party</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
       <div class="modal-body">
            <form action="{{route('add.party')}}" method="POST">
                @csrf
                @method('post')
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Party  Name *</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="party_name" required>
                    </div>
                    </div>  
                    <div class="col-lg-6">
                        <label for="">Party  Group *</label>
                    <div class="input-group input-group-outline">
                        <select name="group_id" class="form-control" id="">
                            <option value="">Select Party Group</option>
                            @foreach ($party_groups as $pt_grp)
                                <option value="{{$pt_grp->id}}">{{$pt_grp->group_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div> 
                    <div class="col-lg-6">
                        <label for="">Party  Email</label>
                    <div class="input-group input-group-outline">
                      <input type="email" class="form-control" name="email" required>
                    </div>
                    </div>  

                    <div class="col-lg-6">
                        <label for="">Party  Phone *</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="phone" required>
                    </div>
                    </div>  


                    <div class="col-lg-4">
                        <label for="">Party  Country *</label>
                    <div class="input-group input-group-outline">
                        <select name="country" class="form-control" id="">
                            <option value="">Select Country</option>
                        </select>
                    </div>
                    </div> 
                    
                    <div class="col-lg-4">
                        <label for="">Party  City *</label>
                    <div class="input-group input-group-outline">
                        <select name="country" class="form-control" id="">
                            <option value="">Select City</option>
                        </select>
                    </div>
                    </div> 

                    <div class="col-lg-4">
                        <label for="">Party  Website </label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="website" required placeholder="example.com">
                    </div>
                    </div> 

                    <div class="col-lg-12">
                        <label for="">Party  Address </label>
                    <div class="input-group input-group-outline">
                      <textarea name="location"  class="form-control" id="" cols="30" rows="3"></textarea>
                    </div>
                    </div> 

                </div>
           
        </div> 
        <div class="modal-footer">
          
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
      </div>
    </div>
  </div>
  {{-- Modal --}}

  <!-- Filter Modal -->
  <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Filter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
       <div class="modal-body">
            <form action="{{route('add.partyGroup')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Party Group</label>
                    <div class="input-group input-group-outline">
                        <select name="party_group" class="form-control" id="" required>
                            <option value="">Select Party Group *</option>
                            @foreach ($party_groups as $group)
                                <option value="{{$group->id}}">{{$group->group_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    </div> 
                    
                    <div class="col-lg-6">
                    <label for="">Party Name or ID</label>
                    <div class="input-group input-group-outline">
                        <input type="text" name="party_name" class="form-control" >
                    </div>

                    </div> 

                    <div class="col-lg-6">
                        <label for="">Party Phone</label>
                        <div class="input-group input-group-outline">
                            <input type="text" name="party_name" class="form-control" >
                        </div>
    
                        </div> 

                        <div class="col-lg-6">
                            <label for="">Party Email</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="party_name" class="form-control">
                            </div>
        
                        </div> 

                        <div class="col-lg-6">
                            <label for="">From</label>
                            <div class="input-group input-group-outline">
                                <input type="date" name="party_name" class="form-control">
                            </div>
        
                        </div> 

                        <div class="col-lg-6">
                            <label for="">To</label>
                            <div class="input-group input-group-outline">
                                <input type="date" name="party_name" class="form-control">
                            </div>
        
                        </div> 
                </div>
           
        </div> 
        <div class="modal-footer">
          
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
      </div>
    </div>
  </div>
  
  <script>
    $(document).ready(function(){
      $('#filter-party').change(function(){
        if($(this).val()){
            window.location.replace('/parties/'+$(this).val()) ;
        }else{
            window.location.replace('/parties') ;

        }
      })
    });
  </script>
  {{-- Filters Modal --}}
@endsection