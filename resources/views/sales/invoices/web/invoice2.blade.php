
		

		<style>
			.invoice-box {
				max-width: 800px;
				margin: auto;
				padding: 10px;
				border: 1px solid black;
		
				font-size: 16px;
				line-height: 20px;
				font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
				color: #555;
			}

			.invoice-box table {
				width: 100%;
				line-height: inherit;
				text-align: left;
			}

			.invoice-box table td {
				padding: 5px;
				vertical-align: top;
			}

			.invoice-box table tr td:nth-child(2) {
				text-align: right;
			}

		

			.invoice-box table tr.top table td.title {
				font-size: 40px;
				line-height: 1px;
				color: #333;
			}

			.invoice-box table tr.information table td {
				padding-bottom: 5px;
			}

			.invoice-box table tr.heading td {
				background: #eee;
				border-bottom: 1px solid #ddd;
				font-weight: bold;
			}

			.invoice-box table tr.details td {
				padding-bottom: 20px;
			}

			.invoice-box table tr.item td {
				border-bottom: 1px solid #eee;
			}

			.invoice-box table tr.item.last td {
				border-bottom: none;
			}

			.invoice-box table tr.total td:nth-child(2) {
				border-top: 2px solid #eee;
				font-weight: bold;
			}

			@media only screen and (max-width: 600px) {
				.invoice-box table tr.top table td {
					width: 100%;
					display: block;
					text-align: center;
				}

				.invoice-box table tr.information table td {
					width: 100%;
					display: block;
					text-align: center;
				}
			}

			/** RTL **/
			.invoice-box.rtl {
				direction: rtl;
				font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
			}

			.invoice-box.rtl table {
				text-align: right;
			}

			.invoice-box.rtl table tr td:nth-child(2) {
				text-align: left;
			}
           
           #items, #items th,#items td {
              
                border-collapse: collapse;
                text-align: center;
                border: solid 1px;
                }

                .bottom{
                    margin-top: 30px;

                }
                .sign-line{
                 border-top: solid 1px;
                 margin-top: 50px;
                 width: fit-content;
                 color: black;
                 font-size: 12px;
                 font-weight: 700
                }
                .cb-box{
                    border: 1px solid;
                    width:40%;
                }

				.footer-total th{
					text-align: left !important
				}
		</style>


	
		<div class="invoice-box">
			<table cellpadding="0" cellspacing="0" >
				<tr class="top">
					<td colspan="2">
						<table>
							<tr>
								<td class="title">
									
									
									<h6 style="margin-bottom: 5px !important">Invoice # {{$order->tran_no}}  
										<br>
										<br>
									@if (isset($config) && $config->logo)
									<img src="{{asset("images/logo/$config->logo")}}" alt="Not Available" style="margin-top : 20px" width="120px">
									@else
										{{isset($config) ? $config->app_title : 'Demo'}}
									@endif 
									<br>
										<p style="font-size: 16px;margin-top:30px">{{$config->app_title}} </p>
										<p style="font-size: 16px">{{$config->address}}</p>
										<p style="font-size: 16px">{{$config->phone}}</p>
									</h6>

								</td>

								<td>
									Order #: {{$order->tran_no}}  <br />
									Invoice Date: {{$order->bill_date !== null ? date('D, d M Y',strtotime($order->bill_date )) :date('D, d M Y', strtotime($order->created_at))}} <br />
									Printed On:{{date('D, d M Y', time())}}
								</td>
							</tr>
						</table>
					</td>
				</tr>

				<tr class="information">
					<td colspan="2">
						<table>
							<tr>
								<td>
									<b>Buyer's Details</b><br />
									{{isset($order->customer ) ? $order->customer->party_name : 'Cash' }}<br />
									{{isset($order->customer ) ? $order->customer->phone : '' }} <br>
                                    {{isset($order->customer ) ? $order->customer->email : '' }}<br/>
                                    {{isset($order->customer ) ? $order->customer->location : '' }}
									
                                  
								</td>
							</tr>
							
						</table>
					</td>
				</tr>

			

			</table>
            <table id="items">
				<thead>
					<tr>
                        <th>S#</th>
						{{-- <th>Field</th>
						<th>Category</th> --}}
						<th>Description</th>
						<th>Rate</th>
						<th>Tax</th>
						<th>Disc.</th>
						<th>Qty.</th>
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
                    <td>{{$item->rate}}</td>
                    <td>{{$item->tax}}</td>
                    <td>{{$item->disc}}</td>
                    <td>{{$item->qty}}</td>
                    <td>{{$item->total}}</td>
                </tr>
                  @endforeach
				
                  <tr class="footer-total">
                    {{-- <th colspan="7"></th> --}}
                    <th colspan="5">Gross Total</th>
                    <th colspan="2">{{env('CURRENCY').round($order->gross_total)}}</th>
                  </tr>
                  @if ($order->discount > 0)
				  <tr class="footer-total">
                    {{-- <th colspan="7"></th> --}}
                    <th colspan="5">Discount</th>
                    <th colspan="2">{{$order->discount_type == 'PERCENT' ? '%'.Round($order->discount) : env('CURRENCY').Round($order->discount)}}</th>
                  </tr>
				  @endif

                  @if ( $order->other_charges > 0)
				  <tr class="footer-total">
                    {{-- <th colspan="7"></th> --}}
                    <th colspan="5">Other Charges</th>
                    <th colspan="2">{{env('CURRENCY').round($order->other_charges)}}</th>
                  </tr>
				  @endif
				  <tr class="footer-total">
                    {{-- <th colspan="7"></th> --}}
                    <th colspan="5">Net Total</th>
                    <th colspan="2">{{env('CURRENCY').round($order->net_total)}}</th>
                  </tr>
				  <tr class="footer-total">
                    {{-- <th colspan="7"></th> --}}
                    <th colspan="5">Received</th>
                    <th colspan="2">{{env('CURRENCY').round($order->recieved ?? 0)}}</th>
                  </tr>

				  @if ($order->recieved)
				  <tr class="footer-total">
                    {{-- <th colspan="7"></th> --}}
                    <th colspan="5">Balance</th>
                    <th colspan="2">{{env('CURRENCY').round((($order->net_total ?? 0) - ($order->recieved ?? 0)) ?? 0)}}</th>
                  </tr>
				  @endif

                </tbody>
            </table>

            <div class="bottom">
               
				<tr>
					<td><b>Note:</b><br>
					{{$order->note ?? ''}}
				<br></td>
				</tr>
				<tr>
					<td>
						<b>Track Your Order</b><br />
						Goto : https://moon-fabrics.com/check-status<br />
						Inv No : {{$order->tran_no}} <br>
						Password : {{$order->password ?? ""}}
						
					  
					</td>
				</tr>
				
                    <p class="sign-line">Receiver's Signature</p>
					<p style="font-size: 12px">
						<b>This Software is Developed By TradeWisePOS  {{isset($config)  ? $config->dev_contact : ''}}</b>
					</p>

            </div>
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
		</div>

	