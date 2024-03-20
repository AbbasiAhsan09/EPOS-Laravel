<title>Purchase Detail Report</title>
@include('reports.header')
<h2>
    Purchase Summary
        @if ((isset($from) && isset($to)) && (!empty($from) && !empty($to)))
        <span class="dates">
        From: {{isset($from) ? date('d-m-y', strtotime($from)) : ''}} To: {{isset($to) ? date('d-m-y', strtotime($to)) : ''}}
    </span>
        @endif
    
</h2>
@foreach ($records as $item)
    <table class="table  table-responsive-sm table-bordered "  border="1">
        <tr class="bg-dark text-light">
            <th colspan="2">Summary 
            </th>
        </tr>
        <tbody>
           
                <tr>
                    <td >Party Name: </td>
                    <td>{{$item->party_name}} </td>
                </tr>
                <tr>
                    <td>Purchase Invoices (Counts)</td>
                    <td>{{$item->purchases->count()}}</td>
                </tr>
                <tr>
                    <td>Gross Total</td>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '. round($item->purchases->sum('total'))}}</td>
                </tr>
                <tr>
                    <td>Net Total</td>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '. round($item->purchases->sum('net_amount'))}}</td>
                </tr>
                <tr>
                    <td>Paid (Total)</td>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '. round($item->purchases->sum('recieved'))}}</td>
                </tr>
                <tr>
                    <td>Balance (Total)</td>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '.round($item->purchases->sum('net_amount'))- round($item->purchases->sum('recieved'))}}</td>
                </tr>
           
        </tbody>
    </table>
    <br>
    @endforeach
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