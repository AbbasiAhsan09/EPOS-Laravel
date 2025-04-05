@extends('reports.layout')
@section('report_content')
    
<table border="1">
    <thead>
        <th>S#</th>
        <th>Doc #</th>
        <th>Date</th>
        <th>Party</th>
        {{-- <th>Gross Total</th> --}}
        {{-- <th>Paid</th> --}}
        {{-- <th>Balance</th> --}}
        <th>User</th>
        <th>Net Total</th>
    </thead>
    <tbody>
        @foreach ($records as $key => $item)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$item->doc_num}}</td>
            <td>{{date('d/m/Y', strtotime($item->created_at))}}</td>
            <td>{{$item->party->party_name}}</td>
            {{-- <td>{{$item->total}}</td> --}}
            {{-- <td>{{$item->recieved}}</td> --}}
            {{-- <td>{{($item->net_amount - $item->recieved) > 0.99 ? round($item->net_amount - $item->recieved) : 0}}</td> --}}
            <td>{{$item->created_by_user->name}}</td>
            <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($item->net_amount,2)}}</td>
        </tr>
        @endforeach
        <tr>
            <th colspan="5">
                Total:  ({{$records->count()}})
            </th>
            <th>{{ConfigHelper::getStoreConfig()["symbol"] . number_format($records->sum('total'),2)}}</th>
            {{-- <th>{{ConfigHelper::getStoreConfig()["symbol"] . $records->sum('net_amount')}}</th> --}}
            {{-- <th>{{ConfigHelper::getStoreConfig()["symbol"] . $records->sum('recieved')}}</th> --}}
            {{-- <th>{{ConfigHelper::getStoreConfig()["symbol"] . $records->sum('net_amount') - $records->sum('recieved')}}</th> --}}
        </tr>
    </tbody>
</table>

@endsection
