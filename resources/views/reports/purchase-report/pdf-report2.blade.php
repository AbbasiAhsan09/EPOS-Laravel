@extends('reports.layout')
@section('report_content')
@php
$config = ConfigHelper::getStoreConfig();
@endphp
<table class="table table-sm table-responsive-sm table-striped " border="1">
    <thead>
        <th>PID</th>
        <th>Doc #</th>
        <th>Date</th>
        {{-- <th>Field</th> --}}
        <th>Category</th>
        <th>Product</th>
        @if ($config && $config['show_bag_sizing']) 
         <th>Bag Size</th>
        <th>Bags</th>
        @endif
        <th>Rate</th>
        <th>Tax</th>
        <th>Disc</th>
        <th>Qty</th>
        <th>Unit</th>
        <th>Total</th>
    </thead>
    <tbody>
        @foreach ($records as $item)
            <tr>
                <td>{{$item->inv_id}}</td>
                <td>{{$item->invoice->doc_num}}</td>
                <td>{{date('d/m/Y', strtotime($item->created_at))}}</td>
                {{-- <td>{{$item->items->categories->field->name}}</td> --}}
                <td>{{$item->items->categories->category}}</td>
                <td>{{$item->items->name}}</td>
        @if ($config && $config['show_bag_sizing']) 

                <td>{{$item->bag_size ?? "-"}}</td>
                <td>{{$item->bags ?? "-"}}</td>
        @endif
                <td>{{$item->rate}}</td>
                <td>%{{$item->tax}}</td>
                <td>%{{0}}</td>
                <td>{{$item->qty}} {{ $item->unit ? $item->unit->symbol : '' }}</td>
                <td>{{ $item->unit ? $item->unit->name : 'Single' }}</td>
                <td>{{number_format($item->total,2)}}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <th colspan="8">Total</th>
        <th >{{$records->sum('qty')}}</th>
        <th></th>
        <th >{{ConfigHelper::getStoreConfig()["symbol"].number_format($records->sum('total'),2)}}</th>
    </tfoot>
</table>
@endsection