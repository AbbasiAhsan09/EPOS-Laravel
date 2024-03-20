@extends('layouts.app')
@section('content')
@include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/eNhzTjJ2rIE'])

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Sale Orders</h1>
    </div>
    <div class="col">
        <div class="btn-grp">
         
            <div class="row .row-customized">
                <div class="col-lg-7">
                    <div class="input-group input-group-outline">
                       <select name="" id="type_search" class="form-control">
                        <option value="sales"  {{session()->get('sales') ? 'selected' : ''}}>Sale</option>
                        <option value="all" {{session()->get('all') ? 'selected' : ''}}>All</option>
                        <option value="canceled" {{session()->get('canceled') ? 'selected' : ''}}>Canceled</option>
                       </select>
                      </div>  
                </div>
                <div class="col-lg-5">
                <button class="btn btn-outline-secondary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">Filter</button>
                <a href="/sales/add" class="btn btn-outline-primary btn-sm mb-0" >Create Order</a href="/sales/add">

                </div>
            </div>
        </div>
    </div>
</div>
@if (Auth::check() && Auth::user()->storeConfig->order_processing)
<ul class="nav nav-pills my-2 " id="process_status_parent">
  <li class="nav-item">
    <span class="nav-link cursor-pointer   {{!request()->has('status') || request('status') == "" ? "text-primary" : '' }}"  data-value="">All</span>
  </li>

  <li class="nav-item active">
    <span class="nav-link cursor-pointer  {{ request('status') == 'pending' ? "text-primary" : '' }}"   data-value="pending" >Pending</span>
  </li>
  <li class="nav-item active">
    <span class="nav-link cursor-pointer  {{ request('status') == 'proceed' ? "text-primary" : '' }}"   data-value="proceed" >Proceed</span>
  </li>
  <li class="nav-item">
    <span class="nav-link cursor-pointer {{ request('status') == 'shipped' ? "text-primary" : '' }}"   data-value="shipped">Shipped</span>
  </li>
  <li class="nav-item">
    <span class="nav-link cursor-pointer {{ request('status') == 'delivered' ? "text-primary" : '' }}"   data-value="delivered">Delivered</span>
  </li>
  
</ul>

@endif
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>ID</th>
            <th>Transaction #</th>
            <th>Customer</th>
            <th>Created at</th>
            <th>User</th>
          
          @if (Auth::check() && Auth::user()->storeConfig->order_processing)
          <th>Status</th>
          @endif
            <th>Net Amount</th>
            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 )
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
        <td>{{$item->user->name}}</td>  
         {{--Status  --}}
            @if (Auth::check() && Auth::user()->storeConfig->order_processing)
              <td>
                
                <div class="input-group input-group-outline">
                    <select class="process_status_select form-control" data-sales-id="{{$item->id ?? ""}}" style="font-weight: 800">
                      <option value="pending" {{$item->order_process_status === 'pending' ? 'selected' : ''}}>Pending</option>
                      <option value="proceed" {{$item->order_process_status === 'proceed' ? 'selected' : ''}}>Proceed</option>
                      <option value="shipped" {{$item->order_process_status === 'shipped' ? 'selected' : ''}}>Shipped</option>
                      <option value="delivered" {{$item->order_process_status === 'delivered' ? 'selected' : ''}}>Delivered</option>
                    </select>
                </div>

              </td>
            @endif
         {{--Status  --}}
        <td> {{ConfigHelper::getStoreConfig()["symbol"].$item->net_total}}</td> 
            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 )
            <td>
                <div class="s-btn-grp">
                  <div class="dropdown">
                  <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4  dropdown-toggle" type="button" id="dropdownMenuButton{{$item->id}}" data-bs-toggle="dropdown" aria-expanded="true">
                      {{-- <i class="fa fa-list"></i> --}}
                  </button>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$item->id}}">
                      {{-- <li><a class="dropdown-item" href="#{{$item->id}}"><i class="fa fa-eye"></i> View</a></li> --}}
                      @if ($item->deleted_at === null)
                      <li><a class="dropdown-item popup" href="{{url("/invoice/".$item->id."")}}"><i class="fa fa-file-invoice"></i> Print Invoice</a></li>
                      @if (Auth::check() && Auth::user()->storeConfig->enable_dc)
                      <li><a class="dropdown-item popup" href="{{url("/challan/".$item->id."")}}"><i class="fa fa-file-invoice"></i> Print Delivery Challan</a></li>
                      @endif
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
    </table>
    
    {{$items->links('pagination::bootstrap-4')}}

</div>
</div>

  <!-- Modal -->
  <div class="modal fade" id="newStoreModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Filter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
       <div class="modal-body">
            <form id="search_filter">
             
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Start Date</label>
                    <div class="input-group input-group-outline">
                      <input type="date" class="form-control" id="start_date" value="{{session()->get('start_date')}}" required>
                    </div>
                    </div> 
                    <div class="col-lg-6">
                      <label for="">End Date</label>
                  <div class="input-group input-group-outline" >
                    <input type="date" class="form-control" id="end_date" value="{{session()->get('end_date')}}" required>
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
          window.location.replace('/sales?type='+$(this).val());
        });

        $('#search_filter').submit(function(e){

          e.preventDefault();
          var start_date = $('#start_date').val();
          var end_date = $('#end_date').val();
          var type = $('#type_search').val();
          window.location.replace('/sales?type='+type+'&start_date='+start_date+'&end_date='+end_date);
        
        });

    $('.popup').click(function(event) {
        event.preventDefault();
        window.open($(this).attr("href"), "popupWindow", "width=300,height=600,scrollbars=yes,left="+($(window).width()-400)+",top=50");
    });

    $('.process_status_select').change(function(){
      var current  = $(this);
      var order_id = current.attr('data-sales-id');
      var status = current.val();

      var params = `sales/change-status?status=${status}&order_id=${order_id}`;
      var url = `{{url('${params}')}}`;
      window.location = url;
      console.log(url);
      
    })

    $("#process_status_parent .nav-link").click(function(){
    
      
            // Get the current URL
            var currentUrl = window.location.href;

            // Check if the "status" parameter is already present
            var statusParam = 'status=';
            var statusIndex = currentUrl.indexOf(statusParam);

            if (statusIndex !== -1) {
                // Extract the value of the existing "status" parameter
                var statusValue = currentUrl.substring(statusIndex + statusParam.length);
                var ampersandIndex = statusValue.indexOf('&');
                if (ampersandIndex !== -1) {
                    statusValue = statusValue.substring(0, ampersandIndex);
                }

                // Remove the existing "status" parameter
                currentUrl = currentUrl.replace(new RegExp('[?&]' + statusParam + statusValue), '');
            }

            // Add the "status=pending" parameter
            var newUrl = currentUrl + (currentUrl.includes('?') ? '&' : '?') + 'status='+$(this).attr('data-value');

            // Redirect to the new URL
            window.location.href = newUrl;

    })
      </script>
  @endsection

@endsection