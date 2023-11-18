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
            <h3 class="text-center h3 fw-bold mb-4 mx-1 mx-md-4 mt-4 title">Order # {{$order->tran_no ?? ""}}</h3>
            <h3 class="text-center h3 fw-bold mb-4 mx-1 mx-md-4 mt-4 title">Order Date # {{$order->bill_date ?? $order->created_at}}</h3>
               <h1 class="text-center text-capitalize">Order Status : {{$order->order_process_status ?? "Unknown"}} </h1>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

    {{-- {{$order}} --}}
@endsection