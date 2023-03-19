@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col">
                            <h1 class="page-title">{{(isset($purchaseRequest) ? 'Edit'  : 'Create')}} Purchase Requisition  {{isset($purchaseRequest) ? ': '. $purchaseRequest->id : ''}}</h1>
                        </div>

                    </div>

                    <div class="new_order_item_selection_wrapper">
                        <div class="new_order_item_selection">
                            <div class="item_selection_wrapper">

                                <div class="input-group input-group-outline">
                                    <label class="form-label">Search</label>
                                    <input type="text" class="form-control" onfocus="focused(this)"
                                        onfocusout="defocused(this)" id="searchItemValue">
                                </div>
                                <div class="item_selection">
                                    <div class="item_selection_list" id="item_selection_list">
                                        {{-- Getting List From Ajax --}}
                                    </div>
                                </div>
                            </div>
                            {{-- Form start --}}
                            @if (isset($purchaseRequest))
                            <form action="{{ route('request.update', $purchaseRequest->id) }}" method="POST">
                                @csrf
                                @method('put')
                            @else
                            <form action="{{ route('request.store') }}" method="POST">
                                @csrf
                                @method('post')
                            @endif
                                <table class="table table-sm table-responsive-sm table-striped table-bordered ">
                                    <thead>
                                        <th>Description</th>
                                        <th>UOM</th>
                                        <th>Rate</th>
                                        <th>Qty</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                    </thead>
                                    <tbody id="cartList">


                                       @if (isset($purchaseRequest) && count($purchaseRequest->details))
                                       @foreach ($purchaseRequest->details as $item)
                                       <tr data-id="{{$item->items->barcode}}" class="itemsInCart">
                                           <td>{{$item->items->name}}</td>
                                           <td> 
                                            <input type="hidden" name="item_id[]" value="{{$item->item_id}}">
                                            @if ($item->items->uom != 0)
                                            <select name="uom[]" class="form-control uom" data-id="{{$item->items->uoms->base_unit_value}}">
                                                <option value="1">{{$item->items->uoms->uom}}</option>
                                                <option value="{{$item->items->uoms->base_unit_value}}" {{$item->is_base_unit ? 'selected' : ''}}>{{$item->items->uoms->base_unit}}</option>
                                            </select>
                                            @else
                                            <select name="uom[]" class="form-control uom" data-id="1" >
                                                <option value="1">Default</option>
                                            </select>
                                            @endif
                                            </td>
                                           <td><input name="rate[]" type="number" step="0.01" placeholder="Rate"
                                                   min="1" class="form-control rate" value="{{$item->rate}}"></td>
                                           <td><input name="qty[]" type="number" step="0.01" placeholder="Qty"
                                                   min="1" class="form-control pr_qty" value="{{$item->qty}}"></td>
                                           <td><input name="tax[]" type="number" step="0.01" placeholder="Tax"
                                                   min="0" class="form-control tax" value="{{$item->taxes}}"></td>
                                           <td class="total">{{$item->total}}</td>
                                           <td> <i class="fa fa-trash" aria-hidden="true"></i></td>
                                           <td></td>
                                       </tr>
                                   @endforeach
                                       @endif

                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="5 text-left">Total</th>
                                            <th class="foot_g_total"></th>
                                        </tr>
                                    </tfoot>
                                </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 order_detail_wrapper">
                    {{-- Total  --}}
                    <div class="order_total_wrapper my-3">
                        <div class="order_total">
                            <h3 class="page-title text-primary">Total: {{ env('CURRENCY') }} <span class="g_total ">0</span>
                            </h3>
                            <input type="hidden" name="gross_total" id="gross_total">
                        </div>
                    </div>
                    {{-- Total  --}}
                    {{-- ORder TYpe --}}
                    <div class="order_create_details_wrapper">
                        <div class="order_create_details">
                            <div class="order_type_wrapper">
                                <div class="order_type">
                                    <h3 class="order_section_sub_title">
                                        PR Type
                                    </h3>
                                    <div class="order_type_items">
                                        <label for="STANDARD" class="order-type-item">
                                            <input type="radio" name="order_tyoe" id="STANDARD" value="STANDARD"
                                                class="form-check-input order_type_val" checked>
                                            STANDARD
                                        </label>

                                        <label for="CONTRACT" class="order-type-item">
                                            <input type="radio" name="order_tyoe" id="CONTRACT" value="CONTRACT"
                                                class="form-check-input order_type_val" disabled>
                                            CONTRACT
                                        </label>
                                        <label for="PLANNED" class="order-type-item">
                                            <input type="radio" name="order_tyoe" id="PLANNED" value="PLANNED"
                                                class="form-check-input order_type_val" disabled>
                                            PLANNED
                                        </label>
                                        <label for="STANDING" class="order-type-item">
                                            <input type="radio" name="order_tyoe" id="STANDING" value="STANDING"
                                                class="form-check-input order_type_val" disabled>
                                            STANDING
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Order TYpe --}}
                    {{-- ORder TYpe --}}
                    <div class="order_create_details_wrapper my-3">
                        <div class="order_create_details">
                            <div class="order_type_wrapper">
                                <div class="order_type">
                                    <h3 class="order_section_sub_title">
                                        Required Before
                                    </h3>
                                    <div class="input-group input-group-outline">
                                        <input type="date" name="required_on" id="otherCharges" class="form-control" value="{{isset($purchaseRequest) ? $purchaseRequest->required_on : '' }}"
                                            required>
                                    </div>
                                    <h3 class="order_section_sub_title mt-3">
                                        Remarks
                                    </h3>
                                    <div class="input-group input-group-outline">
                                        <textarea name="remarks" id="" cols="30" rows="4" class="form-control"
                                            placeholder="Write any thing extra here...">{{isset($purchaseRequest) ? $purchaseRequest->remarks : ''}}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Order TYpe --}}
                    <div>
                        <button type="submit" class="btn btn-primary btn-block my-5" style="width: 100%">
                            Save & Send
                        </button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
@section('scripts')
    <script src="{{ asset('js/purchase_request.js') }}"></script>
@endsection
@endsection
