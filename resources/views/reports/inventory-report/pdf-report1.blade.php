@extends('reports.layout')
@section('report_content')
    

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
    <tfoot>
        <tr>
        <th colspan="7">Total</th>
        <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].$total ?? 0}}</th>
        </tr>
    </tfoot>
</table>

@endsection
