<title>Purchase Return  Report</title>
@include('reports.header')
<h2>
    Purchase Return 
    {{-- @if ((isset($from) && isset($to)) && (!empty($from) && !empty($to)))
    <span class="dates">
        From: {{isset($from) ? date('d-m-y', strtotime($from)) : ''}} To: {{isset($to) ? date('d-m-y', strtotime($to)) : ''}}
    </span>
    @endif --}}

</h2>
<table border="1">
    <thead>
        <th>S#</th>
        <th>Transaction #</th>
        <th>Party</th>
        <th>Created at</th>
        <th>User</th>
        <th>Net Amount</th>
      
    </thead>
    <tbody>
       @foreach ($records as $key => $item)
                <tr >
                <td>{{$key + 1}}</td>
                <td >{{$item->doc_no}}</td>
                <td>{{isset($item->party) ? $item->party->party_name : 'Cash'}}</td>    
                <td>{{date('d-M-y | h:m' , strtotime($item->created_at))}}</td>
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