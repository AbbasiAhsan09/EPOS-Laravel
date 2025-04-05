@if ($data)
{{-- @dump($data->purchase ?? "")
@dump($data->reference_type ?? "") --}}
    @if ($data && isset($data->sale) && $data->reference_type === 'sales_order')
        <p>Sale# {{ $data->sale->tran_no }} - {{$data->sale->condition ? "On condition ".$data->sale->condition : ''}}  
            {{ $data->sale->gp_no ? ' - GP : '. $data->sale->gp_no : "" }}</p>
        {{-- @if ($data->sale && count($data->sale->order_details))
            @foreach ($data->sale->order_details as $detail)
                <p>{{$detail->item_details->fullProductName ?? ""}} - W {{$detail->qty}} {{ '@'.ConfigHelper::getStoreConfig()["symbol"].$detail->rate }}</p>
            @endforeach
        @endif --}}
        
    @endif

            {{-- if sale return --}}
    @if ($data && isset($data->sale_return) && $data->reference_type === 'sales_return')
        <p>Sale Return# {{ $data->sale_return->doc_no }}  Dated {{date('d/m/Y',strtotime($data->sale_return->return_date))}} </p>
        @if ($data->sale_return && count($data->sale_return->order_details))
            @foreach ($data->sale_return->order_details as $detail)
                <p>{{$detail->item_details->fullProductName ?? ""}} - W {{$detail->returned_qty}} {{ '@'.ConfigHelper::getStoreConfig()["symbol"].$detail->returned_rate }}</p>
            @endforeach
        @endif
        
    @endif

           {{-- if purchase return --}}
           @if ($data && isset($data->purchase_return) && $data->reference_type === 'purchase_return')
           <p>Purchase Return# {{ $data->purchase_return->doc_no }}  Dated {{date('d/m/Y',strtotime($data->purchase_return->return_date))}} </p>
           @if ($data->purchase_return && count($data->purchase_return->order_details))
               @foreach ($data->purchase_return->order_details as $detail)
                   <p>{{$detail->item_details->fullProductName ?? ""}} - W {{$detail->returned_qty}} {{ '@'.ConfigHelper::getStoreConfig()["symbol"].$detail->returned_rate }}</p>
               @endforeach
           @endif
           
       @endif

    @if ($data && isset($data->purchase) && $data->reference_type === 'purchase_invoice')
        <p>Purchase#{{ $data->purchase->doc_num }} - {{$data->purchase->condition ? "On condition ".$data->purchase->condition : ''}} Dated {{date('d/m/Y',strtotime($data->purchase->bill_date ? $data->purchase->bill_date : $data->purchase->created_at))}} 
            {{ $data->purchase->gp_no ? ' - GP : '. $data->purchase->gp_no : "" }}</p>
        @if ($data->purchase && count($data->purchase->details))
            @foreach ($data->purchase->details as $detail)
                <p>{{$detail->items->fullProductName ?? ""}} - W {{$detail->qty}} {{ '@'.ConfigHelper::getStoreConfig()["symbol"].$detail->rate }}</p>
            @endforeach
        @endif
        
    @endif

    



    @if ($data && $data->note && (
        !(isset($data->sale) && $data->reference_type === 'sales_order') && 
        !(isset($data->purchase) && $data->reference_type === 'purchase_invoice') &&
        !(isset($data->sale_return) && $data->reference_type === 'sales_return') &&
        !(isset($data->purchase_return) && $data->reference_type === 'purchase_return')
        ))
        {{$data->note}}
        {{ $data->source_account_detail->title ? ", " . $data->source_account_detail->title : "" }}
    @endif
    
@endif