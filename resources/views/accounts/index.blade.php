@extends('layouts.app')
@section('content')
@include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/hGkaaHxzxlo'])

<div class="page-wrapper">
<div class="container">
 
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Accounts</h1>
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
                <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#accountModal">New UOM</button>

                </div>
            </div>
        </div>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>S#</th>
            <th>Title</th>
            <th>Description</th>
            <th>Type</th>
            <th>Opening Balance</th>
            <th>Balance</th>
           
            <th>Actions</th>
        </thead>
        <tbody>
           @foreach ($items as $key => $item)
           <tr>
            <td>{{$key+1}}</td>
            <td>{{$item->title}}</td>
            <td>{{$item->description}}</td>
            <td>{{ucfirst($item->type ?? "")}}</td>
            <td>{{ $item->opening_balance}}</td>
            <td>N/A</td>
            
            <td>
              @if (!$item->reference_type && !$item->reference_id)
              <div class="s-btn-grp">
                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" data-bs-toggle="modal" data-bs-target="#accountModal{{$item->id}}">
                    <i class="fa fa-edit"></i>
                </button>
                {{-- <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}">
                    <i class="fa fa-trash"></i>
                </button> --}}
             
              </div>  
              @else
              {{ucfirst($item->reference_type)}}
              @endif
            </td>
        </tr>

          <!-- Modal -->
 <div class="modal fade" id="accountModal{{$item->id}}" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="accountModalLabel">Edit UOM</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('account.update',$item->id)}}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Title</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="title" value="{{$item->title ?? ""}}" required>
                    </div>
                    </div>
                    <div class="col-lg-12">
                        <label for="">Description</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="description" value="{{$item->description ?? ""}}">
                    </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Type</label>
                    <div class="input-group input-group-outline">
                      {{-- <input type="number" min="1" step="0.01" class="form-control" name="base_unit_value" required > --}}
                        <select name="type"  class="form-control" required>
                            <option value="">Select Account Type</option>
                            @foreach (['assets', 'expenses', 'income', 'equity', 'liabilities'] as $value)
                            <option value="{{ $value }}" {{ $item->type && $item->type === $value ? 'selected' : '' }}>{{ ucfirst($value) }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Opening Balance</label>
                    <div class="input-group input-group-outline">
                      <input type="number"  step="0.01" class="form-control" value="{{$item->opening_balance}}" name="opening_balance" >
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
    {{-- <div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="accountModalLabel">Delete UOM</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form action="{{route('delete.uom',$item->id)}}" method="POST">
                  @csrf
                  @method('put')
                 <label class="form-label">Are you sure you want to delete {{$item->uom}}</label>
             
          </div>
          <div class="modal-footer">
            
            <button type="button" class="btn btn-outline-primary">No</button>
            <button type="submit" class="btn btn-primary">Yes</button>
  
  
          </div>
      </form>
        </div>
      </div>
    </div> --}}
    {{--Delete Modal --}}
           @endforeach
        </tbody>
    </table>
</div>
</div>

  
  <!-- Modal -->
  <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="accountModalLabel">Create New Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('account.add')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Title</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="title" required>
                    </div>
                    </div>
                    <div class="col-lg-12">
                        <label for="">Description</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="description"">
                    </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Type</label>
                    <div class="input-group input-group-outline">
                      {{-- <input type="number" min="1" step="0.01" class="form-control" name="base_unit_value" required > --}}
                        <select name="type"  class="form-control" required>
                            <option value="">Select Account Type</option>
                            @foreach (['assets', 'expenses', 'income', 'equity', 'liabilities'] as $value)
                            <option value="{{ $value }}">{{ ucfirst($value) }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Opening Balance</label>
                    <div class="input-group input-group-outline">
                      <input type="number"  step="0.01" class="form-control" name="opening_balance" >
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