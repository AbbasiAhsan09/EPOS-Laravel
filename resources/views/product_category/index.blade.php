@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Product Categories</h1>
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
                <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">New Category</button>

                </div>
            </div>
        </div>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>S#</th>
            <th>Category Name</th>
            <th>Status</th>
           
            <th>Actions</th>
        </thead>
        <tbody>
           @foreach ($cat as $key => $item)
           <tr>
            <td>{{$key+1}}</td>
            <td>{{$item->category}}</td>
            <td>
              @if ($item->status == 1)
                  <div class="badge badge-sm bg-gradient-success">Active</div>
              @else
                  
              @endif
            </td>
            
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
          <h5 class="modal-title" id="newStoreModalLabel">Edit Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('update.category',$item->id)}}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                
                
                    <div class="col-lg-12">
                      <label for="">Category Name</label>
                <div class="input-group input-group-outline">
                  <input type="text" class="form-control" name="category" required  value="{{$item->category}}">
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




    <!-- Modal -->
    <div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newStoreModalLabel">Delete Category</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form action="{{route('delete.category',$item->id)}}" method="POST">
                  @csrf
                  @method('put')
                  <div class="row">
                  
                  <label class="form-label">Are you sure you want to delete {{$item->category}}?</label>
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
          <h5 class="modal-title" id="newStoreModalLabel">Create New Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('add.category')}}" method="POST">
                @csrf
                <div class="row">
                  
                    <div class="col-lg-12">
                        <label for="">Category Name</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" name="category" required>
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