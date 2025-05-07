@extends("reports.layout")
@section("report_content")
@php
    $config = ConfigHelper::getStoreConfig();
@endphp
@foreach ($records as $record)
<table class="m-2 inv-table" width="100%" border="1" cellspacing="0" cellpadding="5" style="margin-bottom: 10px ">
    <thead >
        <td>Doc# {{ $record->tran_no }}</td>
        <td>Date : {{ $record->bill_date ? date('d/m/Y', strtotime($record->bill_date)) : date('d/m/Y', strtotime($record->created_at))   }}</td>
        <td colspan="2">Party : {{ $record->customer->party_name ?? "" }}</td>
        <td>GP : {{ $record->gp_no ? $record->gp_no : "N/A" }}</td>
        <td>Truck No : {{ $record->truck_no ? $record->truck_no : "N/A" }}</td>
       
    </thead>
    
        <tr>
            <td>Product</td>
            <td>Qty</td>
            <td>Rate</td>
            <td>Disc</td>
            <td>Tax</td>
            <td>Total</td>
        </tr>
        @foreach ($record->order_details as $item)
            <tr>
                <td>{{ $item->item_details->name ?? "" }}</td>
                <td>{{ $item->qty ?? "0" }} {{ $item->unit ? $item->unit->symbol : '' }}</td>
                <td>{{$item->rate}}</td>
                <td>%{{ $item->disc ?? 0 }}</td>
                <td>%{{ $item->tax ?? 0 }}</td>
                <td>{{$config['symbol'].number_format($item->total,2) ?? 0 }}</td>
            </tr>
        @endforeach
    <tr>
        <td>Line Items ({{ count($record->order_details) }})</td>
        <td></td>
        <td>Gross Total : {{ $config['symbol'].number_format($record->gross_total ?? 0, 2) }}</td>
        <td>Discount : {{$record->discount_type == 'PERCENT' ? '% -'.number_format($record->discount,2) : $config['symbol'].' -'.number_format($record->discount,2)}}</td>
        <td>Other Charges : {{ $config['symbol'].number_format($record->other_charges, 2) }}</td>
        <td>Net Total : {{ $config['symbol'].number_format($record->net_total ?? 0, 2) }}</td>
    </tr>
    <tbody>
      
    </tbody>
  
</table>
<hr>
@endforeach


<style>
    table td, table td {
        font-size: 10px !important;
    }
    /* table{
        width: 100% ;
    }
    
    table, td, td {
  border: 1px solid gray;
  border-collapse: collapse;
} */

    .dates{
        float: right
    }
</style>
@endsection
