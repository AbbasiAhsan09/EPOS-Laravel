@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-4">
        <h1 class="page-title">Inventory Balance Report <small>{{session()->get('sales_filter_deleted') ? '(Deleted)' : ''}}</small></h1>
    </div>
    <div class="col">
        <form action="{{route('inventory.balance')}}" method="GET">
            <div class="btn-grp">
         
                
                <div class="row .row-customized">
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="text" name="name" placeholder="Name or Category" value="{{request()->name}}" class="form-control">
                          </div>
                      
                    </div>
                    {{-- Livewire Component--}}
                    @livewire('category-product', 
                    ['selected_field' => request()->field ?? null,
                    'selected_category' => request()->category ?? null,
                    'selected_product' => request()->product ?? null]) 
                    {{-- Livewire Component--}} 
                    {{-- <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <select name="filterBy" class="form-control" id="">
                                <option value="">All</option>
                                <option {{session()->get('inventory_filterBy') ? 'selected' : ''}} value="lowStock">Low Stock</option>
                            </select>
                          </div>
                      
                    </div> --}}
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
        <th>Opening Stock</th>
        <th>Purchased Stock</th>
        <th>P.Return Stock</th>
        <th>Sold Stock</th>
        <th>S.Return Stock</th>
        <th>Available Stock</th>
        <th>Avg Cost</th>
        <th>Total Cost</th>
        @php
            $total = 0;
        @endphp
    </thead>
    <tbody>
       @foreach ($results as $key => $item)
                <tr >
                    <td>{{$item->field}}</td>
                    <td>{{$item->category}}</td>
                    <td>{{$item->name}}</td>
                    <td>{{number_format(($item->opening_qty ?? 0),2)}}</td>
                    <td>
                        @if ($item->purchased_qty != 0)
                        <a href="/reports/purchase-detail-report?field={{$item->field_id}}&category={{$item->category_id}}&product={{$item->item_id}}&type=" target="_blank">{{number_format(($item->purchased_qty),2)}}</a>
                        @else
                        {{number_format(($item->purchased_qty),2)}}
                        @endif
                    </td>
                    <td>
                        @if ($item->purchase_return_qty != 0)
                        <a href="/purchase/returns/detail/?field={{$item->field_id}}&category={{$item->category_id}}&product={{$item->item_id}}&type=" target="_blank">{{number_format($item->purchase_return_qty,2)}}</a>
                        @else
                            {{number_format($item->purchase_return_qty,2)}}
                        @endif
                    </td>
                    <td>
                        @if ($item->sold_qty != 0)
                            <a href="/reports/sales-detail-report?field={{$item->field_id}}&category={{$item->category_id}}&product={{$item->item_id}}&type=" target="_blank" >{{number_format(($item->sold_qty),2)}}</a>
                        @else
                        {{number_format(($item->sold_qty),2)}}
                        @endif
                    </td>
                    
                    <td>
                        @if ($item->sold_returned_qty != 0)
                        <a href="/sales/returns/detail?field={{$item->field_id}}&category={{$item->category_id}}&product={{$item->item_id}}&type=" target="_blank" >{{number_format(($item->sold_returned_qty),2)}}</a>
                        @else
                            {{number_format($item->sold_returned_qty,2)}}
                        @endif
                    </td>
                    <td>{{number_format(($item->avl_qty),2)}}</td>
                    <td>{{number_format(($item->avg_rate),2)}}</td>
                    <td>{{number_format(($item->avg_rate) * ($item->avl_qty) ,2)}}</td>
                </tr>
                @endforeach
                
    </tbody>   
</table>


   
        {!! $results->links('pagination::bootstrap-4') !!}
</div>
</div>



@endsection