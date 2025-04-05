@extends('reports.layout')
@section('report_content')
<table class="table table-sm table-responsive-sm table-striped">
    <thead>
        <th>S#</th>
        <th>PID</th>
        <th>Field</th>
        <th>Category</th>
        <th>Product</th>
        <th>Opening Stock</th>
        <th>Purchased Stock</th>
        <th>Sold Stock</th>
        <th>Available Stock</th>
        <th>Avg Cost</th>
        <th>Total Cost</th>
        @php
            $total = 0;
        @endphp
    </thead>
    <tbody>
       @foreach ($results as $key => $item)
                <tr >
                    <td>{{$key + 1}}</td>
                    <td>{{$item->item_id}}</td>
                    <td>{{$item->field}}</td>
                    <td>{{$item->category}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{number_format(($item->opening_qty),2)}}</td>
                    <td>{{number_format(($item->purchased_qty) - ($item->purchase_return_qty),2)}}</td>
                    <td>{{number_format(($item->sold_qty) - ($item->sold_returned_qty),2)}}</td>
                    <td>{{number_format(($item->avl_qty),2)}}</td>
                    <td>{{number_format(($item->avg_rate),2)}}</td>
                    <td>{{number_format(($item->avg_rate) * ($item->avl_qty) ,2)}}</td>
                </tr>
                @php
                    $total += ($item->avg_rate) * ($item->avl_qty)
                @endphp
    @endforeach
           
    <tfoot>
        <th colspan="11">Total Available Inventory Cost : {{ConfigHelper::getStoreConfig()["symbol"].number_format($total,2)}}</th>
    </tfoot>
    <tfoot>
        <th colspan="11">Stock unit is Weight</th>
    </tfoot>
    </tbody>   
</table>
@endsection