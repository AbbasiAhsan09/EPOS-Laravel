@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">  <small>{{$customer->party_name ?? ''}} : Ledger Details </small></h1>
    </div>
    {{-- <div class="col">
        <form action="{{ route('customer-ledger.index') }}" method="GET">
            <div class="row flex-end">
                <div class="col-lg-3">
                    <label for="">From</label>
                     <div class="input-group input-group-outline">  
                       <input type="date" class="form-control">
                      </div>   
                </div>
                <div class="col-lg-3">
                    <label for="">To</label>
                     <div class="input-group input-group-outline">  
                       <input type="date" class="form-control">
                      </div>   
                </div>
                <div class="col-lg-3">
                    <label for="">Customers</label>
                     <div class="input-group input-group-outline">  
                      <select name="" id="" class="select2Style form-control ">
                        <option value="">All</option>

                      </select>
                      </div>   
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-primary btn-block">Search</button>
                </div>
            
            </div>
        </form>
     
    </div> --}}
</div>
<table class="table table-sm table-responsive-sm table-striped">
    <thead>
        <th>ID</th>
        <th>Transaction #</th>
        <th>Customer</th>
        <th>Created at</th>
        {{-- <th>User</th> --}}
        <th>Net Amount</th>
        <th>Recieved</th>
        <th>Balance</th>
        @if (Auth::user()->role_id == 1)
        <th>Actions</th>
        @endif
    </thead>
    <tbody>
       @foreach ($items as $key => $item)
       <tr >
     <td>{{$item->id}}</td>
     <td  class="{{$item->deleted_at !== null ? 'text-danger' : ''}}">{{$item->tran_no}}</td>
     <td>{{isset($item->customer) ? $item->customer->party_name : 'Cash'}}</td>    
    <td>{{date('d-M-y | h:m' , strtotime($item->created_at))}}</td>
    {{-- <td>{{$item->user->name}}</td>    --}}
    <td> {{env('CURRENCY').$item->net_total}}</td> 
    <td> {{env('CURRENCY').$item->recieved}}</td> 
    <td> {{env('CURRENCY').round($item->net_total - $item->recieved)}}</td> 

        @if (Auth::user()->role_id ==1 )
        <td>
            <div class="s-btn-grp">
              <div class="dropdown">
                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4  dropdown-toggle" type="button" id="dropdownMenuButton{{$item->id}}" data-bs-toggle="dropdown" aria-expanded="true">
                    {{-- <i class="fa fa-list"></i> --}}
                </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$item->id}}">
                 
                  @if ($item->deleted_at === null)
                  <li><a class="dropdown-item popup" href="{{url("/invoice/".$item->id."")}}"><i class="fa fa-file-invoice"></i> Print Invoice</a></li>
                  <li><a class="dropdown-item" href="{{url('/sales/edit/'.$item->id.'')}}"><i class="fa fa-edit"></i> Edit</a></li>
                  <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}"><i class="fa fa-trash"></i> Delete</a></li>
                  @endif
              </ul>
              </div>
          
            </div>
          </td>
        @endif

    </tr>

{{--  Delete Modal  --}}

<div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newStoreModalLabel">Delete Sale: {{$item->tran_no}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
          <form action="{{route('delete.sale',$item->id)}}" method="POST">
              @csrf
              @method('delete')
             <label class="form-label">Are you sure you want to delete {{$item->tran_no}}</label>
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
    <tfoot>
        <th colspan="4"> Total</th>
        <th>{{$items->sum('net_total')}}</th>
        <th>{{$items->sum('recieved')}}</th>
        <th>{{round($items->sum('net_total') - $items->sum('recieved'))}}</th>
    </tfoot>
</table>
    
    {{$items->links('pagination::bootstrap-4')}}

    <div class="row row-customized">
        <div class="col-lg-4">
            <label for="">Payment Date</label>
             <div class="input-group input-group-outline">  
               <input type="date" class="form-control" name="" >
              </div>   
        </div>
        <div class="col-lg-4">
            <label for="">Amount</label>
             <div class="input-group input-group-outline">  
               <input type="number" class="form-control" name="" max="{{round($items->sum('net_total') - $items->sum('recieved'))}}">
              </div>   
        </div>
        <div class="col-lg-4">
            <button class="btn btn-primary btn-block">Update</button>  
        </div>
      </div>
</div>
</div>

@section('scripts')
<script>
      $('.popup').click(function(event) {
        event.preventDefault();
        window.open($(this).attr("href"), "popupWindow", "width=300,height=600,scrollbars=yes,left="+($(window).width()-400)+",top=50");
    });
</script>
@endsection

@endsection