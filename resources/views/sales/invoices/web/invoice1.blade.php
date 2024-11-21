                      

<style>
  .receipt-content .logo a:hover {
text-decoration: none;
/* color: #7793C4;  */
}

.receipt-content .invoice-wrapper {
background: white !important;
border: 1px solid #CDD3E2;
box-shadow: 0px 0px 1px #CCC;
padding: 20px;
margin-top: 20px;
border-radius: 4px; 
}

.receipt-content .invoice-wrapper .payment-details span {
color: #000000;
display: block; 
}
.receipt-content .invoice-wrapper .payment-details a {
display: inline-block;
margin-top: 5px; 
}

.receipt-content .invoice-wrapper .line-items .print a {
display: inline-block;
border: 1px solid #9CB5D6;
padding: 13px 13px;
border-radius: 5px;
color: #708DC0;
font-size: 13px;
-webkit-transition: all 0.2s linear;
-moz-transition: all 0.2s linear;
-ms-transition: all 0.2s linear;
-o-transition: all 0.2s linear;
transition: all 0.2s linear; 
}

.receipt-content .invoice-wrapper .line-items .print a:hover {
text-decoration: none;
border-color: #333;
color: #333; 
}

.receipt-content {
background: #ECEEF4; 
}
@media (min-width: 1200px) {
.receipt-content .container {width: 900px; } 
}

.receipt-content .logo {
text-align: center;
margin-top: 50px; 
}

.receipt-content .logo a {
font-family: Myriad Pro, Lato, Helvetica Neue, Arial;
font-size: 36px;
letter-spacing: .1px;
color: #555;
font-weight: 300;
-webkit-transition: all 0.2s linear;
-moz-transition: all 0.2s linear;
-ms-transition: all 0.2s linear;
-o-transition: all 0.2s linear;
transition: all 0.2s linear; 
}

.receipt-content .invoice-wrapper .intro {
line-height: 25px;
color: #444; 
}

.receipt-content .invoice-wrapper .payment-info {
margin-top: 25px;
padding-top: 15px; 
}

.receipt-content .invoice-wrapper .payment-info span {
color: #000000; 
}

.receipt-content .invoice-wrapper .payment-info strong {
display: block;
color: #000000;
margin-top: 3px; 
}

@media (max-width: 767px) {
.receipt-content .invoice-wrapper .payment-info .text-right {
text-align: left;
margin-top: 20px; } 
}
.receipt-content .invoice-wrapper .payment-details {
border-top: 2px solid #EBECEE;
margin-top: 30px;
padding-top: 20px;
line-height: 22px; 
}


@media (max-width: 767px) {
.receipt-content .invoice-wrapper .payment-details .text-right {
text-align: left;
margin-top: 20px; } 
}
.receipt-content .invoice-wrapper .line-items {
margin-top: 10px; 
}
.receipt-content .invoice-wrapper .line-items .headers {
color: #000000;
font-size: 13px;
letter-spacing: .3px;
border-bottom: 2px solid #EBECEE;
padding-bottom: 4px; 
}
.receipt-content .invoice-wrapper .line-items .items {
margin-top: 8px;
border-bottom: 2px solid #000000;
padding-bottom: 8px; 
}
.receipt-content .invoice-wrapper .line-items .items .item {
padding: 10px 0;
color: #696969;
font-size: 15px; 
}
@media (max-width: 767px) {
.receipt-content .invoice-wrapper .line-items .items .item {
font-size: 13px; } 
}
.receipt-content .invoice-wrapper .line-items .items .item .amount {
letter-spacing: 0.1px;
color: #000000;
font-size: 16px;
}
@media (max-width: 767px) {
.receipt-content .invoice-wrapper .line-items .items .item .amount {
font-size: 13px; } 
}

.receipt-content .invoice-wrapper .line-items .total {
margin-top: 30px; 
}

.receipt-content .invoice-wrapper .line-items .total .extra-notes {
float: left;
width: 40%;
text-align: left;
font-size: 13px;
color: #000000;
line-height: 20px; 
}

@media (max-width: 767px) {
.receipt-content .invoice-wrapper .line-items .total .extra-notes {
width: 100%;
margin-bottom: 30px;
float: none; } 
}

