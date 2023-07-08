@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-4">
        <h1 class="page-title">Inventory Balance Report <small>{{session()->get('sales_filter_deleted') ? '(Deleted)' : ''}}</small></h1>
    </div>
    <div class="col">
        <form action="{{route('inventory-report.index')}}" method="GET">
            <div class="btn-grp">
         
                
                <div class="row .row-customized">
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="text" name="name" placeholder="Name or Category" value="{{session()->get('inventory_report_name')}}" class="form-control">
                          </div>
                      
                    </div>
                    {{-- Livewire Component--}}
                    @livewire('category-product', 
                    ['selected_field' => session()->get('inventory-report-field') ?? null,
                    'selected_category' => session()->get('inventory-report-category') ?? null,
                    'selected_product' => session()->get('inventory-report-product') ?? null]) 
                    {{-- Livewire Component--}} 
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <select name="filterBy" class="form-control" id="">
                                <option value="">All</option>
                                <option {{session()->get('inventory_filterBy') ? 'selected' : ''}} value="lowStock">Low Stock</option>
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
                    <input type="hidden" name="filter_deleted" value="{{session()->get('sales_filter_deleted') ? 'true' : 'false'}}">
                    <div class="col-lg-2">
                        <button class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
    
<table class="table table-sm table-responsive-sm table-striped">
    <thead>
        <th>Field</th>
        <th>Category</th>
        <th>Product</th>
        <th>Available Stock (Base units)</th>
        <th>Available Stock (Units)</th>
        <th>Base Unit Value</th>
        <th>Stock Alert</th>
        <th>TP</th>
        <th>Available Cost</th>
        @php
            $total = 0;
        @endphp
    </thead>
    <tbody>
       @foreach ($records as $key => $item)
                <tr >
                    <td>{{$item->field}}</td>
                    <td>{{$item->category}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{(!empty($item->stock_qty) ? ($item->stock_qty) : 0).' '.$item->base_unit}}</td>
                    <td>{{(!empty($item->stock_qty) ? (round($item->stock_qty / (($item->base_unit_value) ?? 1))) : 0).' '.$item->uom}}</td>
                    <td>{{($item->uom) ? '1 '.($item->uom).' = '.$item->base_unit_value.' '.$item->base_unit : ''}}</td>
                    <td>{{$item->low_stock}}</td>
                    <td>{{$item->tp ?? "0"}}</td>
                    @php
                        $total += (!empty($item->stock_qty) ? (round($item->stock_qty / (($item->base_unit_value) ?? 1))) : 0) * ($item->tp ?? 0);
                    @endphp
                    <td>{{ env('CURRENCY').(!empty($item->stock_qty) ? (round($item->stock_qty / (($item->base_unit_value) ?? 1))) : 0) * ($item->tp ?? 0)}}</td>
                </tr>
                @endforeach
                
    </tbody>   
</table>


   
        {!! $records->links('pagination::bootstrap-4') !!}
</div>
</div>



@endsection