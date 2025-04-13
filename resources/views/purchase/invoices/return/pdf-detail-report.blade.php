@extends('reports.layout')
@section('report_content')
<table class="table table-sm table-responsive-sm table-striped ">
        <thead>
            <th>PR.ID</th>
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
                    <td>{{$item->purchase_return_id}}</td>
                    <td>{{$item->return->doc_no ?? ""}}</td>
                        <td>{{date('d/m/Y', strtotime($item->return->return_date))}}</td>
                    <td>{{$item->item_details->categories->category ?? ""}}</td>
                    <td>{{$item->item_details->name ?? ""}}</td>
                    <!-- <td>{{$item->bag_size ?? "-"}}</td>
                    <td>{{$item->bags ?? "-"}}</td> -->
                    <td>{{$item->returned_rate}}</td>
                    <td>%{{$item->returned_tax}}</td>
                    <td>%{{$item->returned_disc}}</td>
                    <td>{{$item->returned_qty}} {{ $item->unit ? $item->unit->symbol : '' }}</td>
                    <td>{{ $item->unit ? $item->unit->name : 'Single' }}</td>
                    <td>{{$item->returned_total}}</td>
                </tr>
            @endforeach
        
        </tbody>
        <tfoot>
            <th colspan="8">Total</th>
            <th colspan="1">{{$records->sum('returned_qty')}}</th>
            <th></th>
            <th colspan="1">{{number_format($records->sum('returned_total'),2)}}</th>

        </tfoot>
    </table>
@endsection