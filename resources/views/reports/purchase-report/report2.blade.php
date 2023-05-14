@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-4">
        <h1 class="page-title">Purchase Detail Report</h1>
    </div>
    <div class="col">
        <form action="{{route('purchase-report.detail')}}" method="GET">
            <div class="btn-grp">
         
                <div class="row .row-customized">
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="start_date" 
                            value="{{session()->get('purchase-detail-report-start-date')}}" 
                            class="form-control">
                          </div>
                      
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="end_date" 
                            value="{{session()->get('purchase-detail-report-end-date')}}" 
                            placeholder="To"  class="form-control">
                          </div>
                      
                    </div>
                   
                    {{-- Livewire Component--}}
                    @livewire('category-product', ['selected_category' => session()->get('purchase-detail-report-category') ?? null,
                     'selected_product' => session()->get('purchase-detail-report-product') ?? null]) 
                    {{-- Livewire Component--}} 


               

                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <select name="type" class="form-control" id="">
                                <option value="">Web</option>
                                <option value="pdf">PDF</option>
                            </select>
                          </div>
                      
                    </div>
                    <div class="col-lg-2">
                        <button class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped ">
        <thead>
            <th>Inv ID</th>
            <th>Doc #</th>
            <th>Category</th>
            <th>Product</th>
            <th>Rate</th>
            <th>Tax</th>
            <th>Disc</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Total</th>
            <th>Date</th>
        </thead>
        <tbody>
            @foreach ($records as $item)
                <tr>
                    <td>{{$item->inv_id}}</td>
                    <td>
                        <a href="{{url('purchase/invoice/'.$item->inv_id.'/edit')}}" class="text-primary">{{$item->invoice->doc_num}}</a></td>
                    <td>{{$item->items->categories->category}}</td>
                    <td>{{$item->items->name}}</td>
                    <td>{{$item->rate}}</td>
                    <td>%{{$item->tax}}</td>
                    <td>%{{0}}</td>
                    <td>{{$item->qty}}</td>
                    <td>{{$item->items->uom ? $item->items->uoms->base_unit :( isset($item->items->uoms->uom) ? $item->items->uoms->uom : 'Default') }}</td>
                    <td>{{$item->total}}</td>
                    <td>{{date('m-d-y', strtotime($item->created_at))}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
      
    </div>
</div>
</div>



@endsection