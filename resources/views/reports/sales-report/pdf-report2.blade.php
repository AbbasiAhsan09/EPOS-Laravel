<title>Sale Detail Report</title>
@include('reports.header')
<h2>
    Sale Detail Report
        @if ((isset($from) && isset($to)) && (!empty($from) && !empty($to)))
        <span class="dates">
        From: {{isset($from) ? date('d-m-y', strtotime($from)) : ''}} To: {{isset($to) ? date('d-m-y', strtotime($to)) : ''}}
    </span>
        @endif
    
</h2>
<table class="table table-sm table-responsive-sm table-striped ">
    <thead>
        <th>Inv ID</th>
        <th>Doc #</th>
        <th>Field</th>
        <th>Category</th>
        <th>Product</th>
        <th>Rate</th>
        <th>Tax</th>
        <th>Disc</th>
        <th>Qty</th>
        <th>Unit</th>
        <th>Total</th>
        <th>Date</th>
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
                <td>{{$item->item_details->categories->field->name ?? ""}}</td>
                <td>{{$item->item_details->categories->category ?? ""}}</td>
                <td>{{$item->item_details->name ?? ""}}</td>
                <td>{{$item->rate}}</td>
                <td>%{{$item->tax}}</td>
                <td>%{{0}}</td>
                <td>{{$item->qty}}</td>
                <td>{{$item->item_details->uom ? $item->item_details->uoms->base_unit :( isset($item->item_details->uoms->uom) ? $item->item_details->uoms->uom : 'Default') }}</td>
                <td>{{$item->total}}</td>
                <td>{{date('m-d-y', strtotime($item->created_at))}}</td>
            </tr>
        @endforeach
       <tfoot>
        <tr>
        <th colspan="10">Total</th>
        <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].round($records->sum('total'))}}</th>
        </tr>
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