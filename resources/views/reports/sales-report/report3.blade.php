@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-4">
        <h1 class="page-title">Sales (Summary)</h1>
    </div>
    <div class="col">
        <form action="{{route('sales-report.summary')}}" method="GET">
            <div class="btn-grp">
         
                <div class="row .row-customized">
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="start_date" 
                            value="{{session()->get('sale-summary-report-start-date')}}" 
                            class="form-control">
                          </div>
                      
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="end_date" 
                            value="{{session()->get('sale-summary-report-end-date')}}" 
                            placeholder="To"  class="form-control">
                          </div>
                      
                    </div>

                    <div class="col-lg-4">
                        <div class="input-group input-group-outline">
                            <select name="customer" id="" class="form-control">
                                <option value="">All Customers</option>
                                @foreach ($customers as $customer)
                                <option value="{{$customer->id}}" {{session()->get('sale-summary-customer') == $customer->id ? 'selected' : ''}}>{{$customer->party_name}}</option>
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
                    <div class="col-lg-2">
                        <button class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@foreach ($records as $item)
    <table class="table  table-responsive-sm table-bordered " style="border: solid 1px" border="1">
        <tr class="bg-dark text-light">
            <th colspan="2">Summary 
            </th>
        </tr>
        <tbody>
           
                <tr>
                    <th width="40%">Party Name: </th>
                    <td>
                <a href="{{url('/reports/sales-report?customer='.$item->id.'&start_date='.session()->get('sale-summary-report-start-date').'&end_date='.session()->get('sale-summary-report-end-date').'')}}" >{{$item->party_name}} </a>
            </td>
                    
                </tr>
                <tr>
                    <th>Sale Invoices (Counts)</th>
                    <td>{{$item->sales->count()}}</td>
                </tr>
                <tr>
                    <th>Gross Total</th>
                    <td>{{env('CURRENCY').' '. round($item->sales->sum('total'))}}</td>
                </tr>
                <tr>
                    <th>Net Total</th>
                    <td>{{env('CURRENCY').' '. round($item->sales->sum('net_total'))}}</td>
                </tr>
                <tr>
                    <th>Paid (Total)</th>
                    <td>{{env('CURRENCY').' '. round($item->sales->sum('recieved'))}}</td>
                </tr>
                <tr>
                    <th>Balance (Total)</th>
                    <td>{{env('CURRENCY').' '.round($item->sales->sum('net_total'))- round($item->sales->sum('recieved'))}}</td>
                </tr>
           
        </tbody>
    </table>
    @endforeach
    <div class="d-flex justify-content-center">
      
    </div>
</div>
</div>



@endsection