.receipt-content .invoice-wrapper .line-items .total .extra-notes strong {
display: block;
margin-bottom: 5px;
color: #000000; 
}

.receipt-content .invoice-wrapper .line-items .total .field {
margin-bottom: 7px;
font-weight: 700;
font-size: 14px;
color: #000000; 
}

.receipt-content .invoice-wrapper .line-items .total .field.grand-total {
margin-top: 10px;
font-size: 16px;
font-weight: 700; 
}

.receipt-content .invoice-wrapper .line-items .total .field.grand-total span {
color: #000000;
font-weight: 700;
font-size: 16px; 
}

.receipt-content .invoice-wrapper .line-items .total .field span {
display: inline-block;
margin-left: 20px;
min-width: 85px;
color: #000000;
font-weight: 700;
font-size: 15px; 
}

.receipt-content .invoice-wrapper .line-items .print {
margin-top: 50px;
text-align: center; 
}



.receipt-content .invoice-wrapper .line-items .print a i {
margin-right: 3px;
font-size: 14px; 
}

.receipt-content .footer {
margin-top: 20px;
margin-bottom: 10px;
text-align: center;
font-size: 12px;
color: #969CAD; 
}            
.inv_logo{
  /* filter: grayscale(1); */
}       
.inv-main-bg{
  position: relative;
  background: transparent !important;
  z-index: 1;

}

.inv-main-bg::after{
  position: absolute;
  top: 0;
  right: 0;
  opacity: 0.2;
  content:  "";
  background: url("{{asset('images/inv.png')}}");
  width: 100%;
  height: 180px;
  z-index: -1;
  rotate: 180deg;
  background-repeat: no-repeat;

}

