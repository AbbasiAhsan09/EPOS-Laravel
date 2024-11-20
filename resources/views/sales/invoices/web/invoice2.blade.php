@extends('sales.invoices.web.layout')
@section('invoice_content')
	<div class="invoice-details">
		<div class="middle-meta" >
			<div class="party-details" >
				{{-- <h6 style="margin-bottom: 10px">Party Details</h6> --}}
				@if (isset($order->customer ) && $order->customer)
					
					@if ($order->customer->business_name)
						<h4>{{$order->customer->business_name}}</h4>
					@endif
					
					<h4>{{$order->customer->party_name}}</h4>
					
					@if ($order->customer->phone)
						<p><strong>PH: </strong>{{$order->customer->phone}}</p>
					@endif


					@if ($order->customer->email)
					<p> <strong>Email: </strong> {{$order->customer->email}}</p>
					@endif


					@if ($order->customer->location)
					<p><strong>Add:</strong> {{$order->customer->location}}</p>
					@endif

				@else
					<h4>Cash</h4>
				@endif
			</div>
			<div class="invoice-other-info">
				@if (isset($order->condition) && $order->condition)
				<h4>Condition : {{$order->condition}}</h4>
				@endif
			</div>
		</div>
		<table class="invoice-table">
			<thead>
				<th width="50px">S No.</th>
				<th>Description</th>
				<th>Rate</th>
				<th>Qty</th>
				<th>Total</th>
			</thead>
			<tbody>
				@foreach ($order->order_details as $key => $item)
				<tr>
					<td>{{$key+1}}</td>
					<td>{{$item->item_details->name ?? ''}}</td>
					<td>{{number_format($item->qty,2)}}</td>
					<td>{{number_format($item->rate,2)}}</td>
					<td>{{number_format($item->total,2)}}</td>
				</tr>
				@endforeach
				<tr>
					<td colspan="2" style="border: none" rowspan="5">
						@if ($order->note)
							<strong>Note:</strong> {{$order->note}} <br>
						@endif
						@php
						$formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
						$amount_in_words = $formatter->format($order->net_total);
						@endphp
						<strong>Amount in words: </strong>{{ucfirst($amount_in_words)}}
						
					</td>
					<th colspan="2">Sub Total</th>
					<th>{{ConfigHelper::getStoreConfig()["symbol"].number_format($order->gross_total,2)}}</th>
				</tr>
				@if ($order->other_charges > 0)
				<tr>
					<th colspan="2">Other Charges</th>
					<th>{{ConfigHelper::getStoreConfig()["symbol"].number_format($order->other_charges,2)}}</th>
				</tr>
				@endif

				@if ($order->discount > 0)
				<tr>
					<th colspan="2">Discount</th>
					<th>{{$order->discount_type == 'PERCENT' ? '%'.number_format($order->discount,2) : ConfigHelper::getStoreConfig()["symbol"].number_format($order->discount,2)}}</th>
				</tr>
				@endif

				<tr>
					<th colspan="2">Net Total</th>
					<th>{{ConfigHelper::getStoreConfig()["symbol"].number_format($order->net_total,2)}}</th>
				</tr>

				@if (!ConfigHelper::getStoreConfig()["use_accounting_module"])
				<tr>
					<th colspan="2">Recieved</th>
					<th>{{ConfigHelper::getStoreConfig()["symbol"].number_format($order->recieved,2)}}</th>
				</tr>

				<tr>
					<th colspan="2">Balance</th>
					<th>{{ConfigHelper::getStoreConfig()["symbol"].number_format((($order->net_total ?? 0) - ($order->recieved ?? 0)) ?? 0),2}}</th>
				</tr>
				@endif


			</tbody>
		</table>
	</div>

	<style>
		.middle-meta{
			display: flex;
			align-items: center;
			justify-content: space-between;
			margin: 20px 0;
		}
		.party-details h4{
			margin: 5px 0
		}
	</style>
@endsection