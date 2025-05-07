@extends("reports.layout")
@section("report_content")
@php
    $config = ConfigHelper::getStoreConfig();
@endphp
@foreach ($records as $record)
<table class="m-2 inv-table" width="100%" border="1" cellspacing="0" cellpadding="5" style="margin-bottom: 10px ">
    <thead >
        <th>Doc# {{ $record->tran_no }}</th>
        <th>Date : {{ $record->bill_date ? date('d/m/Y', strtotime($record->bill_date)) : date('d/m/Y', strtotime($record->created_at))   }}</th>
        <th colspan="2">Party : {{ $record->customer->party_name ?? "" }}</th>
        <th>GP : {{ $record->gp_no ? $record->gp_no : "N/A" }}</th>
        <th>Truck No : {{ $record->truck_no ? $record->truck_no : "N/A" }}</th>
       
    </thead>
    
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Rate</th>
            <th>Disc</th>
            <th>Tax</th>
            <th>Total</th>
        </tr>
        @foreach ($record->order_details as $item)
            <tr>
                <td><strong>{{ $item->item_details->name ?? "" }}</strong></td>
                <td>{{ $item->qty ?? "0" }} {{ $item->unit ? $item->unit->symbol : '' }}</td>
                <td>{{$item->rate}}</td>
                <td>%{{ $item->disc ?? 0 }}</td>
                <td>%{{ $item->tax ?? 0 }}</td>
                <td>{{$config['symbol'].number_format($item->total,2) ?? 0 }}</td>
            </tr>
        @endforeach
    <tr>
        <th colspan="2">Line Items ({{ count($record->order_details) }})</th>
        <th>Gross Total : {{ $config['symbol'].number_format($record->gross_total ?? 0, 2) }}</th>
        <th>Discount : {{$record->discount_type == 'PERCENT' ? '% -'.number_format($record->discount,2) : $config['symbol'].' -'.number_format($record->discount,2)}}</th>
        <th>Other Charges : {{ $config['symbol'].number_format($record->other_charges, 2) }}</th>
        <th>Net Total : {{ $config['symbol'].number_format($record->net_total ?? 0, 2) }}</th>
    </tr>
    <tbody>
      
    </tbody>
  
</table>
@endforeach


<style>
    /* table{
        width: 100% ;
    }
    
    table, th, td {
  border: 1px solid gray;
  border-collapse: collapse;
} */

    .dates{
        float: right
    }
</style>
@endsection
