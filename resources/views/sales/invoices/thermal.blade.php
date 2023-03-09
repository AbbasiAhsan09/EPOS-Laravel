<title>Invoice</title>
<body>
    
    <div class="reciept-wrapper">
        <div class="receipt">
            <h5 class="nos">NTN : 41300303033  | STN : 41300303033</h5>
            <div class="brand">
                <img src="https://hibarnsley.com/wp-content/uploads/2017/06/dummy-logo.png" alt="Not Available" width="150px">
            </div>
            {{-- <h5 class="nos">INV# 41300303033  | STORE ID : 009</h5> --}}
            <p>Lorem ipsum dolor sit, amet consectetur adipisicing elit. Ad labore </p>
    
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
                    <td>{{$item->item_details->name}}</td>
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
                    <th>{{' '.$order->net_total}} </th>
                  </tr>
                </table>
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