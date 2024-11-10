@extends('layouts.app')
@section('content')
@include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/SQvssnygJS8'])

<div class="page-wrapper">
<div class="container-fluid">
 
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Parties</h1>
    </div>
    <div class="col">
        <div class="btn-grp">
         
            <div class="row .row-customized">
                <div class="col-lg-6">
                    {{-- <div class="input-group input-group-outline">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)">
                      </div> --}}
                      <div class="input-group input-group-outline">
                      <select  id="filter-party" class="form-control" readonly disabled>
                        <option value="">All Parties</option>
                        
                        @foreach ($party_groups as $party_group)
                        <option value="{{$party_group->id}}" {{ $group_id ? ($group_id == $party_group->id ? 'selected' : '') : ''}}>{{$party_group->group_name}}</option>
                        @endforeach
                      </select>
                      </div>
                </div>
                <div class="col-lg-6">
                  <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">New Party</button>
                  <button class="btn btn-outline-secondary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#importModal">Import</button>
                    <button class="btn btn-outline-secondary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#filterModal">Filter</button>

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
        @if (Auth::user()->userroles->role_name == "Admin")
        <th>Actions</th>            
        @endif
    </thead>
    <tbody>
       @foreach ($parties as $item)
       {{-- @dd($item) --}}
       <tr>
        <td>{{$item->id}}</td>
        <td>
          {{$item->groups->group_name}}
        
          </td>
        <td>
          <a href="{{url((str_contains(strtolower($item->groups->group_name),'vendor') ? '/vendor-ledger?vendor_id='.$item->id.'' :  '/customer-ledger?customer_id='.$item->id.'' ))}}" class="text-primary">
            {{$item->party_name}}
          </a>
        </td>
        <td>{{$item->phone}}</td>
        <td>{{$item->email}}</td>
        <td>
            @if ($item->status == 1)
                <div class="badge badge-sm bg-gradient-success">Active</div>
            @elseif($item->status == 2)
                <div class="badge badge-sm bg-gradient-success">Active</div>
            @endif
        </td>
        @if (Auth::user()->userroles->role_name == "Admin")
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
          <h5 class="modal-title" id="newStoreModalLabel">Update Party</h5>
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
                    <div class="col-lg-3">
                        <label for="">Party  Group *</label>
                    <div class="input-group input-group-outline">
                        <select name="group_id" class="form-control" id="" required>
                            <option value="">Select Party Group</option>
                            @foreach ($party_groups as $pt_gp)
                                <option value="{{$pt_gp->id}}" {{$pt_gp->id == $item->group_id ? 'selected' : ''}}>{{$pt_gp->group_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    
                    @if (ConfigHelper::getStoreConfig()["use_accounting_module"])
                    <div class="col-lg-3">
                        <label for="">Opening Balance</label>
                    <div class="input-group input-group-outline">
                      <input type="number" name="opening_balance" value="{{ $item->opening_balance }}" class="form-control" >
                    </div>
                    </div>
                    @endif  
                    <div class="col-lg-4">
                        <label for="">Party  Email</label>
                    <div class="input-group input-group-outline">
                      <input type="email" class="form-control" name="email" value="{{$item->email}}" >
                    </div>
                    </div>  

                    <div class="col-lg-4">
                        <label for="">Party  Phone *</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="phone" value="{{$item->phone}}" >
                    </div>
                    </div>  
                    
                    <div class="col-lg-4">
                      <label for="">Party  Business Name </label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" name="business_name" value="{{$item->business_name ?? ""}}" placeholder="Demo Trades">
                  </div>
                  </div>

                  
                    {{-- <div class="col-lg-4">
                        <label for="">Party  Website </label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" value="{{$item->website}}" name="website"  placeholder="example.com">
                    </div>
                    </div>  --}}

                    
                    {{-- <livewire:location /> --}}
                    @livewire('location', [
                       'initialCountryId' => isset($item->country) && $item->country ? $item->country : '' ,
                        'initialStateId' => isset($item->state) && $item->state ? $item->state : "" ,
                        'initialCityId' => isset($item->city) && $item->city ?$item->city  : ""
                    ])

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


      <!-- Delete Modal -->
      <div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="newStoreModalLabel">Delete Party</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('delete.party',$item->id)}}" method="POST">
                    @csrf
                    @method('delete')
                    <div class="row">
                    
                    <label class="form-label">Are you sure you want to delete {{$item->party_name}}?</label>
                    </div>
               
            </div>
            <div class="modal-footer">
              
              <button type="button" class="btn btn-outline-primary">No</button>
              <button type="submit" class="btn btn-primary">Yes</button>
            </div>
        </form>
          </div>
        </div>
      </div>
      {{-- Delete ModalEnd --}}



       @endforeach
    </tbody>
