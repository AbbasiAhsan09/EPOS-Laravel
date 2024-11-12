@extends('layouts.app')

@section('content')
@include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/hGkaaHxzxlo'])
@php
    $is_trial = isset(Auth::user()->store->is_trial) ? Auth::user()->store->is_trial:  false;
    $renewalDate = isset(Auth::user()->store->renewal_date) ? Carbon\Carbon::parse(Auth::user()->store->renewal_date) : false;
@endphp
<div class="container-fluid">
    <h2 class="pb-4">
      Hello, <a href="{{url("/profile")}}">{{ Auth::check() ? strtoupper(Auth::user()->name) : "" }}</a> 😊
      <p><b>
        Store: {{Auth::check() ? Auth::user()->store->store_name ?? "": ''}}
      </b></p>
    </h2>
   @if ((Auth::check() && $is_trial ))
   <div class="card mb-5">
    <div class="card-body">
      <div class="row">
        <div class="col">
          <h3 class="text-primary">{{\Carbon\Carbon::now()->diffInDays(Auth::user()->store->created_at->addDays(14))}} Days' Remaining</h3>
          <p>Your 14 days trial is expiring on <b class="text-danger">{{date('D d M,Y',strtotime(Auth::user()->store->created_at->addDays(14)))}}</b></p>    
        </div>
        <div class="col d-flex justify-content-end align-items-center">
          <a class="btn btn-primary" target="_blank" href="{{url("/payment")}}">Subscribe Now</a>
        </div>
      </div>
    </div>
  </div>
   @endif
   @if ($renewalDate != false)
   @if (((Auth::check() && !$is_trial && ($renewalDate->lessThanOrEqualTo(\Carbon\Carbon::now()->addDays(15)))) ))
   <div class="card mb-5">
    <div class="card-body">
      <div class="row">
        <div class="col">
          <h3 class="text-primary">{{\Carbon\Carbon::now()->diffInDays($renewalDate)}} Days' Remaining</h3>
          <p>Your annual subscription is expiring on <b class="text-danger">{{$renewalDate->format('D, d M, Y')}}</b></p>    
        </div>
        <div class="col d-flex justify-content-end align-items-center">
          <a class="btn btn-primary" target="_blank" href="{{url("/payment")}}">Re-Subscribe Now</a>
        </div>
      </div>
    </div>
  </div>
   @endif    
   @endif
    {{-- Quick Buttons --}}
    <div class="row mb-3">
      <div class="col-lg-2">
        <a href="/sales/add" class="btn btn-primary w-100 btn-large btn-dashboard">Create Sale</a>
      </div>
      <div class="col-lg-2">
        <a href="/purchase/invoice/0/create" class="btn btn-info w-100 btn-large btn-dashboard">Create Purchase</a>
      </div>
      
      @if (ConfigHelper::getStoreConfig()["use_accounting_module"])
      <div class="col-lg-2">
        <a href="/account/journal" class="btn btn-secondary w-100 btn-large btn-dashboard">Transactions</a>
      </div>
      @else
      <div class="col-lg-2">
        <a href="/parties" class="btn btn-secondary w-100 btn-large btn-dashboard">Parties</a>
      </div>
      @endif

      <div class="col-lg-2">
        <a href="/reports/inventory-balance" class="btn btn-warning w-100 btn-large btn-dashboard">Inventory Balance</a>
      </div>
      <div class="col-lg-2">
        <a href="/products" class="btn btn-success w-100 btn-large btn-dashboard">Items</a>
      </div>
      <div class="col-lg-2">
        <a href="/reports/" class="btn btn-secondary w-100 btn-large btn-dashboard">Reports</a>
      </div>
    </div>
    {{-- Quick Buttons --}}
    <div class="row">
      <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
              <i class="material-icons opacity-10">shopping_cart</i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">{{date('M')}}  Sales</p>
              <h4 class="mb-0">{{ConfigHelper::getStoreConfig()["symbol"].' '.round($sales->sum('net_total'))}}</h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            {{-- <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than last week</p> --}}
          </div>
        </div>
      </div>
      <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
        <div class="card">
          <div class="card-header p-3 pt-2">
            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
              <i class="material-icons opacity-10">inventory_2</i>
            </div>
            <div class="text-end pt-1">
              <p class="text-sm mb-0 text-capitalize">{{date('M')}}  Purchases</p>
              <h4 class="mb-0">{{ConfigHelper::getStoreConfig()["symbol"].' '.round($purchases->sum('net_amount'))}}</h4>
            </div>
          </div>
          <hr class="dark horizontal my-0">
          <div class="card-footer p-3">
            {{-- <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+3% </span>than last month</p> --}}
          </div>
        </div>
      </div>

   
    </div>
   
    <div class="row mt-4">
      <div class="col-lg-4 col-md-6 mt-4 mb-4">
        <div class="card z-index-2 ">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
            <div class="bg-gradient-primary shadow-primary border-radius-lg py-3 pe-1">
              <div class="chart">
                <canvas id="chart-bars" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
          </div>
          <div class="card-body">
            <h6 class="mb-0 ">Weekly Sales</h6>
            {{-- <p class="text-sm ">Last Campaign Performance</p> --}}
            <hr class="dark horizontal">
            {{-- <div class="d-flex ">
              <i class="material-icons text-sm my-auto me-1">schedule</i>
              <p class="mb-0 text-sm"> campaign sent 2 days ago </p>
            </div> --}}
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6 mt-4 mb-4">
        <div class="card z-index-2  ">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
            <div class="bg-gradient-success shadow-success border-radius-lg py-3 pe-1">
              <div class="chart">
                <canvas id="chart-line" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
          </div>
          <div class="card-body">
            <h6 class="mb-0 "> Monthly Sales </h6>
            {{-- <p class="text-sm "> (<span class="font-weight-bolder">+15%</span>) increase in today sales. </p> --}}
            <hr class="dark horizontal">
            {{-- <div class="d-flex ">
              <i class="material-icons text-sm my-auto me-1">schedule</i>
              <p class="mb-0 text-sm"> updated 4 min ago </p>
            </div> --}}
          </div>
        </div>
      </div>
      <div class="col-lg-4 mt-4 mb-3">
        <div class="card z-index-2 ">
          <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2 bg-transparent">
            <div class="bg-gradient-dark shadow-dark border-radius-lg py-3 pe-1">
              <div class="chart">
                <canvas id="chart-line-tasks" class="chart-canvas" height="170"></canvas>
              </div>
            </div>
          </div>
          <div class="card-body">
            <h6 class="mb-0 ">Monthly Purchases </h6>
            {{-- <p class="text-sm ">Last Campaign Performance</p> --}}
            <hr class="dark horizontal">
            {{-- <div class="d-flex ">
              <i class="material-icons text-sm my-auto me-1">schedule</i>
              <p class="mb-0 text-sm">just updated</p>
            </div> --}}
          </div>
        </div>
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-lg-6 col-md-6 mb-md-0 mb-4">
        <div class="card">
          <div class="card-header pb-0">
            <div class="row">
              <div class="col-lg-6 col-7">
                <h6>Sales</h6>
                <p class="text-sm mb-0">
                  <i class="fa fa-check text-info" aria-hidden="true"></i>
                  <span class="font-weight-bold ms-1"></span> This month
                </p>
              </div>
              <div class="col-lg-6 col-5 my-auto text-end">
                <div class="dropdown float-lg-end pe-4">
                  <a class="cursor-pointer" id="dropdownTable" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-ellipsis-v text-secondary"></i>
                  </a>
                  <ul class="dropdown-menu px-2 py-3 ms-sm-n4 ms-n5" aria-labelledby="dropdownTable">
                    <li><a class="dropdown-item border-radius-md" href="{{url('/sales/add')}}">New Sale</a></li>
                    <li><a class="dropdown-item border-radius-md" href="{{url('/reports/sales-report')}}">Sales Report</a></li>
                    {{-- <li><a class="dropdown-item border-radius-md" href="javascript:;">Something else here</a></li> --}}
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="card-body px-0 pb-2">
            <div class="table-responsive">
              <table class="table align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Doc #</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Party</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Gross Total</th>
                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Net Total</th>
                  </tr>
                </thead>
                <tbody>
                 @foreach ($sales as $item)
                 <tr>
                  <td>
                    <div class="d-flex px-2 py-1">
                      
                      <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-sm">{{$item->tran_no}}</h6>
                      </div>
                    </div>
                  </td>
                  <td>
                    {{$item->customer->party_name ?? 'Cash'}}
                  </td>
                  <td class="align-middle text-center text-sm">
                    <span class="text-xs font-weight-bold"> {{ConfigHelper::getStoreConfig()["symbol"].' '.$item->gross_total}} </span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-xs font-weight-bold"> {{ConfigHelper::getStoreConfig()["symbol"].' '.$item->net_total}} </span>
                  </td>
                </tr>
                 @endforeach
             
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-3">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6>Balance Details</h6>
            {{-- <p class="text-sm">
              <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
              <span class="font-weight-bold">24%</span> this month
            </p> --}}
          </div>
          <div class="card-body p-3">
            <div class="timeline timeline-one-side">
              <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-warning text-gradient">sell</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">{{ConfigHelper::getStoreConfig()["symbol"]. round($saleBalance->sum('net_total') -  $saleBalance->sum('recieved'))}}</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Sales Balance (Overall Cash)</p>
                </div>
              </div>
              <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-success text-gradient">sell</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">{{ConfigHelper::getStoreConfig()["symbol"]. round($saleBalanceParties->sum('net_total') -  $saleBalanceParties->sum('recieved'))}}</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Sales Balance (Overall Parties)</p>
                </div>
              </div>
              <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-danger text-gradient">inventory_2</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">{{ConfigHelper::getStoreConfig()["symbol"]. round(($purchaseBalance->sum('net_amount') -  $purchaseBalance->sum('recieved')))}}</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Purchase Balance (Overall)</p>
                </div>
              </div>
             
              {{-- <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-warning text-gradient">credit_card</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">New card added for order #4395133</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">20 DEC 2:20 AM</p>
                </div>
              </div>
              <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-primary text-gradient">key</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">Unlock packages for development</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">18 DEC 4:54 AM</p>
                </div>
              </div>
              <div class="timeline-block">
                <span class="timeline-step">
                  <i class="material-icons text-dark text-gradient">payments</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">New order #9583120</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">17 DEC</p>
                </div>
              </div> --}}
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-3 col-md-3">
        <div class="card h-100">
          <div class="card-header pb-0">
            <h6>Overall Summary</h6>
            {{-- <p class="text-sm">
              <i class="fa fa-arrow-up text-success" aria-hidden="true"></i>
              <span class="font-weight-bold">24%</span> this month
            </p> --}}
          </div>
          <div class="card-body p-3">
            <div class="timeline timeline-one-side">
              <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-warning text-gradient">sync_alt</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">{{ConfigHelper::getStoreConfig()["symbol"]. round($totalSales -  $totalPurchase)}}</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Overall Outstanding Account</p>
                </div>
              </div>
              <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-success text-gradient">arrow_forward_ios</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">{{ConfigHelper::getStoreConfig()["symbol"]. round($totalSales)}}</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Overall Sales </p>
                </div>
              </div>
              <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-danger text-gradient">arrow_back_ios</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">{{ConfigHelper::getStoreConfig()["symbol"]. round($totalPurchase)}}</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">Overall Purchase</p>
                </div>
              </div>
             
              {{-- <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-warning text-gradient">credit_card</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">New card added for order #4395133</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">20 DEC 2:20 AM</p>
                </div>
              </div>
              <div class="timeline-block mb-3">
                <span class="timeline-step">
                  <i class="material-icons text-primary text-gradient">key</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">Unlock packages for development</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">18 DEC 4:54 AM</p>
                </div>
              </div>
              <div class="timeline-block">
                <span class="timeline-step">
                  <i class="material-icons text-dark text-gradient">payments</i>
                </span>
                <div class="timeline-content">
                  <h6 class="text-dark text-sm font-weight-bold mb-0">New order #9583120</h6>
                  <p class="text-secondary font-weight-bold text-xs mt-1 mb-0">17 DEC</p>
                </div>
              </div> --}}
            </div>
          </div>
        </div>
      </div>
    </div>
    
  </div>
@endsection
