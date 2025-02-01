@extends('layouts.app')
@section('content')
@include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/hGkaaHxzxlo'])

<div class="page-wrapper">
<div class="container">
 
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Labours</h1>
    </div>
    <div class="col">
        <div class="btn-grp">
         
            <div class="row .row-customized">
                <div class="col-lg-8">
                    <form action="{{route("labour.index")}}">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" onfocus="focused(this)" 
                            onfocusout="defocused(this)" name="search" value="{{request()->search}}">
                          </div>
                    </form>
                </div>
                <div class="col-lg-4">
                <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">Add Labour</button>

                </div>
            </div>
        </div>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>S#</th>
            <th>Name</th>
            <th>Phone</th>
            <th>Address</th>
            <th>Description</th>
            <th>Actions</th>
        </thead>
        <tbody>
           @foreach ($items as $key => $item)
           <tr>
            <td>{{$key+1}}</td>
            <td>{{$item->name}}</td>
            <td>{{$item->phone}}</td>
            <td>{{$item->address}}</td>
            <td>{{ $item->description }}</td>
            <td>
              <div class="s-btn-grp">
                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}">
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
          <h5 class="modal-title" id="newStoreModalLabel">Edit Labour</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('labour.update',$item->id)}}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Name</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="name" required  value="{{$item->name}}">
                    </div>
                    </div>
                    <div class="col-lg-12">
                        <label for="">Phone</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="pone"   value="{{$item->phone}}">
                    </div>
                    </div>
                    <div class="col-lg-12">
                      <label for="">Address</label>
                  <div class="input-group input-group-outline">
                    <input type="number" min="1" step="0.01" class="form-control" name="address"   value="{{$item->address}}">
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



    <!-- Delete Modal -->
    <div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newStoreModalLabel">Delete Labour</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form action="{{route('labour.destroy',$item->id)}}" method="POST">
                  @csrf
                  @method('delete')
                 <label class="form-label">Are you sure you want to delete {{$item->name}}</label>
             
          </div>
          <div class="modal-footer">
            
            <button type="button" class="btn btn-outline-primary">No</button>
            <button type="submit" class="btn btn-primary">Yes</button>
  
  
          </div>
      </form>
        </div>
      </div>
    </div>
    {{--Delete Modal --}}
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
          <h5 class="modal-title" id="newStoreModalLabel">Add New Labour</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('labour.store')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Name</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="name" required>
                    </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Phone</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="phone"  >
                    </div>
                    </div>
                    <div class="col-lg-6">
                      <label for="">Address</label>
                  <div class="input-group input-group-outline">
                    <input type="number" min="1" step="0.01" class="form-control" name="address"  >
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
@endsection