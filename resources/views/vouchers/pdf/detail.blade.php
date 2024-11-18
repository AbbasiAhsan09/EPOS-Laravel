@extends('reports.layout')
@section("report_content")
<table style="margin-top: 10px">
    {{-- @dump($voucher->toArray()) --}}
    <tr>
        <th>Doc # : {{$voucher->doc_no}}</th>
        <th>Date : {{date("d/m/Y",strtotime($voucher->date))}}</th>
        <th >Mode : {{$voucher->mode ? ucfirst($voucher->mode) : '-'}}</th>
       
    </tr>
    <tr>
        <th>Bank : {{$voucher->account->title}}</th>
        <th >Account : {{$voucher->account_from->title ?? "-"}}</th>
        <th>User : {{$voucher->user->name ?? '-'}}</th>
    </tr>
    
</table>
<hr>
<table>
    <thead>
        <th width="100px">Reference No.</th>
        {{-- <th>Account</th> --}}
        <th>Description</th>
        <th>Amount</th>
        {{-- <th>Debit</th> --}}
        {{-- <th>Mode</th> --}}
    </thead>
    
    <tbody>

        @if ($voucher->entries && count($voucher->entries))
        @foreach ($voucher->entries as $item)
        {{-- @dump($item->toArray()) --}}
        <tr>
            <td>{{$item->reference}}</td>
            {{-- <td>{{$item->account->title}}</td> --}}
            <td>{{$item->description}}</td>
            <td>{{$item->amount ? ConfigHelper::getStoreConfig()["symbol"].number_format($item->amount,2) : '-'}}</td>
            {{-- <td>{{$item->debit ? ConfigHelper::getStoreConfig()["symbol"].number_format($item->debit,2) : '-'}}</td> --}}
            {{-- <td>{{$item->mode ? ucfirst($item->mode) : '-'}}</td> --}}
        </tr>
        @endforeach
        @endif
        
    </tbody>
    <tr>
        <td colspan="2">Total : ({{$voucher->entries->count()}})</td>
        {{-- <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($voucher->entries->sum("credit"),2)}}</td> --}}
        <td><strong>{{ConfigHelper::getStoreConfig()["symbol"].number_format($voucher->entries->sum("amount"),2)}}</strong></td>
        {{-- <td></td> --}}
    </tr>
</table>

@if ($voucher->note)
    <hr>
    <p style="font-size: 12px">
        <strong>Note:</strong>
        {{$voucher->note}}
    </p>
@endif

<br>
@endsection