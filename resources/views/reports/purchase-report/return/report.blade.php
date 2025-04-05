@extends('reports.layout')
@section('report_content')
<table border="1">
    <thead>
        <th>S#</th>
        <th>Doc #</th>
        <th>Date</th>
        <th>Party</th>
        <th>User</th>
        <th>Net Amount</th>
      
    </thead>
    <tbody>
       @foreach ($records as $key => $item)
       {{-- @dump($item) --}}
                <tr >
                <td>{{$key + 1}}</td>
                <td >{{$item->doc_no}}</td>
                <td>{{date('d/m/Y' , strtotime($item->return_date))}}</td>
                <td>{{isset($item->party) ? $item->party->party_name : 'Cash'}}</td>    
                <td>{{$item->user->name}}</td>   
                <td> {{ConfigHelper::getStoreConfig()["symbol"].number_format($item->net_total,2)}}</td>
                </tr>
                @endforeach
    </tbody>
    <tfoot>
        <th colspan="5">Total</th>
        <th>{{ConfigHelper::getStoreConfig()["symbol"].number_format($records->sum("net_total"),2)}}</th>
      </tfoot>
</table>
@endsection
