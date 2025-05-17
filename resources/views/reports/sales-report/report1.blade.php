@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-4">
        <h1 class="page-title">Sales Report <small>{{session()->get('sales_filter_deleted') ? '(Deleted)' : ''}}</small></h1>
    </div>
    <div class="col">
        <form action="{{route('sales-report.index')}}" method="GET">
            <div class="btn-grp">
         
                <div class="row .row-customized">
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="start_date" value="{{session()->get('sales_report_start_date')}}" class="form-control">
                          </div>
                      
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="end_date" value="{{session()->get('sales_report_end_date')}}" placeholder="To"  class="form-control">
                          </div>
                      
                    </div>
                    <div class="col-lg-4">
                        <div class="input-group input-group-outline">
                            <select name="customer" class="form-control" id="">
                                <option value="">All Customers</option>
                                <option value="0" {{session()->get('sales_report_customer')  == '0' ? 'selected' : ''}}>Cash</option>
                                <option value="exclude_cash" {{session()->get('sales_report_customer')  == 'exclude_cash' ? 'selected' : ''}}>Exclude Cash</option>
                                @foreach ($customers as $customer)
                                    <option value="{{$customer->id}}" {{session()->get('sales_report_customer')  == $customer->id ? 'selected' : ''}}>{{$customer->party_name}}</option>
                                @endforeach
                            </select>
                          </div>
                      
                    </div>

                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <select name="type" class="form-control" id="">
                                <option value="">Web</option>
                                <option value="pdf">PDF</option>
                            </select>
                          </div>
                      
                    </div>
                    <input type="hidden" name="filter_deleted" value="{{session()->get('sales_filter_deleted') ? 'true' : 'false'}}">
                    <div class="col-lg-2">
                        <button class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
    
<table class="table table-sm table-responsive-sm table-striped">
    <thead>
        <th>ID</th>
        <th>Transaction #</th>
        <th>Customer</th>
        <th>Created at</th>
        <th>User</th>
        <th>Net Amount</th>
        {{-- <th>Recieved</th> --}}
        {{-- <th>Balance</th> --}}
        @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2)
        <th>Actions</th>
        @endif
    </thead>
    <tbody>
       @foreach ($records as $key => $item)
       <tr >
     <td>{{$item->id}}</td>
     <td  class="{{$item->deleted_at !== null ? 'text-danger' : ''}}">{{$item->tran_no}}</td>
     <td>{{isset($item->customer) ? $item->customer->party_name : 'Cash'}}</td>    
    <td>{{date('d-M-y | h:m' , strtotime($item->created_at))}}</td>
    <td>{{$item->user->name}}</td>   
    <td> {{ConfigHelper::getStoreConfig()["symbol"].$item->net_total}}</td>
    {{-- <td>{{ConfigHelper::getStoreConfig()["symbol"]. $item->recieved}}</td>  --}}
    {{-- <td>{{ConfigHelper::getStoreConfig()["symbol"]. (round($item->net_total - $item->recieved)) }}</td>  --}}
        @if (Auth::user()->role_id == 1  || Auth::user()->role_id == 2 )
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
                  @if ( Auth::check() && Auth::user()->storeConfig->enable_dc)
                      <li><a class="dropdown-item popup" href="{{url("/challan/".$item->id."")}}"><i class="fa fa-file-invoice"></i> Print Delivery Challan</a></li>
                      @endif
                  <li><a class="dropdown-item" href="{{url('/sales/edit/'.$item->id.'')}}"><i class="fa fa-edit"></i> Edit</a></li>
                  <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#transactionHistory{{$item->id}}"><i class="fa fa-dollar"></i> Transaction History</a></li>
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


{{--  transaction  history  Modal  --}}

<div class="modal fade" id="transactionHistory{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="newStoreModalLabel">Transaction History: {{$item->tran_no}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="modal-body">
          @if (isset($item->transactions ) && count($item->transactions ))
          <ul>
           @foreach ($item->transactions as $transaction)
           <li>{{$item->tran_no}} | {{date('d/m/Y',strtotime($transaction->created_at))}} | {{ConfigHelper::getStoreConfig()["symbol"].$transaction->amount}}</li>
           @endforeach
           </ul>
       @else
       <h6>There is no transaction currently for {{$item->doc_num}}.</h6>
       @endif
       </div>
      </div>
    </div>
  </div>
</div>

{{--transactionHistory Modal --}}

       @endforeach
    </tbody>
    <tfoot>
      <th colspan="5">Total</th>
      <th>{{ConfigHelper::getStoreConfig()["symbol"].number_format($records->sum("net_total"),2)}}</th>
    </tfoot>
</table>


   
        {!! $records->links('pagination::bootstrap-4') !!}
</div>
</div>



@endsection