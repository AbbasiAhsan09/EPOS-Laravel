<title>Purchase Report</title>
@include('reports.header')
<h2>
    Purchase Report {{request()->filter_deleted == 'true' ? '(Deleted)' : ''}}
    @if ((isset($from) && isset($to)) && (!empty($from) && !empty($to)))
    <span class="dates">
        From: {{isset($from) ? date('d-m-y', strtotime($from)) : ''}} To: {{isset($to) ? date('d-m-y', strtotime($to)) : ''}}
    </span>
    @endif

</h2>
<table border="1">
    <thead>
        <th>S#</th>
        <th>Doc #</th>
        <th>Party</th>
        {{-- <th>Gross Total</th> --}}
        <th>Net Total</th>
        {{-- <th>Paid</th> --}}
        {{-- <th>Balance</th> --}}
        <th>User</th>
        <th>Date</th>
    </thead>
    <tbody>
        @foreach ($records as $key => $item)
        <tr>
            <td>{{$key+1}}</td>
            <td>{{$item->doc_num}}</td>
            <td>{{$item->party->party_name}}</td>
            {{-- <td>{{$item->total}}</td> --}}
            <td>{{$item->net_amount}}</td>
            {{-- <td>{{$item->recieved}}</td> --}}
            {{-- <td>{{($item->net_amount - $item->recieved) > 0.99 ? round($item->net_amount - $item->recieved) : 0}}</td> --}}
            <td>{{$item->created_by_user->name}}</td>
            <td>{{date('m-d-y', strtotime($item->created_at))}}</td>
        </tr>
        @endforeach
        <tr>
            <th colspan="3">
                Total:
            </th>
            <th>{{ConfigHelper::getStoreConfig()["symbol"] . $records->sum('total')}}</th>
            {{-- <th>{{ConfigHelper::getStoreConfig()["symbol"] . $records->sum('net_amount')}}</th> --}}
            {{-- <th>{{ConfigHelper::getStoreConfig()["symbol"] . $records->sum('recieved')}}</th> --}}
            {{-- <th>{{ConfigHelper::getStoreConfig()["symbol"] . $records->sum('net_amount') - $records->sum('recieved')}}</th> --}}
            <th colspan="2">Count : ({{$records->count()}})</th>
        </tr>
    </tbody>
</table>
<style>
    table{
        width: 100% ;
    }
    
    table, th, td {
  border: 1px solid gray;
  border-collapse: collapse;
}

    .dates{
        float: right
    }
</style>