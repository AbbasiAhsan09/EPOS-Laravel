@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-4">
        <h1 class="page-title">Purchase (Summary)</h1>
    </div>
    <div class="col">
        <form action="{{route('purchase-report.summary')}}" method="GET">
            <div class="btn-grp">
         
                <div class="row .row-customized">
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="start_date" 
                            value="{{session()->get('purchase-summary-report-start-date')}}" 
                            class="form-control">
                          </div>
                      
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="end_date" 
                            value="{{session()->get('purchase-summary-report-end-date')}}" 
                            placeholder="To"  class="form-control">
                          </div>
                      
                    </div>

                    <div class="col-lg-4">
                        <div class="input-group input-group-outline">
                            <select name="vendor" id="" class="form-control">
                                <option value="">All Vendors</option>
                                @foreach ($vendors as $vendor)
                                <option value="{{$vendor->id}}" {{session()->get('purchase-summary-vendor') == $vendor->id ? 'selected' : ''}}>{{$vendor->party_name}}</option>
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
                <a href="{{url('/reports/purchase-report?vendor='.$item->id.'&start_date='.session()->get('purchase-summary-report-start-date').'&end_date='.session()->get('purchase-summary-report-end-date').'')}}" >{{$item->party_name}} </a>
            </td>
                    
                </tr>
                <tr>
                    <th>Purchase Invoices (Counts)</th>
                    <td>{{$item->purchases->count()}}</td>
                </tr>
                <tr>
                    <th>Gross Total</th>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '. round($item->purchases->sum('total'))}}</td>
                </tr>
                <tr>
                    <th>Net Total</th>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '. round($item->purchases->sum('net_amount'))}}</td>
                </tr>
                <tr>
                    <th>Paid (Total)</th>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '. round($item->purchases->sum('recieved'))}}</td>
                </tr>
                <tr>
                    <th>Balance (Total)</th>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '.round($item->purchases->sum('net_amount'))- round($item->purchases->sum('recieved'))}}</td>
                </tr>
           
        </tbody>
    </table>
    @endforeach
    <div class="d-flex justify-content-center">
      
    </div>
</div>
</div>



@endsection