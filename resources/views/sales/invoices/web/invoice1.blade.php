<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Invoice {{$order->tran_no}}</title>
	<style>
		/* CSS styles */
		body {
			font-family: Arial, sans-serif;
			margin: 0;
			padding: 0;
		}
		
		.invoice-container {
			width: 800px;
			margin: 0 auto;
			padding: 20px;
			border: 1px solid #ccc;
		}
		
		h1 {
			font-size: 30px;
			text-align: center;
			margin-top: 0;
		}
		
		.invoice-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			margin-bottom: 20px;
		}
		
		.invoice-header h2 {
			margin: 0;
		}
		
		.invoice-details {
			display: flex;
			justify-content: space-between;
			align-items: flex-start;
		}
		
		.invoice-details p {
			margin: 0;
		}
		
		.invoice-items {
			margin-top: 40px;
		}
		
		.invoice-items table {
			width: 100%;
			border-collapse: collapse;
		}
		
		.invoice-items th,
		.invoice-items td {
			padding: 10px;
			text-align: left;
			border: 1px solid #ccc;
		}
		
		.invoice-items th {
			background-color: #f2f2f2;
		}
		
		.invoice-total {
			margin-top: 20px;
			display: flex;
			justify-content: flex-end;
			align-items: center;
		}
		
		.invoice-total p {
			margin: 0;
			margin-right: 10px;
		}
		
		.invoice-total strong {
			font-size: 20px;
		}
	</style>
</head>
<body>
	<div class="invoice-container">
		<header class="invoice-header">
			<h1>
                @if (isset($config) && $config->logo)
                <img src="{{asset("images/logo/$config->logo")}}" alt="Not Available" width="150px">
                @endif
            </h1>
			<h2>
                Invoice# {{$order->tran_no}}    
            </h2>
		</header>
		
		<section class="invoice-details">
			<div>
				<p>From:</p>
				<p>{{$config->app_title}}</p>
				<p>{{$config->address}}</p>
				<p>{{$config->phone}}</p>
			</div>
			
			<div>
				<p>To:</p>
				<p> {{isset($order->customer ) ? $order->customer->name : 'Cash' }}</p>
                <p>{{isset($order->customer ) ? $order->customer->phone : '' }}</p>
				<p>{{isset($order->customer ) ? $order->customer->email : '' }}</p>
				
			</div>
		</section>
		
		<section class="invoice-items">
			<table>
				<thead>
					<tr>
                        <th>S#</th>
						<th>Description</th>
						<th>Rate</th>
						<th>Tax</th>
						<th>Discount</th>
						<th>Qty.</th>
						<th>Total</th>
					</tr>
				</thead>
				<tbody>
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
				
                  <tr>
                    <th colspan="6">Gross Total</th>
                    <th colspan="6">{{env('CURRENCY').round($order->gross_total)}}</th>
                  </tr>
                  <tr>
                    <th colspan="6">Discount</th>
                    <th colspan="6">{{env('CURRENCY').Round($order->discount)}}</th>
                  </tr>
                  <tr>
                    <th colspan="6">Other Charges</th>
                    <th colspan="6">{{env('CURRENCY').round($order->other_charges)}}</th>
                  </tr>

                </tbody>
            </table>

        </section>
    </div>
</body>
</html>
