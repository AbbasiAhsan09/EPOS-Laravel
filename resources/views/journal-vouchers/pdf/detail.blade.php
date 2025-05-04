@extends('reports.layout')
@section("report_content")
<table>
    <tr>
        <th>Doc # : {{$voucher->doc_no}}</th>
        <th>Account : {{$voucher->account->title}}</th>
        <th>Date : {{date("d/m/Y",strtotime($voucher->date))}}</th>
    </tr>
    
</table>
<table>
    <thead>
        <th width="100px">Reference No.</th>
        <th>Account</th>
        <th>Description</th>
        <th>Debit</th>
        <th>Credit</th>
        <th>Mode</th>
    </thead>
    <tbody>
        @if ($voucher->entries && count($voucher->entries))
        @foreach ($voucher->entries as $item)
        <tr>
            <td>{{$item->reference_no}}</td>
            <td>{{$item->account->title}}</td>
            <td>{{$item->description}}</td>
            <td>{{$item->debit ? ConfigHelper::getStoreConfig()["symbol"].number_format($item->debit,2) : '-'}}</td>
            <td>{{$item->credit ? ConfigHelper::getStoreConfig()["symbol"].number_format($item->credit,2) : '-'}}</td>
            <td>{{$item->mode ? ucfirst($item->mode) : '-'}}</td>
        </tr>
        @endforeach
        @endif
        
    </tbody>
    <tr>
        <td colspan="3">Total : ({{$voucher->entries->count()}})</td>
        <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($voucher->entries->sum("debit"),2)}}</td>
        <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($voucher->entries->sum("credit"),2)}}</td>
        <td></td>
    </tr>
</table>
@endsection