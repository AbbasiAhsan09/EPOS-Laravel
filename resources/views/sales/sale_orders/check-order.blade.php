@extends('layouts.client')
@section('content')
<section class="vh-100 reg-screen" >
    <div class="container h-100 ">
      <div class="row d-flex justify-content-center align-items-center h-100 ">
        <div class="col-lg-12 col-xl-11">
          <div class="card text-black transparent-s" style="border-radius: 25px;">
            <div class="card-body p-md-5">
                @if ($order->store->config)
                <img src="{{asset("images/logo/".$order->store->config->logo."")}}" width="180px" style="display: block; margin :auto " alt="">
                @else
                    <img src="{{asset('images/logo.png')}}" width="180px" style="display: block; margin :auto " alt="">
                @endif
               
                
                    <div class="row">
                        <div class="col-lg-4">
                    <h3 class="title">To </h3>
                    @if ($order->customer)
                    {{$order->customer->business_name ?? ""}} <br>
                    {{$order->customer->party_name ?? ""}} <br>
                    {{$order->customer->phone}} <br>
                    @else 
                    Cash
                    @endif
                        </div>

                        <div class="col-lg-4">
                            <h3 class="title">From</h3>
                            @if ($order->store->config)
                                {{$order->store->config->app_title}} <br>
                                {{$order->store->config->address ?? ""}} <br>
                                {{$order->store->config->phone ?? ""}} 
                            @endif
                        </div>
                        <div class="col-lg-4">
                            <h3 class="h3 fw-bold mb-4 mx-1 mx-md-4 mt-4 title">Order # {{$order->tran_no ?? ""}}</h3>
                            <h3 class="h3 fw-bold mb-4 mx-1 mx-md-4 mt-4 title">Order Date : {{$order->bill_date ?? $order->created_at}}</h3>
                            <h4 class="text-uppercase fw-bold mb-4 mx-1 mx-md-4 mt-4 title">Order Status : {{$order->order_process_status ?? "Unknown"}} </h4>
            
                        </div>
                    </div>
                
                 
                @if ($order->order_details)
                <br><br>
                <table id="" class="table table-responsive-sm table-bordered">
                    <thead>
                        <tr>
                            <th>S#</th>
                            {{-- <th>Field</th>
                            <th>Category</th> --}}
                            <th>Description</th>
                            <th>Rate</th>
                            <th>Tax</th>
                            <th>Disc.</th>
                            <th>Qty.</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->order_details as $key => $item)
                    <tr  >
                        <td>{{$key+1}}</td>
                        {{-- <td>{{$item->item_details->categories->field->name ?? ''}}</td>
                        <td>{{$item->item_details->categories->category ?? ''}}</td> --}}
                        <td>{{$item->item_details->name ?? ''}}</td>
                        <td>{{$item->rate}}</td>
                        <td>{{$item->tax}}</td>
                        <td>{{$item->disc}}</td>
                        <td>{{$item->qty}}</td>
                        <td>{{$item->total}}</td>
                    </tr>
                      @endforeach
                    
                      <tr class="footer-total">
                        {{-- <th colspan="7"></th> --}}
                        <th colspan="5">Gross Total</th>
                        <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].round($order->gross_total)}}</th>
                      </tr>
                      @if ($order->discount > 0)
                      <tr class="footer-total">
                        {{-- <th colspan="7"></th> --}}
                        <th colspan="5">Discount</th>
                        <th colspan="2">{{$order->discount_type == 'PERCENT' ? '%'.Round($order->discount) : ConfigHelper::getStoreConfig()["symbol"].Round($order->discount)}}</th>
                      </tr>
                      @endif
    
                      @if ( $order->other_charges > 0)
                      <tr class="footer-total">
                        {{-- <th colspan="7"></th> --}}
                        <th colspan="5">Other Charges</th>
                        <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].round($order->other_charges)}}</th>
                      </tr>
                      @endif
                      <tr class="footer-total">
                        {{-- <th colspan="7"></th> --}}
                        <th colspan="5">Net Total</th>
                        <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].round($order->net_total)}}</th>
                      </tr>
                      <tr class="footer-total">
                        {{-- <th colspan="7"></th> --}}
                        <th colspan="5">Received</th>
                        <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].round($order->recieved ?? 0)}}</th>
                      </tr>
    
                      @if ($order->recieved)
                      <tr class="footer-total">
                        {{-- <th colspan="7"></th> --}}
                        <th colspan="5">Balance</th>
                        <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].round((($order->net_total ?? 0) - ($order->recieved ?? 0)) ?? 0)}}</th>
                      </tr>
                      @endif
    
                    </tbody>
                </table>
                @endif
                <p>This software is developed by <b>TradeWisePOS</b> | WhatsApp : 0320 0681969</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

    {{-- {{$order}} --}}
@endsection