@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container">
 
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Party Groups</h1>
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
                <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">New Group</button>

                </div>
            </div>
        </div>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>S#</th>
            <th>Group Name</th>
            @if (Auth::user()->role_id == 1)
            <th>Actions</th>
            @endif
        </thead>
        <tbody>
           @foreach ($items as $key => $item)
           <tr>
         <td>{{$key+1}}</td>
         <td>{{$item->group_name}}</td>    
          
            @if (Auth::user()->role_id ==1 )
            <td>
                <div class="s-btn-grp">
                  <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}">
                      <i class="fa fa-edit"></i>
                  </button>
                  {{-- <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}">
                      <i class="fa fa-trash"></i>
                  </button> --}}
               
              </div>
              </td>
            @endif

        </tr>

          <!-- Modal -->
  <div class="modal fade" id="newStoreModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Edit Group</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      <div class="modal-body">
            <form action="{{route('edit.partyGroup',$item->id)}}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Party Group Name</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="group_name" required  value="{{$item->group_name}}">
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
            <h5 class="modal-title" id="newStoreModalLabel">Delete UOM</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          {{-- <div class="modal-body">
              <form action="{{route('delete.uom',$item->id)}}" method="POST">
                  @csrf
                  @method('put')
                 <label class="form-label">Are you sure you want to delete {{$item->uom}}</label>
             
          </div> --}}
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
          <h5 class="modal-title" id="newStoreModalLabel">Create New Group</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
       <div class="modal-body">
            <form action="{{route('add.partyGroup')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Party Group Name</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="group_name" required>
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