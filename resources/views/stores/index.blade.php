@extends('layouts.app')
@section('content')


<div class="page-wrapper">
<div class="container">

    <div class="row row-customized">
        <div class="col">
            <h1 class="page-title">Stores</h1>
        </div>
        <div class="col">
            <div class="btn-grp">
             
                <div class="row .row-customized">
                    <div class="col-lg-8">
                        <div class="input-group input-group-outline">
                            <select name="" id="type_search" class="form-control">
                                <option value="all" {{session()->get('store_all') ? 'selected' : ''}}>All</option>
                                <option value="expired"  {{session()->get('expired') ? 'selected' : ''}}>Expired</option>
                             <option value="running" {{session()->get('running') ? 'selected' : ''}}>Running</option>
                             <option value="trial" {{session()->get('trial') ? 'selected' : ''}}>Trial</option>
                            </select>
                           </div>  
                      
                    </div>
                    <div class="col-lg-4">
                    <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">New Store</button>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>S#</th>
            <th>Store Name</th>
            <th> Phone</th>
            <th>Location</th>
            <th>Type</th>
            <th>Supervisor</th>
            <th>Status</th>
            <th>Actions</th>
        </thead>
        <tbody>
           @foreach ($stores as $key => $item)
           <tr>
            <td>{{$key+1}}</td>
            <td>{{$item->store_name}}</td>
            <td>{{$item->store_phone}}</td>
            <td>{{$item->store_location}}</td>
            <td>{{ucfirst($item->type)}}</td>
            <td>{{$item->users->name}}</td>
            <td>Active</td>
            
            <td>
                <div class="s-btn-grp">
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}">
                        <i class="fa fa-edit"></i>
                    </button>
                    <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}">
                        <i class="fa fa-trash"></i>
                    </button>
                 
                </div>
            </td>
        </tr>

          <!-- Modal -->
  <div class="modal fade" id="newStoreModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Edit Store</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('update.stores',$item->id)}}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-lg-6 mb-2" >
                        <label class="form-label">Store Name</label>
                        <div class="input-group input-group-outline">
                           
                            <input type="text" class="form-control"  name="store_name" required  value="{{$item->store_name}}"  onfocus="focused(this)" onfocusout="defocused(this)">
                          </div>
           
                    </div>
                    <div class="col-lg-6 mb-2">
                        <label class="form-label">Contact</label>

                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="store_phone" required value="{{$item->store_phone}}"  onfocus="focused(this)" onfocusout="defocused(this)">
                          </div>

                       
                    </div>
                    <div class="col-lg-6 mb-2">
                        <label class="form-label">Location</label>

                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="store_location" required value="{{$item->store_location}}"  onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                    </div>

                    <div class="col-lg-6 mb-2">
                        <label class="form-label">Phone</label>

                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="phone" required value="{{$item->phone}}"  onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                    </div>

                    <div class="col-lg-6 mb-2">
                        <label class="form-label">Email</label>

                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="email" required value="{{$item->email}}"  onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                    </div>
                    <div class="col-lg-6 mb-2">
                        <label class="form-label">Website Domain</label>

                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="domain" required value="{{$item->phone}}"  onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-2"><label for="">Store Type</label>
                        <div class="input-group input-group-outline">
                         
                            <select id="" class="form-control" name="type" required  onfocus="focused(this)" onfocusout="defocused(this)">
                                @if ($item->type == 'site')
                                <option value="site">Site</option>
                                <option value="online">Online</option>
                                @else
                                <option value="online">Online</option>
                                <option value="site">Site</option>
                                @endif
                                
                            </select>
                        </div>
                       
                   
                    </div>
                    <div class="col-lg-6 mb-2">
                        <label for="">Supervisor</label>
                        <div class="input-group input-group-outline">
                            <select id="" class="form-control" readonly name="store_supervisor" required  onfocus="focused(this)" onfocusout="defocused(this)">
                                <option value="">Select Supervisor</option>
                                @foreach ($users as $user)
                                    <option value="{{$user->id}}" {{$user->id == $item->store_supervisor ? 'selected' : '' }}>{{$user->name}}</option>
                                @endforeach
                            </select>
                           
                        </div>
                       
                        
                    </div>

                    <div class="col-lg-6 mb-2">
                        <label for="">Stauts</label>
                        <div class="input-group input-group-outline">
                            <select id="" class="form-control" readonly name="status" required  onfocus="focused(this)" onfocusout="defocused(this)">
                                <option value="1" {{$item->is_locked ? 'selected' : ''}}>Locked</option>
                                <option value="0" {{!$item->is_locked ? 'selected' : ''}}>Running</option>
                                <option value="0" {{!$item->is_locked && $item->renewal_date == null ? 'selected' : ''}}>Trial</option>
                            </select>
                           
                        </div>
                       
                        
                    </div>

                    <div class="col-lg-6 mb-2">
                        <label for="">Renewal Date</label>
                        <div class="input-group input-group-outline">
                           <input type="date" required name="renewal_date" value="{{$item->renewal_date}}" class="form-control">
                        </div>
                    </div>

                </div>
           
        </div>
        <div class="modal-footer">
          
            <button type="submit" class="btn btn-primary">UPdate</button>

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
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Create New Store</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('add.stores')}}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-lg-6 mb-2" >
                        <div class="input-group input-group-outline">
                            <label class="form-label">Store Name *</label>
                           
                            <input type="text" class="form-control"  name="store_name" required    onfocus="focused(this)" onfocusout="defocused(this)">
                          </div>
           
                    </div>
                    <div class="col-lg-6 mb-2">

                        <div class="input-group input-group-outline">
                            <label class="form-label">Contact *</label>
                            <input type="text" class="form-control" name="store_phone" required   onfocus="focused(this)" onfocusout="defocused(this)">
                          </div>

                       
                    </div>
                    <div class="col-lg-6 mb-2">

                        <div class="input-group input-group-outline">
                            <label class="form-label">Contact 2</label>
                            <input type="text" class="form-control" name="phone"  onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                    </div>
                    <div class="col-lg-6 mb-2">

                        <div class="input-group input-group-outline">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" name="store_location" required   onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                    </div>

                  

                    <div class="col-lg-6 mb-2">

                        <div class="input-group input-group-outline">
                            <label class="form-label">Email</label>
                            <input type="text" class="form-control" name="email"   onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                    </div>
                    <div class="col-lg-6 mb-2">

                        <div class="input-group input-group-outline">
                            <label class="form-label">Website Domain</label>
                            <input type="text" class="form-control" name="domain"   onfocus="focused(this)" onfocusout="defocused(this)">
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-2">
                        <div class="input-group input-group-outline">
                         
                            <select id="" class="form-control" name="type" required  onfocus="focused(this)" onfocusout="defocused(this)">
                                <option value="">Select Type</option>
                                <option value="site">Site</option>
                                <option value="online">Online</option>
                               
                                     
                            </select>
                        </div>
                       
                   
                    </div>
                    <div class="col-lg-6 mb-2">
                        <div class="input-group input-group-outline">
                            <select id="" class="form-control" name="store_supervisor" required  onfocus="focused(this)" onfocusout="defocused(this)">
                                <option value="">Select Supervisor</option>
                                @foreach ($users as $user)
                                    <option value="{{$user->id}}">{{$user->name}}</option>
                                @endforeach
                            </select>
                           
                        </div>
                       
                        
                    </div>
                    
                <div class="col-lg-6 mb-2">
                    <label for="">Stauts</label>
                    <div class="input-group input-group-outline">
                        <select id="" class="form-control" readonly name="status" required  onfocus="focused(this)" onfocusout="defocused(this)">
                            <option value="1" >Locked</option>
                            <option value="0" >Running</option>
                            <option value="trial" >Trial</option>
                        </select>
                       
                    </div>
                   
                    
                </div>

                <div class="col-lg-6 mb-2">
                    <label for="">Renewal Date</label>
                    <div class="input-group input-group-outline">
                       <input type="date" required name="renewal_date" value="{{date('Y-m-d')}}" class="form-control">
                    </div>
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

  @section('scripts')
  <script>
    $('#type_search').change(function(){
      window.location.replace('/store?filter='+$(this).val());
    });


$('.popup').click(function(event) {
    event.preventDefault();
    window.open($(this).attr("href"), "popupWindow", "width=300,height=600,scrollbars=yes,left="+($(window).width()-400)+",top=50");
});

  </script>
@endsection
@endsection