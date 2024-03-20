                      

<style>
    .receipt-content .logo a:hover {
  text-decoration: none;
  /* color: #7793C4;  */
}

.receipt-content .invoice-wrapper {
  background: white !important;
  border: 1px solid #CDD3E2;
  box-shadow: 0px 0px 1px #CCC;
  padding: 20px 40px 10px;
  margin-top: 40px;
  border-radius: 4px; 
}

.receipt-content .invoice-wrapper .payment-details span {
  color: #A9B0BB;
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
  background: white; 
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
  color: #A9B0BB; 
}

.receipt-content .invoice-wrapper .payment-info strong {
  display: block;
  color: #444;
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
  margin-top: 40px; 
}
.receipt-content .invoice-wrapper .line-items .headers {
  color: #A9B0BB;
  font-size: 13px;
  letter-spacing: .3px;
  border-bottom: 2px solid #EBECEE;
  padding-bottom: 4px; 
}
.receipt-content .invoice-wrapper .line-items .items {
  margin-top: 8px;
  border-bottom: 2px solid #EBECEE;
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
  color: #84868A;
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
  color: #7A7A7A;
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
  color: #454545; 
}

.receipt-content .invoice-wrapper .line-items .total .field {
  margin-bottom: 7px;
  font-size: 14px;
  color: #555; 
}

.receipt-content .invoice-wrapper .line-items .total .field.grand-total {
  margin-top: 10px;
  font-size: 16px;
  font-weight: 500; 
}

.receipt-content .invoice-wrapper .line-items .total .field.grand-total span {
  color: #20A720;
  font-size: 16px; 
}

.receipt-content .invoice-wrapper .line-items .total .field span {
  display: inline-block;
  margin-left: 20px;
  min-width: 85px;
  color: #84868A;
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

.sign-line{
    border-top: solid 1px black;
    margin-top: 50px;
    padding: 10px;
}
.inv_logo{
    /* filter: grayscale(1); */
}       
/* .inv-main-bg{
    position: relative;
    background: transparent !important;
    z-index: 1;

}

.inv-main-bg::after{
    position: absolute;
    top: 0;
    right: 0;
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
    content:  "";
    background: url("{{asset('images/inv.png')}}");
    width: 100%;
    height: 180px;
    z-index: -1;
    rotate: 0deg;
    background-repeat: no-repeat;

} */
</style>
<div class="receipt-content">
    
    <div class="container bootstrap snippets bootdey ">
        {{-- <img src="{{asset("images/inv.jpg")}}" alt="" class="bg-inv"> --}}
		<div class="row">
			<div class="col-md-12">
				<div class="invoice-wrapper inv-main-bg">

					{{-- <div class="intro">
						@if (isset($config) && $config->logo)
									<img src="{{asset("images/logo/$config->logo")}}"  alt="Not Available" style="margin-top : 20px" width="120px" class="inv_logo">
									@else
										{{isset($config) ? $config->app_title : 'Demo'}}
									@endif
					</div> --}}

                    <h3 class="text-center text-uppercase"><b>Delivery Challan</b></h3>

					<div class="payment-info">
						<div class="row">
							<div class="col-sm-4">
								<span>Challan No.</span>
								<strong>{{$order->tran_no ?? ""}}</strong>
							</div>
							<div class="col-sm-4 text-right">
								<span>Order Date:</span>
								<strong>{{$order->bill_date !== null ? date('D, d M Y',strtotime($order->bill_date )) :date('D, d M Y', strtotime($order->created_at))}}</strong>
							</div>

                            <div class="col-sm-4 text-right">
								<span>Printed On:</span>
								<strong>{{date('D, d M Y', time())}}</strong>
							</div>
						</div>
					</div>

					<div class="payment-details">
						<div class="row">
							<div class="col-sm-6">
								<span>To</span>
								<strong>
									{{isset($order->customer ) ? $order->customer->business_name : '' }}
								</strong>
								<p>
									{{isset($order->customer ) ? $order->customer->party_name : 'Cash' }} <br>
									{{isset($order->customer ) ? $order->customer->phone : '' }}  <br>
									{{isset($order->customer ) ? $order->customer->location : '' }}  <br>
									<a href="#">
                                        {{isset($order->customer ) ? $order->customer->email : '' }}
									</a>
								</p>
							</div>
							<div class="col-sm-6 text-right">
								<span>From</span>
								<strong>
									{{$config->app_title ?? ""}}
								</strong>
								<p>
									{{$config->address ?? ""}} <br>
									{{$config->phone ?? ""}} <br>
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
                                    {{-- <th>Rate</th> --}}
                                    {{-- <th>Tax</th> --}}
                                    {{-- <th>Disc.</th> --}}
                                    <th>Qty.</th>
                                    {{-- <th>Total</th> --}}
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($order->order_details as $key => $item)
                            <tr class="items-table" >
                                <td>{{$key+1}}</td>
                                {{-- <td>{{$item->item_details->categories->field->name ?? ''}}</td>
                                <td>{{$item->item_details->categories->category ?? ''}}</td> --}}
                                <td>{{$item->item_details->fullProductName ?? ''}}</td>
                                {{-- <td>{{$item->rate}}</td>
                                <td>{{$item->tax}}</td>
                                <td>{{$item->disc}}</td> --}}
                                <td>{{$item->qty}}</td>
                                {{-- <td>{{$item->total}}</td> --}}
                            </tr>
                              @endforeach
            
                            </tbody>
                        </table>
						{{--Table --}}
						
                       
                            <div class="row">
                               <div class="col-6">
                                <p class="sign-line"> Signature</p>
                               </div>

                               <div class="col-6">
                                <p class="sign-line">Receiver's Signature</p>
                               </div>
                             </div>

                        <div class="footer">
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

<style>
      @page {
    size: auto !important;
}
</style>