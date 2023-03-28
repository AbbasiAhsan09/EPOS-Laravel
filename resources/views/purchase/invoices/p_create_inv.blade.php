@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col">
                            <h1 class="page-title">{{isset($order) ? 'Create' : 'Create'}} Purchase Invoice  </h1>
                        </div>
                    </div>

                    <div class="new_order_item_selection_wrapper">
                        <div class="new_order_item_selection">
                        <div class="item_selection_wrapper">
                            
                                <div class="input-group input-group-outline">
                                    <label class="form-label">Search</label>
                                    <input type="text" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)" id="searchItemValue">
                                </div> 
                                <div class="item_selection">
                                <div class="item_selection_list" id="item_selection_list">
                                   {{-- Getting List From Ajax --}}
                                </div>
                            </div>
                           </div>
                           {{-- Form start --}}
                        @if (isset($order))
                        <form action="{{route('invoice.store')}}" method="POST">
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
                                               
                                       @if (isset($order) && count($order->details))
                                       @foreach ($order->details as $item)
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
                                                   min="0" class="form-control tax" value="{{$item->tax}}"></td>
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
                            <h3 class="page-title text-primary">Total: {{env('CURRENCY')}} <span class="g_total ">0</span></h3>
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
                                        Invoice Type
                                    </h3>
                                    <div class="order_type_items">   
                                                {{-- <label for="posOrder" class="order-type-item">
                                                <input type="radio" name="order_tyoe" id="posOrder" value="pos" class="form-check-input order_type_val" {{isset($order) ? ($order->type == 'SALES' ? 'checked' : '') : 'checked'}}>
                                                    SALE QUOTATION 
                                                </label> --}}
                                      
                                                <label for="normalOrder" class="order-type-item">
                                                <input type="radio" name="order_tyoe" id="normalOrder" value="normal" class="form-check-input order_type_val" checked>
                                                    PURCHASE INVOICE
                                                </label>


                                                


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- Order TYpe --}}
                    {{-- Customer  --}}
                        <div class="select_party_wrapper">
                            <h4 class="order_section_sub_title">
                                Select Customer
                            </h4>
                            <div class="select_party">
                                
                                <div class="input-group input-group-outline">
                                    <select name="party_id" class="form-control" id="customer_select" >
                                        <option value="">Select Customer</option>
                                        {{-- @foreach ($customers as $customer)
                                            <option value="{{$customer->id}}" {{isset($order) &&  $order->party_id == $customer->id  ? 'selected' : ''}}>{{$customer->party_name}}</option>
                                        @endforeach --}}
                                    </select>
                                  </div> 
                            </div>
                        </div>
                    {{-- Customer  --}}

                     {{-- Customer  --}}
                     <div class="select_vendor_wrapper">
                        <div class="row">
                            <div class="col">
                                <h4 class="order_section_sub_title">
                                    Inv Date
                                </h4>
                                <div class="input-group input-group-outline">
                                    <input type="date" name="doc_date" class="form-control" value="{{date('Y-m-d',time())}}" >
                                  </div> 
                            </div>

                            <div class="col">
                                <h4 class="order_section_sub_title">
                                    Due Date
                                </h4>
                                <div class="input-group input-group-outline">
                                    <input type="date" name="due_date" class="form-control" value="{{date('Y-m-d',strtotime(\Carbon\Carbon::now()->addDays(10)))}}" >
                                  </div> 
                            </div>
                        </div>

                        <h4 class="order_section_sub_title">
                            Select Vendor
                        </h4>
                        <div class="select_party">
                            
                            <div class="input-group input-group-outline">
                                <select name="party_id" class="select2Style form-control" id="vendor_select" style="width: 100%">
                                    
                                    @foreach ($vendors as $vendor)
                                        <option value="{{$vendor->id}}"  {{isset($order) &&  $order->party_id == $vendor->id  ? 'selected' : ''}}>{{$vendor->party_name}}</option>
                                    @endforeach
                                </select>
                              </div> 
                        </div>
                        <h4 class="order_section_sub_title mt-2">Purchase Order #</h4>
                     
                            <div class="input-group input-group-outline">
                                <input type="text" name="q_num" class="form-control" value="{{isset($order) ? $order->doc_num : ''}}" readonly>
                              </div> 
                       
                    </div>
                {{-- Customer  --}}
                  
                    {{-- Payment Methods --}}
                    <div class="payment_methods_wrapper my-3">
                        <h4 class="order_section_sub_title">
                            Payment Methods
                        </h4>
                        <div class="payment_methods">
                            <label for="cash" class="order-type-item">
                                <input type="radio" name="payment_method" id="cash" value="cash" class="form-check-input order_type_val" checked>
                                   Cash
                            </label>
                            <label for="card" class="order-type-item">
                                <input type="radio" name="payment_method" id="card"  value="card" class="form-check-input order_type_val" >
                                   Card 
                            </label>

                            <label for="Bank" class="order-type-item other-methods">
                                <input type="radio" name="payment_method" id="Bank" value="bank"  class="form-check-input order_type_val " >
                                   Bank 
                            </label>

                            <label for="Cheque" class="order-type-item other-methods">
                                <input type="radio" name="payment_method" id="Cheque"  value="cheque" class="form-check-input order_type_val " >
                                   Cheque 
                            </label>
                            


                        </div>
                    </div>
                    {{-- Payment Methods --}}

                    {{-- Receiveing --}}
                    <div class="receiving-wrapper">
                        <div class="receiving">
                            <h4 class="order_section_sub_title">
                                Discount:
                            </h4>
                            <div class="input-group input-group-outline">
                                <div class="row">
                                    <div class="col">
                                        <input type="text" name="discount"  id="discount" class="form-control" required 
                                        value="{{isset($order) ? ($order->discount_type == 'PERCENT' ? '%'.(round(($order->discount * 100)/$order->sub_total, 2)) : $order->discount) : '%'}}" onkeypress="validationForSubmit()" >
                                    </div>
                                    <div class="col" id="discountSection" style="display: none">
                                        <input type="number"  id="discountValue" disabled readonly class="form-control" required value="%" onkeypress="validationForSubmit()" >
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4 class="order_section_sub_title">
                                Other Charges:
                            </h4>
                            <div class="input-group input-group-outline">
                                <input type="number" name="other_charges" id="otherCharges" class="form-control" required min="0" onkeypress="validationForSubmit()"  value="{{$order->other_charges  ?? 0}}">
                            </div>

                            <hr>
                            <h4 class="order_section_sub_title">
                                Remarks:
                            </h4>
                            <div class="input-group input-group-outline">
                            <textarea name="remarks" id="" cols="" rows="5" class="form-control" placeholder="Remarks Here...">{{isset($order) ? $order->remarks : ''}}</textarea>
                            </div>
                            <div class="">
                                <h4 class="order_section_sub_title" >
                                    Paid Amount ({{env('CURRENCY')}}):
                                </h4>
                                <div class="input-group input-group-outline">
                                    <input type="number" name="recieved" id="received-amount" class="form-control"  value="0" min="1" onkeypress="validationForSubmit()" >
                                </div> 
                                <hr>
                                <div class="row row-customized">
                                    <div class="col">
                                        <h4 class="order_section_sub_title">
                                            Balance:
                                        </h4>
                                    </div>
                                    <div class="col">
                                        <div class="input-group input-group-outline">
                                            <input type="number" class="form-control" disabled readonly  id="returning-amount">
                                        </div>
                                    </div>
                           
                                </div>
                                
                            </div>
                           
                            <hr>
                            <button class="btn btn-block btn-primary btn-lg" id="saveOrderBtn" style="width: 100%" disabled>Proccedd</button>
                        </form>
                        </div>
                    </div>
                    {{-- Receiveing --}}
                </div>
            </div>
        </div>
    </div>
    
@section('scripts')
     {{-- Custom jS --}}
<script src="{{asset('js/quotation.js')}}"></script>

<script>
    $(document).ready(function() {
        $('.select2Style').select2({
                    placeholder: "Select",
                    allowClear: true
                });
    });
        </script>
@endsection
@endsection