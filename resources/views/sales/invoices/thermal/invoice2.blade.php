<head>
<title>Invoice</title>
</head>
<body style="width: fit-content">
    
    <div class="reciept-wrapper" id="receiptMain">
        <div class="receipt">
            <h5 class="nos">{{isset($config) && $config->show_ntn ? 'NTN#'.$config->ntn.' | ' : ''}}
                {{isset($config) && $config->show_ptn ? 'PTN#'.$config->ptn : ''}}</h5>
            <div class="brand">
            @if (isset($config) && $config->logo)
            <img src="{{asset("images/logo/$config->logo")}}" alt="Not Available" width="150px">
            @else
                {{isset($config) ? $config->app_title : 'Demo'}}
            @endif    
            </div>
            {{-- <h5 class="nos">INV# 41300303033  | STORE ID : 009</h5> --}}
            <p>{{isset($config) ? $config->address : ''}}</p>
    
            <div class="inv_details">
    
                <table>
                    <tr>
                        <th  colspan="2" class="tb-header">INVOICE DETAILS</th>
                    </tr>
                    <tr>
                        <th>INV# {{isset($order) ? $order->id : ''}}</th> 
                        <th>CASHIER: {{isset($order->user) ? $order->user->name : ''}}</th>
                    </tr>
                    <tr>
                        <th>STORE ID: 00009</th> 
                        <th>DATE: {{date('d.m.y h:m:A',strtotime($order->created_at))}}</th>
                    </tr>
                    <tr>
                        <th  colspan="2" class="tb-header">CUSTOMER INFORMATION</th>
                    </tr>
                    <tr>
                        <th>CUSTOMER: {{isset($order->customer ) ? $order->customer->name : '' }}</th> 
                        <th>CUSTOMER ID: {{isset($order->customer ) ? $order->customer->id : '' }}</th>
                    </tr>
                </table>
                <table>
                    <tr class="tb-header-sm">
                        <th>#</th>
                        <th>Description</th>
                        <th>Rate</th>
                        <th>Tax</th>
                        <th>Disc</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                  @foreach ($order->order_details as $key => $item)
                <tr class="items-table">
                    <td>{{$key+1}}</td>
                    <td>{{$item->item_details->name ?? ''}}</td>
                    <td>{{$item->rate}}</td>
                    <td>{{$item->tax}}</td>
                    <td>{{$item->disc}}</td>
                    <td>{{$item->qty}}</td>
                    <td>{{$item->total}}</td>
                </tr>
    
                  @endforeach
                  <tr class="foot-table">
                    <th colspan="6">Gross Total: </th>
                    <th>{{$order->gross_total}} </th>
                  </tr>
                  <tr class="foot-table">
                    <th colspan="6">Discount: </th>
    
                    <th>{{$order->discount_type == 'FLAT' ? $order->discount : "%".$order->discount}}</th>
                  </tr>
                  <tr class="foot-table">
                    <th colspan="6">Net Total: </th>
                    <th style="font-size: 15px">{{' '.$order->net_total}} </th>
                  </tr>
                </table>

                <div class="inv_messages" style="margin-top: 10px">
                    {!!isset($config) ? $config->invoice_message : ''!!}
                </div>

                <p>This Software is Developed By DevDox Solution | {{isset($config)  ? $config->dev_contact : ''}}</p>
            </div>
        </div>
    </div>
    
</body>

<style>
    @font-face {
    font-family: receipt-font;
    src: url('../fonts/reciept/FakeReceipt-Regular.woff2') format('woff2'),
        url('../fonts/reciept/FakeReceipt-Regular.woff') format('woff');
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}
*{
    font-size: 12px;
    font-family: receipt-font;
    text-align: center;

}


table th{
    text-align: left;
    padding: 5px 0
}
.tb-header{
    background: black;
    color: white;
    padding:7px 0;
    text-align: center

}
.items-table td {
    border-bottom: solid 1px gray
}

    .reciept-wrapper{
        width: 350px;
    border-radius: 10px;
        margin: auto;
        box-shadow: 5px 5px 15px gray;
        padding: 10px;
    }
    table{
        width: 100%
    }
    .brand{
        margin:  15px 0
    }

    tr.tb-header-sm th{
        background: black;
        font-size: 10px;
        text-align: center;
        color: white
    }
    .foot-table th:nth-child(1){
        text-align: right;
        padding-right: 10px;
        /* border-right: 1px solid */
    }
    
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
  // print the window when a button is clicked
    // enable automatic printing in Chrome
    var printSettings = {
      'mediaSize': { 'name': 'na_legal', 'height_microns': 355.6, 'width_microns': 215.9, 'custom_display_name': 'Legal' },
      'shouldPrintBackgrounds': true
    };
    if (navigator.userAgent.indexOf('Chrome') !== -1) {
      printSettings['autoprint'] = true;
    }
    // print the window
    window.print();
    setTimeout(() => {
        window.close();
    }, 1000);
});
</script>