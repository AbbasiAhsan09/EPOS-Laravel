@extends('reports.layout')
@section("report_content")

<table class="table table-sm table-responsive-sm table-striped ">
    <thead>
        <th>SID</th>
        <th>Doc #</th>
        <th>Date</th>
        {{-- <th>Field</th> --}}
        <th>Category</th>
        <th>Product</th>
        <!-- <th>Bag Size</th>
        <th>Bags</th> -->
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
                <td>{{$item->sale_id}}</td>
                <td>
                {{-- <a href="{{url('sales/edit/'.$item->sale_id)}}" class="text-primary"> --}}
                    {{$item->sale->tran_no ?? ''}}
                {{-- </a> --}}
                </td>
                {{-- <td>{{$item->item_details->categories->field->name ?? ""}}</td> --}}
                <td>{{date('d/m/Y', strtotime($item->created_at))}}</td>
                <td>{{$item->item_details->categories->category ?? ""}}</td>
                <td>{{$item->item_details->name ?? ""}}</td>
                <!-- <td>{{$item->bag_size ?? "-"}}</td>
                <td>{{$item->bags ?? "-"}}</td> -->
                <td>{{number_format($item->rate,2)}}</td>
                <td>%{{$item->tax}}</td>
                <td>%{{0}}</td>
                <td>{{number_format($item->qty,2)}} {{ $item->unit ? $item->unit->symbol : '' }}</td>
                <td>{{ $item->unit ? $item->unit->name : 'Single' }}</td>
                <td>{{number_format($item->total,2)}}</td>
            </tr>
        @endforeach
       <tfoot>
        <tr>
        <th colspan="10">Total</th>
        <th colspan="1">{{ConfigHelper::getStoreConfig()["symbol"].number_format($records->sum('total'),2)}}</th>
        </tr>
    </tfoot>
    </tbody>
</table>
@endsection