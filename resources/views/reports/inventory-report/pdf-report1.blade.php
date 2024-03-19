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
        <th>TP</th>
                <th>Available Cost</th>
        @php
            $total = 0;
        @endphp
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
            <td>{{$item->tp ?? "0"}}</td>
                        @php
                $total += (!empty($item->stock_qty) ? (round($item->stock_qty / (($item->base_unit_value) ?? 1))) : 0) * ($item->tp ?? 0);
            @endphp
            <td>{{ ConfigHelper::getStoreConfig()["symbol"].(!empty($item->stock_qty) ? (round($item->stock_qty / (($item->base_unit_value) ?? 1))) : 0) * ($item->tp ?? 0)}}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<h4>Total Cost : {{ConfigHelper::getStoreConfig()["symbol"].$total ?? 0}}</h4>
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