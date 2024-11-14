@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-3">
        <h1 class="page-title">Purchase Return Detail Report</h1>
    </div>
    <div class="col">
        <form action="{{route('detail.purchase_return')}}" method="GET">
            <div class="btn-grp">
         
                <div class="row .row-customized">
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="from" 
                            value="{{request()->from ?? null}}" 
                            class="form-control">
                          </div>
                      
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="to" 
                            value="{{request()->to ?? null}}" 
                            placeholder="To"  class="form-control">
                          </div>
                      
                    </div>
                   
                    {{-- Livewire Component--}}
                    @livewire('category-product', 
                    ['selected_field' => request()->field,
                    'selected_category' => request()->category,
                     'selected_product' => request()->product]) 
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
            <th>PR.ID</th>
            <th>Doc #</th>
            <th>Date</th>
            {{-- <th>Field</th> --}}
            <th>Category</th>
            <th>Product</th>
            <th>Bag Size</th>
            <th>Bags</th>
            <th>Rate</th>
            <th>Tax</th>
            <th>Disc</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Total</th>
        </thead>
        <tbody>
            @foreach ($records as $item)
                <tr>
                    <td>{{$item->purchase_return_id}}</td>
                    <td>
                        <a href="{{url('/purchase/return/'.$item->purchase_return_id.'')}}" class="text-primary">{{$item->return->doc_no ?? ""}}</a></td>
                        {{-- <td>{{$item->items->categories->field->name ?? ""}}</td> --}}
                        <td>{{date('d/m/Y', strtotime($item->return->return_date))}}</td>
                    <td>{{$item->item_details->categories->category ?? ""}}</td>
                    <td>{{$item->item_details->name ?? ""}}</td>
                    <td>{{$item->bag_size ?? "-"}}</td>
                    <td>{{$item->bags ?? "-"}}</td>
                    <td>{{$item->returned_rate}}</td>
                    <td>%{{$item->returned_tax}}</td>
                    <td>%{{$item->returned_disc}}</td>
                    <td>{{$item->returned_qty}}</td>
                    <td>{{$item->item_details->uom ? $item->item_details->uoms->base_unit :( isset($item->item_details->uoms->uom) ? $item->item_details->uoms->uom : 'Default') }}</td>
                    <td>{{$item->returned_total}}</td>
                </tr>
            @endforeach
        
        </tbody>
        <tfoot>
            <th colspan="10">Total</th>
            <th colspan="2">{{$records->sum('returned_qty')}}</th>
            <th colspan="2">{{number_format($records->sum('returned_total'),2)}}</th>

        </tfoot>
    </table>
    {!! $records->links('pagination::bootstrap-4') !!}
</div>
</div>



@endsection