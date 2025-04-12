@extends('layouts.app')
@section('content')
@include("includes.spinner")
    <div class="page-wrapper">
           {{-- Form start --}}
                        {{-- @if (isset($order)) --}}
                        <form action="{{route('invoice.store')}}" method="POST" id="inv_form">
                            @csrf
                            @method('post')
                        {{-- @endif --}}
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-12">
                    <div class="row">
                        <div class="col-lg-2">
                            <h1 class="page-title">{{isset($order) ? 'Create' : 'Create'}} Purchase Invoice  </h1>
                            @if (isset($last_id) && $last_id)
                                PI/{{$last_id+1}}
                            @endif
                        </div>
                        <div class="col">
                              {{-- Customer  --}}
                     <div class="select_vendor_wrapper">
                        <div class="row mb-2">
                            <div class="col-lg-2">
                                <h4 class="order_section_sub_title">
                                    Inv Date 
                                </h4>
                                <div class="input-group input-group-outline">
                                    <input type="date" name="doc_date" required class="form-control" value="{{isset($order) ? $order->doc_date : date('Y-m-d',time())}}" >
                                  </div> 
                            </div>

                            <div class="col-lg-2">
                               @if (isset($config) && $config->due_date_enabled )
                               <h4 class="order_section_sub_title">
                                Due Date
                            </h4>
                            <div class="input-group input-group-outline">
                                <input type="date" name="due_date" class="form-control" value="{{isset($order) ? $order->due_date : date('Y-m-d',strtotime(\Carbon\Carbon::now()->addDays(10)))}}" >
                              </div>
                               @endif 
                            </div>
                            <div class="col-lg-2">
                                <h4 class="order_section_sub_title">
                                    Select Vendor
                                </h4>
                                <div class="select_party">
                                    
                                    <div class="input-group input-group-outline">
                                        <select name="party_id" class="select2Style form-control" id="vendor_select" style="width: 100%">
                                            
                                            @foreach ($vendors as $group => $vendorGroups)
                                            <optgroup label="{{ucfirst($group)}}">
                                                @foreach ($vendorGroups as $vendor)
                                                    
                                                
                                                <option value="{{$vendor->id}}"  {{isset($order) &&  $order->party_id == $vendor->id  ? 'selected' : ''}}>{{$vendor->party_name}}</option>
                                            </optgroup>
                                                @endforeach
                                            @endforeach
                                        </select>
                                        <input type="hidden" name="q_num" class="form-control" value="{{isset($order) ? $order->doc_num : ''}}" readonly>
                                      </div> 
                                </div>
                            </div>
                            <div class="col-lg-4">
                                 {{-- Total  --}}
                                <div class="order_total_wrapper my-3" >
                                    <div class="order_total" style="background: red; color : white !important">
                                        <h3  class="p-2 page-title text-primary" style="color: white !important">Total: {{ConfigHelper::getStoreConfig()["symbol"]}} <span class="g_total ">0</span></h3>
                                        <input type="hidden" name="gross_total" id="gross_total">
                                    </div>
                                </div>
                            {{-- Total  --}}
                            </div>
                            {{-- <div class="col-lg-2">
                                <h4 class="order_section_sub_title mt-2">Purchase Order #</h4>
                     
                                <div class="input-group input-group-outline">
                                  </div> 
                            </div> --}}
                        </div>
                       
                    </div>
                {{-- Customer  --}}
                        </div>
                    </div>

                    <div class="new_order_item_selection_wrapper mid-order-selection">
                        <div class="new_order_item_selection">
                        <div class="item_selection_wrapper">
                            
                            @if (isset($config) && $config->search_filter  == 'type')
                            <div class="input-group input-group-outline">
                                <label class="form-label">Search</label>
                                <input type="text" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)" id="searchItemValue">
                            </div> 
                            <div class="item_selection">
                            <div class="item_selection_list" id="item_selection_list">
                               {{-- Getting List From Ajax --}}
                            </div>
                            </div>
                            @else
                            @livewire('category-product-component-for-order-search',['col' => 12] )

                            @endif
                           </div>
                        
                              <table class="table table-sm table-responsive-sm table-striped table-bordered ">
                                <thead>
                                    <th>Description</th>
                                    <th>UOM</th>
                                    {{-- <th>Bag Size</th>
                                    <th>Bags</th> --}}
                                    <th>Rate</th>
                                    {{-- <th>MRP</th> --}}
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
                                            {{-- <input type="hidden" name="uom[]" value="1"> --}}
                                            @if ($item->item_details->product_units->count() > 0)
                                            <select class="form-control unit_id" name="unit_id[]"
                                                data-unit_type_id="{{ $item->item_details->unit_type_id }}"
                                                {{ !$item->item_details->unit_type_id ? 'readonly' : '' }}>

                                                @if (!$item->item_details->unit_type_id)
                                                    <option value="1">Single</option>
                                                @endif

                                                @if ($item->item_details->unit_type_id && isset($item->item_details->product_units) && count($item->item_details->product_units) > 0)
                                                    @foreach (collect($item->item_details->product_units)->sortByDesc('default') as $product_unit)
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
                                            {{-- <td><input name="bag_size[]" type="number" step="0.01" placeholder="Size"
                                                min="0" class="form-control bag_size" value="{{$item->bag_size}}"></td> --}}
                                                {{-- <td><input name="bags[]" type="number" step="0.01" placeholder="Bags"
                                                    min="0" class="form-control bags" value="{{$item->bags}}"></td> --}}
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
                                        <th colspan="4 text-left">Total</th>
                                        <th class="foot_g_total"></th>
                                    </tr>
                                </tfoot>
                              </table>
                        </div>
                    </div>
                     {{-- Other Dynamic Fields --}}
                     @if (isset($dynamicFields) && count($dynamicFields->fields) )
                     <div class="card " >
                         
                         <div class="card-body">
                             <h3 class="order_section_sub_title">Custom Fields</h3>
                             <hr>
                             <div class="row">
                             @foreach ($dynamicFields->fields as $dynamicField)
                             <div class="col-lg-6">
                                 @php
                                     $old_field_value = '';
                                 @endphp
                                 @if (isset(($order->dynamicFeildsData)) && count($order->dynamicFeildsData))
                                     @foreach ($order->dynamicFeildsData as $dynamicFieldData)
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

                     <div class="card bottom-inv-section p-4">
                        <div class="receiving">
                            <div class="row">
                                <div class="col-lg-2">
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
                                </div>
                                <div class="col-lg-2">
                                    <h4 class="order_section_sub_title">
                                        Other Charges:
                                    </h4>
                                    <div class="input-group input-group-outline">
                                        <input type="number" name="other_charges" id="otherCharges" class="form-control" required min="0" 
                                        onkeypress="validationForSubmit()"  value="{{isset($order) ? $order->others : 0}}">
                                    </div>
                                </div>
                                <div class="col-lg-3">
                                    <input type="hidden" name="order_tyoe" id="normalOrder" value="normal" class="form-check-input order_type_val"  checked>
                                    <h4 class="order_section_sub_title">
                                        Remarks:
                                    </h4>
                                    <div class="input-group input-group-outline">
                                    <textarea name="remarks" style="height: 38px" id="" cols="" rows="5" class="form-control" placeholder="Remarks Here...">{{isset($order) ? $order->remarks : ''}}</textarea>
                                    </div>
                                </div>
                                @if (!ConfigHelper::getStoreConfig()["use_accounting_module"])
                                <div class="col-lg-3">
                                    <h4 class="order_section_sub_title" >
                                        Paid Amount ({{ConfigHelper::getStoreConfig()["symbol"]}}):
                                    </h4>
                                    <div class="input-group input-group-outline">
                                       
                                        <input type="number" name="recieved" id="received-amount" class="form-control"  value="0"  onkeypress="validationForSubmit()" >
                                    </div> 
                                </div>
                                
                                <div class="col-lg-3">
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
                              </div>
                                    @endif
                             
                            </div>
                            
                           
                            <hr>
                            <button class="btn btn-block btn-primary btn-lg" id="saveOrderBtn" style="width: 100%" disabled>Proceed</button>
                        </form>
                        </div>
                     </div>
 
             {{-- Dynamic field end --}}

                </div>
            </div>
        </div>
    </form>
    </div>
    <script>
           let submitted = 0;
           let isSubmitting = false;
           const buttons =   document.querySelectorAll("#inv_form button")
           const form = document.querySelector("#inv_form");

            form.addEventListener("keypress", function (event) {
                // Check if the target of the event is an input element
                if (event.target.tagName === "INPUT" && event.key === "Enter") {
                    // alert("hiu");
                    event.preventDefault(); // Prevent form submission
                }
            });

        buttons.forEach((button) => {
           button.addEventListener("keypress", function (event) {
               if (event.key === "Enter") {
               event.preventDefault();  // Prevent form submission
               }
           });
           });



    document.addEventListener("keydown", function (event) {
    if (event.altKey && event.key === "a") {
        // alert("pressed");
        event.preventDefault(); // Prevent the default behavior

        // Check if the "save" button is disabled and the gross total value is valid
        const inValid = document.getElementById("saveOrderBtn").disabled;
        const gTotal = document.getElementById("gross_total").value;
        const grossTotalNumber = +gTotal; // Convert to a number
        const form = document.getElementById("inv_form"); // Reference to the form

        // Log for debugging

        // Check if the form is valid
        if (form.checkValidity() && !inValid && !isNaN(grossTotalNumber) && grossTotalNumber > 0 && submitted === 0) {
            showSpinner(); // Show spinner while processing
            submitted++; // Increment submitted flag
            isSubmitting = true;
            form.requestSubmit(); // Submit the form
        } else {
            // If form is not valid, trigger native validation message
            isSubmitting = false;
            form.reportValidity();
        }
    }
});

    </script>
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