.inv-main-bg::before{
  position: absolute;
  bottom: 0;
  left: 0;
  opacity: 0.2;
  content:  "";
  background: url("{{asset('images/inv.png')}}");
  width: 100%;
  height: 180px;
  z-index: -1;
  rotate: 0deg;
  background-repeat: no-repeat;

}
</style>
<div class="receipt-content">
  
  <div class="container bootstrap snippets bootdey ">
      {{-- <img src="{{asset("images/inv.jpg")}}" alt="" class="bg-inv"> --}}
  <div class="row">
    <div class="col-md-12">
      <div class="invoice-wrapper inv-main-bg">
        <div class="intro">
          @if (isset($config) && $config->logo && $config->invoice_logo)
                <img src="{{asset("images/logo/$config->logo")}}"  alt="Not Available" style="margin-top : 20px" width="120px" class="inv_logo">
                @elseif(isset($config) && $config->invoice_name)
                  <h2 style="text-transform: uppercase">{{isset($config) ? $config->app_title : 'Demo'}}</h2>
                @endif
                @if (isset($config) && $config->invoice_logo && $config->invoice_name)
                    <br>
                <strong style="text-transform: uppercase">{{isset($config) ? $config->app_title : 'Demo'}}</strong>
                @endif
        </div>

        <div class="payment-details">
          <div class="row">
            <div class="col-sm-6">
              {{-- <span><strong>Party Details</strong></span> --}}
              @if (isset($order->customer->business_name) && $order->customer->business_name)
              <strong style="font-size: 20px">
                Business Name : {{isset($order->customer ) ? $order->customer->business_name : '' }}
              </strong>
              @endif
              <p>
                @if (isset($order->customer ))
                <strong style="font-size: 20px">Party Name: {{$order->customer->party_name}}</strong> <br>
                @if ($order->customer->phone)
                  {{$order->customer->phone}}                      
                @endif
                @if ($order->customer->location)
                    {{$order->customer->location}}
                @endif
                @if ($order->customer->email)
                    {{$order->customer->email}}
                @endif
                @else
                <strong style="font-size: 20px">Party : Cash</strong> <br>
                @endif

                @if (isset($order->broker) && $order->broker)
                    <b>Broker :</b> {{$order->broker}} <br>
                @endif

                @if (isset($order->condition) && $order->condition)
                <b>Condition :</b> {{$order->condition}}
                @endif
                
              </p>
            </div>
            <div class="col-sm-6 text-right">
              {{-- <span>Payment To</span> --}}
            
                <strong>Bill No. {{$order->tran_no ?? ""}}</strong>
                
                <p>
                <strong>Date : {{$order->bill_date !== null ? date('D, d M Y',strtotime($order->bill_date )) :date('D, d M Y', strtotime($order->created_at))}}</strong> <br>
                @if (isset($order->gp_no) && $order->gp_no)
                    Gate Pass : {{$order->gp_no}} <br>
                @endif
                @if (isset($order->truck_no) && $order->truck_no)
                    Truck No. {{$order->truck_no}} <br>
                @endif
              
              </p>
            </div>
          </div>
        </div>

        <div class="line-items">
          {{--Table --}}
                      <table id="" class="table table-sm table-bordered">
                          <thead>
                              <tr>
                                  <th>S#</th>
                                  {{-- <th>Field</th>
                                  <th>Category</th> --}}
                                  <th>Description</th>
                                  <th>Bag Pack.</th>
                                  <th>Qty</th>
                                  {{-- <th>Tax</th> --}}
                                  {{-- <th>Disc.</th> --}}
                                  <th>Weight.</th>
                                  <th>Rate</th>
                                  <th>Total</th>
                              </tr>
                          </thead>
                          <tbody>
                              @foreach ($order->order_details as $key => $item)
                          <tr class="items-table" >
                              <td>{{$key+1}}</td>
                              {{-- <td>{{$item->item_details->categories->field->name ?? ''}}</td>
                              <td>{{$item->item_details->categories->category ?? ''}}</td> --}}
                              <td>{{$item->item_details->name ?? ''}}</td>
                              <td>{{$item->bag_size ?? '-'}}</td>
                              <td>{{$item->bags ?? '-'}}</td>
                              {{-- <td>{{$item->tax}}</td> --}}
                              {{-- <td>{{$item->disc}}</td> --}}
                              <td>{{$item->qty}}</td>
                              <td>{{$item->rate}}</td>
                              <td>{{$item->total}}</td>
                          </tr>
                            @endforeach
          
                          </tbody>
                      </table>
                      @php
                          $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                          $amount_in_words = $formatter->format($order->net_total);
                      @endphp
                      
          {{--Table --}}
          <div class="total text-right">
            
            <p class="extra-notes">
              <strong>Amount in words :</strong>
               {{ucfirst($amount_in_words)}}  only.
               @if (isset($order->note)  && $order->note)
               <br>
               <br>
               <strong>Extra Notes</strong>
              {{$order->note ?? ''}}
              @endif
            </p>

            {{-- @if (isset($order->note)  && $order->note)
              <p class="extra-notes">
              <strong>Extra Notes</strong>
              {{$order->note ?? ''}}
            </p>
              @endif
               --}}
            <div class="field">
              Subtotal <span>{{ConfigHelper::getStoreConfig()["symbol"].round($order->gross_total)}}</span>
            </div>
                          @if ( $order->other_charges > 0)
            <div class="field">
              Bardana Charges <span>{{ConfigHelper::getStoreConfig()["symbol"].round($order->other_charges)}}</span>
            </div>
                          @endif
                          @if ($order->discount > 0)
            <div class="field">
              Discount <span>{{$order->discount_type == 'PERCENT' ? '%'.Round($order->discount) : ConfigHelper::getStoreConfig()["symbol"].Round($order->discount)}}</span>
            </div>
                          @endif
            <div class="field grand-total">
              Net Total. <span>{{ConfigHelper::getStoreConfig()["symbol"].round($order->net_total)}}</span>
            </div>

            @if (!ConfigHelper::getStoreConfig()["use_accounting_module"])
            <div class="field grand-total">
              Recieved <span>{{ConfigHelper::getStoreConfig()["symbol"].round($order->recieved ?? 0)}}</span>
            </div>

            <div class="field grand-total">
              Balance <span>{{ConfigHelper::getStoreConfig()["symbol"].round((($order->net_total ?? 0) - ($order->recieved ?? 0)) ?? 0)}}</span>
            </div>
            @endif

          </div>

          <div class="print" style="font-size: 14px">
            <td>
                <p style="text-align: center"> {{$config->invoice_message ?? ""}} </p>                  
            </td>
          </div>
                      <div class="footer" style="color: black; font-weight: 700">
                          The Software is Developed by TradeWisePOS | +92 320 0681969
                      </div>
        </div>
      </div>

      
    </div>
  </div>
</div>
</div>                    


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

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