</table>
{{$parties->links('pagination::bootstrap-4')}}
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
                    <div class="col-lg-3">
                        <label for="">Party  Group *</label>
                    <div class="input-group input-group-outline">
                        <select name="group_id" class="form-control" id="" required>
                            <option value="">Select Party Group</option>
                            @foreach ($party_groups as $pt_grp)
                                <option value="{{$pt_grp->id}}">{{$pt_grp->group_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div> 
                    @if (ConfigHelper::getStoreConfig()["use_accounting_module"])
                    <div class="col-lg-3">
                      <label for="">Opening Balance</label>
                  <div class="input-group input-group-outline">
                     <input type="number" name="opening_balance" class="form-control" id="">
                  </div>
                  </div> 
                  @endif
                    <div class="col-lg-4">
                        <label for="">Party  Email</label>
                    <div class="input-group input-group-outline">
                      <input type="email" class="form-control" name="email" >
                    </div>
                    </div>  

                    <div class="col-lg-4">
                        <label for="">Party  Phone *</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="phone" >
                    </div>
                    </div>  


                    <div class="col-lg-4">
                      <label for="">Party  Business Name </label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" name="business_name"  placeholder="Demo Trades">
                  </div>
                  </div>
                    @livewire('location')

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
        
            <form action="{{route('parties.index')}}" method="GET">
                @csrf
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Party Group</label>
                    <div class="input-group input-group-outline">
                        <select name="party_group" class="form-control" id="" required>
                            <option value="">Select Party Group *</option>
                            @foreach ($party_groups as $group)
                                <option value="{{$group->id}}" {{isset(request()->party_group) && request()->party_group == $group->id ? 'selected' : ''}}>{{$group->group_name}}</option>
                            @endforeach
                        </select>
                    </div>

                    </div> 
                    
                    <div class="col-lg-6">
                    <label for="">Party Name or ID</label>
                    <div class="input-group input-group-outline">
                        <input type="text" name="party_name" value="{{request()->party_name && request()->party_name ? request()->party_name : ''}}" class="form-control" >
                    </div>

                    </div> 

                    <div class="col-lg-6">
                        <label for="">Party Phone</label>
                        <div class="input-group input-group-outline">
                            <input type="text" name="party_phone"
                            value="{{request()->party_phone && request()->party_phone ? request()->party_phone : ''}}"
                             class="form-control" >
                        </div>
    
                        </div> 

                        <div class="col-lg-6">
                            <label for="">Party Email</label>
                            <div class="input-group input-group-outline">
                                <input type="text" name="party_email"
                                value="{{request()->party_email && request()->party_email ? request()->party_email : ''}}"
                                 class="form-control">
                            </div>
        
                        </div> 

                        {{-- <div class="col-lg-6">
                            <label for="">From</label>
                            <div class="input-group input-group-outline">
                                <input type="date" name="from" class="form-control">
                            </div>
        
                        </div> 

                        <div class="col-lg-6">
                            <label for="">To</label>
                            <div class="input-group input-group-outline">
                                <input type="date" name="to" class="form-control">
                            </div>
        
                        </div>  --}}
                </div>
           
        </div> 
        <div class="modal-footer">
          <a href="{{url("parties")}}" class="btn btn-secondary">Reset</a>
          
          <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </form>
      </div>
    </div>
  </div>



   <!-- Filter Modal -->
   <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog ">
      <div class="modal-content ">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Import CSV</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
       <div class="modal-body">
        <form action="{{route("parties.importCSV")}}" method="POST"  enctype="multipart/form-data">
          @csrf
          <div class="row">
                      
            <div class="col-lg-12">
                <label for="">Upload File </label>
          <div class="input-group input-group-outline">
            <input type="file" class="form-control" name="file" required>
          </div>
            </div>
            
           
            
        </div>
       </div> 
        <div class="modal-footer">
          <a href="{{url("parties")}}" class="btn btn-secondary">Reset</a>
          
          <button type="submit" class="btn btn-primary">Import</button>
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