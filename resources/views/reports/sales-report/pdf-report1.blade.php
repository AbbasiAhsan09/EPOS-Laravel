<title>Sales Report</title>
@include('reports.header')
<h2>
    Sales Report {{request()->filter_deleted == 'true' ? '(Deleted)' : ''}}
    @if ((isset($from) && isset($to)) && (!empty($from) && !empty($to)))
    <span class="dates">
        From: {{isset($from) ? date('d-m-y', strtotime($from)) : ''}} To: {{isset($to) ? date('d-m-y', strtotime($to)) : ''}}
    </span>
    @endif

</h2>
<table border="1">
    <thead>
        <th>ID</th>
        <th>Doc #</th>
        <th>Customer</th>
        <th>Created at</th>
        <th>User</th>
        <th>Net Amount</th>
        {{-- <th>Recieved</th> --}}
        {{-- <th>Balance</th> --}}
      
    </thead>
    <tbody>
       @foreach ($records as $key => $item)
                <tr >
                <td>{{$item->id}}</td>
                <td  class="{{$item->deleted_at !== null ? 'text-danger' : ''}}">{{$item->tran_no}}</td>
                <td>{{isset($item->customer) ? $item->customer->party_name : 'Cash'}}</td>    
                <td>{{date('d-M-y | h:m' , strtotime($item->created_at))}}</td>
                <td>{{$item->user->name}}</td>   
                <td> {{ConfigHelper::getStoreConfig()["symbol"].$item->net_total}}</td>
                {{-- <td>{{ConfigHelper::getStoreConfig()["symbol"]. $item->recieved}}</td>  --}}
                {{-- <td>{{ConfigHelper::getStoreConfig()["symbol"]. (round($item->net_total - $item->recieved)) }}</td>  --}}
                </tr>
                @endforeach
    </tbody>
    <tfoot>
        <th colspan="5">Total</th>
        <th>{{ConfigHelper::getStoreConfig()["symbol"].number_format($records->sum("net_total"),2)}}</th>
      </tfoot>
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