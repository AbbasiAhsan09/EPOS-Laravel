@extends('layouts.app')
@section('content')
    <div class="page-wrapper">
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col">
                            <h1 class="page-title">{{isset($invoice) ? 'Edit' : 'Create'}} Purchase Invoice: {{$invoice->doc_num}}  </h1>
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
                        @if (isset($invoice))
                        <form action="{{route('invoice.update', $invoice->id)}}" method="POST">
                            @csrf
                            @method('put')
                        @endif
                              <table class="table table-sm table-responsive-sm table-striped table-bordered ">
                                <thead>
                                    <th>Description</th>
                                    <th>UOM</th>
                                    @if ($config->show_bag_sizing)                                            
                                    <th>Bag Size</th>
                                    <th>Bags</th>
                                    @endif
                                   
                                    <th>Rate</th>
                                    {{-- <th>MRP</th> --}}
                                    <th>Qty</th>
                                    <th>Tax</th>
                                    <th>Total</th>
                                </thead>
                                <tbody id="cartList">
                                               
                                       @if (isset($invoice) && count($invoice->details))
                                       @foreach ($invoice->details as $item)
                                       <tr data-id="{{$item->items->barcode}}" class="itemsInCart">
                                           <td>{{$item->items->name}}
                                          
                                            {{-- <input type="hidden" name="item_id[]" value="{{$item->item_id}}"> --}}
                                            {{-- <input type="hidden" name="uom[]" value="1"> --}}
                                        </td>
                                        <td> 
                                            <input type="hidden" name="item_id[]" value="{{$item->item_id}}">
                                            {{-- <input type="hidden" name="uom[]" value="1"> --}}
                                            <select class="form-control unit_id" name="unit_id[]"
                                            data-unit_type_id="{{ $item->items->unit_type_id }}"
                                            {{ !$item->items->unit_type_id ? 'readonly' : '' }}>
                                            
                                            @if (!$item->items->unit_type_id)
                                            <option value="">Single</option>
                                            @endif
                                            
                                            @if ($item->items->unit_type_id && $item->items->product_units->count() > 0)
                                                @if ($item->items->unit_type_id && isset($item->items->product_units) && count($item->items->product_units) > 0)
                                                    @foreach (collect($item->items->product_units)->sortByDesc('default') as $product_unit)
                                                        <option value="{{ $product_unit->unit->id }}"
                                                            {{ $item->unit_id == $product_unit->unit->id ? 'selected' : '' }}
                                                            data-conversion_rate="{{ $product_unit->conversion_rate }}"
                                                            data-rate="{{ $product_unit->unit_rate }}"
                                                            data-cost="{{ $product_unit->unit_cost }}">
                                                            {{ $product_unit->unit->symbol }}
                                                        </option>
                                                    @endforeach
                                                @endif
                                            </select>
                                           
                                        @else
                                        @endif
                                            </td>
                                            @if ($config->show_bag_sizing)     
                                            <td><input name="bag_size[]" type="number" step="0.01" placeholder="Size"
                                                min="0" class="form-control bag_size" value="{{$item->bag_size}}"></td>
                                            <td><input name="bags[]" type="number" step="0.01" placeholder="Bags"
                                                min="0" class="form-control bags" value="{{$item->bags}}"></td>
                                                @endif
                                            <td><input name="rate[]" type="number" step="0.01" placeholder="Rate"
                                                min="1" class="form-control rate" value="{{$item->rate}}"></td>
                                                {{-- <td><input name="mrp[]" type="number" step="0.01" placeholder="Rate"
                                                    min="1" class="form-control mrp" value="{{$item->mrp}}" required></td> --}}
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

                            {{-- Other Dynamic Fields --}}
                            @if (isset($dynamicFields) && count($dynamicFields->fields) )
                            <div class="card" >
                                
                                <div class="card-body">
                                    <h3 class="order_section_sub_title">Custom Fields</h3>
                                    <hr>
                                    <div class="row">
                                    @foreach ($dynamicFields->fields as $dynamicField)
                                    <div class="col-lg-6">
                                        @php
                                            $old_field_value = '';
                                        @endphp
                                        @if (isset(($invoice->dynamicFeildsData)) && count($invoice->dynamicFeildsData))
                                            @foreach ($invoice->dynamicFeildsData as $dynamicFieldData)
                                                @if ($dynamicField->id === $dynamicFieldData->field_id)
                                                    @php
                                                        $old_field_value = $dynamicFieldData->value ?? "";
                                                    @endphp
                                                @endif
                                            @endforeach
                                        @endif
                                        <h3 class="order_section_sub_title">{{$dynamicField->label}}</h3>
                                        <div class="input-group input-group-outline">
                                             <input type="{{$dynamicField->type === 'input' && $dynamicField->datatype === 'string' ? 'text' : 'number'}}" class="form-control" 
                                             name="dynamicFields[][{{$dynamicField->name}}]" {{$dynamicField->required ? 'required' : ''}} 
                                             value="{{$old_field_value}}"  min="0" onfocus="focused(this)" onfocusout="defocused(this)">
                                            
                                          </div>
                                    </div>
                                    @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
        
                    {{-- Dynamic field end --}}
             
                    
                </div>
                <div class="col-lg-4 order_detail_wrapper">
                      {{-- Total  --}}
                      <div class="order_total_wrapper my-3">
                        <div class="order_total">
                            <h3 class="page-title text-primary">Total: {{ConfigHelper::getStoreConfig()["symbol"]}} <span class="g_total ">0</span></h3>
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
                                                <input type="radio" name="order_tyoe" id="posOrder" value="pos" class="form-check-input order_type_val" {{isset($invoice) ? ($invoice->type == 'SALES' ? 'checked' : '') : 'checked'}}>
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
                                            <option value="{{$customer->id}}" {{isset($invoice) &&  $invoice->party_id == $customer->id  ? 'selected' : ''}}>{{$customer->party_name}}</option>
                                        @endforeach --}}
                                    </select>
                                  </div> 
                            </div>
                        </div>
                    {{-- Customer  --}}

                     {{-- Customer  --}}
                     <div class="select_vendor_wrapper">
                        <div class="row mb-2">
                            <div class="col">
                                <h4 class="order_section_sub_title">
                                    Inv Date 
                                </h4>
                                <div class="input-group input-group-outline">
                                    <input type="date" name="doc_date" required class="form-control" value="{{$invoice->doc_date}}" >
                                  </div> 
                            </div>

                            <div class="col">
                                @if (isset($config) && $config->due_date_enabled )
                                <h4 class="order_section_sub_title">
                                    Due Date
                                </h4>
                                <div class="input-group input-group-outline">
                                    <input type="date" name="due_date" class="form-control" value="{{$invoice->due_date}}" >
                                  </div> 
                                  @endif
                            </div>
                        </div>
                        <h4 class="order_section_sub_title">
                            Select Vendor
                        </h4>
                        <div class="select_party">
                            
                            <div class="input-group input-group-outline">
                                <select name="party_id" class="select2Style form-control" id="vendor_select" style="width: 100%">
                                    
                                    @foreach ($vendors as $group => $vendorGroups)
                                        <optgroup label="{{ucfirst($group)}}">
                                        @foreach ($vendorGroups as $vendor)
                                        <option value="{{$vendor->id}}"  {{isset($invoice) &&  $invoice->party_id == $vendor->id  ? 'selected' : ''}}>{{$vendor->party_name}}</option>
                                        @endforeach
                                    </optgroup>
                                    @endforeach
                                </select>
                              </div> 
                        </div>
                        <h4 class="order_section_sub_title mt-2">Purchase Order #</h4>
                     
                            <div class="input-group input-group-outline">
                                <input type="text" name="q_num" class="form-control" value="{{isset($invoice) ? ($invoice->order->doc_num  ?? ""): ''}}" readonly>
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
                                        value="{{isset($invoice) ? ($invoice->discount_type == 'PERCENT' ? '%'.(round(($invoice->discount * 100)/$invoice->total, 2)) : $invoice->discount) : '%'}}" onkeypress="validationForSubmit()" >
                                    </div>
                                    <div class="col" id="discountSection" style="display: none">
                                        <input type="number"  id="discountValue" disabled readonly class="form-control" required value="%" onkeypress="validationForSubmit()" >
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <h4 class="order_section_sub_title">
                                OtherCharges: 
                            </h4>
                            <div class="input-group input-group-outline">
                                <input type="number" name="other_charges" id="otherCharges" class="form-control" required min="0" onkeypress="validationForSubmit()"  value="{{$invoice->others  ?? 0}}">
                            </div>

                            <hr>
                            <h4 class="order_section_sub_title">
                                Remarks:
                            </h4>
                            <div class="input-group input-group-outline">
                            <textarea name="remarks" id="" cols="" rows="5" class="form-control" placeholder="Remarks Here...">{{isset($invoice) ? $invoice->remarks : ''}}</textarea>
                            </div>
                            <div class="">
                                @if (!ConfigHelper::getStoreConfig()["use_accounting_module"])
                                <h4 class="order_section_sub_title" >
                                    Paid Amount ({{ConfigHelper::getStoreConfig()["symbol"]}}):
                                </h4>
                                {{-- @dump($order) --}}
                                <div class="input-group input-group-outline">
                                    <input type="number" name="recieved" id="received-amount" class="form-control"  value="{{isset($invoice) ? $invoice->recieved : 0}}" min="0" onkeypress="validationForSubmit()" >
                                </div> 
                                <hr>
                                @endif
                                
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
                            <button class="btn btn-block btn-primary btn-lg" id="saveOrderBtn" style="width: 100%" disabled>Proceed</button>
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