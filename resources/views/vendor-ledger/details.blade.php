@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">  <small>{{$vendor->party_name ?? ''}} : Ledger Details </small></h1>
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
<table class="table table-sm table-responsive-sm table-striped ">
  <thead>
      <th>S#</th>
      <th>Doc #</th>
      <th>PO #</th>
      <th>Party</th>
      {{-- <th>Gross Total</th> --}}
      <th>Net. Total</th>
      <th>Recieved</th>
      <th>Balance</th>
      <th>Created By</th>
      <th>Created At</th>

      <th>Actions</th>
  </thead>
  <tbody>
      @foreach ($items as $key => $item)
          <tr >
              <td>{{$key+1}}</td>
              <td>{{$item->doc_num}}</td>
              <td><a href="{{url("/purchase/order/".$item->order->id."/edit")}}" style="font-style: italic">{{$item->order->doc_num ?? '(Null)'}}</a></td>
              
              <td>{{$item->party->party_name}}</td>
              {{-- <td>{{ConfigHelper::getStoreConfig()["symbol"].' '.$item->total}}</td> --}}
              <td class="text-primary">
                <b>  {{$item->net_amount}}</b>
              </td>
              <td class="text-primary">
                <b>  {{$item->recieved}}</b>
              </td>
              <td class="text-primary">
                <b>  {{$item->net_amount - $item->recieved}}</b>
              </td>
              <td>{{$item->created_by_user->name}}</td>
              <td>{{date('d.m.y | h:m A' , strtotime($item->created_at))}}</td>
              <td>
                  <div class="s-btn-grp">
                      <a class="btn btn-link text-dark text-sm mb-0 px-0 ms-4 {{$item->created_at != $item->updated_at ? 'text-primary' : ''}}" href="{{url("/purchase/invoice/$item->id/edit")}}"><i class="fa fa-edit"></i></a>
                  </div>
              </td>
          </tr>
      @endforeach
  </tbody>
  <tfoot>
    <th colspan="4">Total</th>
    <th>{{$items->sum('net_amount')}}</th>
    <th>{{$items->sum('recieved')}}</th>
    <th>{{$items->sum('net_amount') - $items->sum('recieved')}}</th>
  </tfoot>
</table>
    
    {{$items->links('pagination::bootstrap-4')}}
<form action="{{route('vendor-ledger.update',$vendor->id)}}" method="POST">
  @csrf
  @method('put')
  <div class="row row-customized">

    <div class="col-lg-4">
        <label for="">Payment Date</label>
         <div class="input-group input-group-outline">  
           <input type="date" class="form-control" name="date" required value="{{date('Y-m-d')}}">
          </div>   
    </div>

    <div class="col-lg-4">
        <label for="">Amount</label>
         <div class="input-group input-group-outline">  
           <input type="number" class="form-control" name="amount" required min="1" max="{{round($items->sum('net_amount') - $items->sum('recieved'))}}">
          </div>   
    </div>

    <div class="col-lg-4">
        <button class="btn btn-primary btn-block">Update</button>  
    </div>
  </div>
</form>
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