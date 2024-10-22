@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-3">
            <h2>Customer Payments</h2>
        </div>
        <div class="col-lg-7">
            <div class="row">
                <div class="col-lg-3">
                    
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered table-striped" border="2">
        <thead>
            <th>Date</th>
            <th>Customer</th>
            <th>Recieved Account</th>
            <th>Description</th>
            <th>Amount</th>
        </thead>
        <tbody>
            @foreach ($data as $item)
            <tr>
                <td>{{$item->transaction_date ? date("m-d-Y",strtotime($item->transaction_date)) : 'N/A'}}</td>
                <td>
                    {{$item->account->title ?? "N/A"}}
                </td>
                <td>{{$item->source_account_detail->title ?? "N/A"}}</td>
                <td>{{$item->note && !empty($item->note) ? $item->note : "-"}}</td>
                <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($item->credit,2)}}</td>
            </tr>    
            @endforeach
            
        </tbody>
    </table>
</div>
@endsection