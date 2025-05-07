@extends('layouts.app')
@section('content')

@php
    $config = ConfigHelper::getStoreConfig();
@endphp
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="row row-customized">
                <div class="col-lg-4">
                    <h1 class="page-title">
                        Sales Report
                    </h1>
                </div>
                <div class="col">
                    <form action="{{ route('invoice-detail-report') }}" method="GET">
                        <div class="btn-grp">

                            <div class="row .row-customized">
                                <div class="col-lg-2">
                                    <div class="input-group input-group-outline">
                                        <input type="date" name="from"
                                            value="{{ request()->query('from') ?? "" }}" class="form-control">
                                    </div>

                                </div>
                                <div class="col-lg-2">
                                    <div class="input-group input-group-outline">
                                        <input type="date" name="to"
                                            value="{{ request()->query('to') ?? "" }}" placeholder="To"
                                            class="form-control">
                                    </div>

                                </div>
                                <div class="col-lg-4">
                                    <div class="input-group input-group-outline">
                                        <select name="customer" class="form-control" id="">
                                            <option value="">All Customers</option>
                                            <option value="0"
                                                {{ request()->query('customer') == '0' ? 'selected' : '' }}>Cash
                                            </option>
                                            <option value="exclude_cash"
                                                {{ request()->query('customer') == 'exclude_cash' ? 'selected' : '' }}>
                                                Exclude Cash</option>
                                            @foreach ($customers as $customer)
                                                <option value="{{ $customer->id }}"
                                                    {{ request()->query('customer') == $customer->id ? 'selected' : '' }}>
                                                    {{ $customer->party_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="col-lg-2">
                                    <div class="input-group input-group-outline">
                                        <select name="type" class="form-control" id="">
                                            <option value="">Web</option>
                                            <option value="pdf">PDF</option>
                                        </select>
                                    </div>

                                </div>
                                <input type="hidden" name="filter_deleted"
                                    value="{{ session()->get('sales_filter_deleted') ? 'true' : 'false' }}">
                                <div class="col-lg-2">
                                    <button class="btn btn-primary">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            @foreach ($records as $record)
            <table class="m-2 inv-table" width="100%" border="1" cellspacing="0" cellpadding="5">
                <thead >
                    <th>Doc# {{ $record->tran_no }}</th>
                    <th>Date : {{ $record->bill_date ? date('d/m/Y', strtotime($record->bill_date)) : date('d/m/Y', strtotime($record->created_at))   }}</th>
                    <th colspan="2">Party : {{ $record->customer->party_name ?? "" }}</th>
                    <th>GP : {{ $record->gp_no ? $record->gp_no : "N/A" }}</th>
                    <th>Truck No : {{ $record->truck_no ? $record->truck_no : "N/A" }}</th>
                   
                </thead>
                <tbody>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Rate</th>
                        <th>Disc</th>
                        <th>Tax</th>
                        <th>Total</th>
                    </tr>
                    @foreach ($record->order_details as $item)
                        <tr>
                            <td><strong>{{ $item->item_details->name ?? "" }}</strong></td>
                            <td>{{ $item->qty ?? "0" }} {{ $item->unit ? $item->unit->symbol : '' }}</td>
                            <td>{{$item->rate}}</td>
                            <td>%{{ $item->disc ?? 0 }}</td>
                            <td>%{{ $item->tax ?? 0 }}</td>
                            <td>{{$config['symbol'].number_format($item->total,2) ?? 0 }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <th colspan="2">Line Items ({{ count($record->order_details) }})</th>
                    <th>Gross Total : {{ $config['symbol'].number_format($record->gross_total ?? 0, 2) }}</th>
                    <th>Discount : {{$record->discount_type == 'PERCENT' ? '% -'.number_format($record->discount,2) : $config['symbol'].' -'.number_format($record->discount,2)}}</th>
                    <th>Other Charges : {{ $config['symbol'].number_format($record->other_charges, 2) }}</th>
                    <th>Net Total : {{ $config['symbol'].number_format($record->net_total ?? 0, 2) }}</th>
                </tfoot>
                <tbody>
                  
                </tbody>
              
            </table>
            @endforeach
            



            {!! $records->links('pagination::bootstrap-4') !!}
        </div>
    </div>



@endsection
