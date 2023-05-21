<title>Inventory Report</title>
@include('reports.header')
<h2>
    Inventory Report {{request()->filter_deleted == 'true' ? '(Deleted)' : ''}}
    @if ((isset($from) && isset($to)) && (!empty($from) && !empty($to)))
    <span class="dates">
        From: {{isset($from) ? date('d-m-y', strtotime($from)) : ''}} To: {{isset($to) ? date('d-m-y', strtotime($to)) : ''}}
    </span>
    @endif

</h2>
<table border="1">
    <thead>
        <th>Field</th>
        <th>Category</th>
        <th>Product</th>
        <th>Available Stock (Base units)</th>
        <th>Available Stock (Units)</th>
        <th>Base Unit Value</th>
        <th>Stock Alert</th>
    </thead>
    <tbody>
       @foreach ($records as $key => $item)
                <tr >
                    <td>{{$item->field}}</td>
                    <td>{{$item->category}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{(!empty($item->stock_qty) ? ($item->stock_qty) : 0).' '.$item->base_unit}}</td>
                    <td>{{(!empty($item->stock_qty) ? (round($item->stock_qty / (($item->base_unit_value) ?? 1))) : 0).' '.$item->uom}}</td>
                    <td>{{($item->uom) ? '1 '.($item->uom).' = '.$item->base_unit_value.' '.$item->base_unit : ''}}</td>
                    <td>{{$item->low_stock}}</td>
                </tr>
                @endforeach
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