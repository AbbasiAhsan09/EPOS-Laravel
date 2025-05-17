
<title>Labour Bill Report</title>
<h3>Labour Bill Report</h3>
<table class="table table-sm-responsive table-bordered table-striped mt-2" border="2">
    <thead>
        <th>Status</th>
        <th>Doc #</th>
        <th>Labour</th>
        <th>Open Date</th>
        <th>Close Date</th>
        <th>Created Date</th>
        <th>Net Total</th>
    </thead>
    <tbody>
        @foreach ($items as $item)
            <tr style="{{!empty($item->end_date) ? 'background: rgb(233, 187, 187); color : black;' : ''}} {{$item->is_paid ? 'background : #6ffc76; color : black !important;' : ''}}">
                <td>
                    @if (!empty($item->end_date))
                    <span class="badge badge-sm bg-gradient-danger">Closed</span>
                    @else
                    <span class="badge badge-sm bg-gradient-success">Open</span>
                    @endif

                    @if($item->is_paid)
                    (Paid)
                    @endif
                </td>
                <td>{{$item->doc_no ?? ""}}</td>
                <td>{{$item->labour->name ?? ""}}</td>
                <td>{{date("d/m/Y", strtotime($item->start_date))}}</td>
                <td>{{$item->end_date ? date("d/m/Y", strtotime($item->end_date)) : '-'}}</td>
                <td>{{date("d/m/Y", strtotime($item->created_at))}}</td>
                <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($item->net_total,2)}}</td>
           
            </tr>
        @endforeach
        <tfoot>
            <th colspan="5">Grand Total</th>
            <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].number_format($items->sum("net_total"),2)}}</th>
        </tfoot>
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