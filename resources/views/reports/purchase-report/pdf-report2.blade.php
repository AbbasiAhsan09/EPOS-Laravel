<title>Purchase Detail Report</title>
@include('reports.header')
<h2>
    Purchase Detail Report
        @if ((isset($from) && isset($to)) && (!empty($from) && !empty($to)))
        <span class="dates">
        From: {{isset($from) ? date('d-m-y', strtotime($from)) : ''}} To: {{isset($to) ? date('d-m-y', strtotime($to)) : ''}}
    </span>
        @endif
    
</h2>
<table class="table table-sm table-responsive-sm table-striped " border="1">
    <thead>
        <th>Inv ID</th>
        <th>Doc #</th>
        <th>Date</th>
        {{-- <th>Field</th> --}}
        <th>Category</th>
        <th>Product</th>
        <th>Bag Size</th>
        <th>Bags</th>
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
                <td>{{date('m-d-y', strtotime($item->created_at))}}</td>
                {{-- <td>{{$item->items->categories->field->name}}</td> --}}
                <td>{{$item->items->categories->category}}</td>
                <td>{{$item->items->name}}</td>
                <td>{{$item->bag_size ?? "-"}}</td>
                <td>{{$item->bags ?? "-"}}</td>
                <td>{{$item->rate}}</td>
                <td>%{{$item->tax}}</td>
                <td>%{{0}}</td>
                <td>{{$item->qty}}</td>
                <td>{{$item->items->uom ? $item->items->uoms->base_unit :( isset($item->items->uoms->uom) ? $item->items->uoms->uom : 'Default') }}</td>
                <td>{{$item->total}}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <th colspan="10">Total</th>
        <th colspan="1">{{$records->sum('qty')}}</th>
        <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].number_format($records->sum('total'),2)}}</th>
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