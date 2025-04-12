@extends('layouts.app')
@section('content')
@php
    
    $isEditMode = isset($order);
    

@endphp
@include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/eNhzTjJ2rIE'])
@include("includes.spinner")
    <div class="page-wrapper">
          {{-- Form start --}}
          @if ($isEditMode)
          <form action="{{route('update.purchase_return' , $order->id)}}" method="POST" id="sale_form">
              @csrf
              @method('put')
          @else
          <form action="{{route('add.purchase_return')}}" method="POST" id="sale_form">
              @csrf
              @method('post')
          @endif
        <div class="container-fluid">
            <div class="row ">
                <div class="col-lg-12">
                    
                    <div class="mid-section">
                        <div class="row align-items-center">
                            <div class="col-lg-2">
                                <h1 class="page-title">{{$isEditMode ? 'Edit' : 'Create'}} Purchase Return {{$isEditMode ? (' : '.$order->tran_no ?? '') : '' }}</h1>
                                @if ($isEditMode)
                                    <b>{{$order->doc_no ?? ""}}</b>
                                @endif
                            </div>
                    
                            <div class="col-lg-10">
                                <div class="order-meta">
                                    <div class="row">
                                        <div class="col-lg-2">
                                            <div class="bill-date-wrapper">
                                             <h3 class="order_section_sub_title">
                                                 Purchase Inv.
                                             </h3>
                                             <div class="input-group input-group-outline d-flex align-items-center justify-content-center">
                                             <input type="text" placeholder="18/SA/23/07/1" name="invoice_no" class="form-control" id="invoice_no" value="{{$isEditMode ? $order->invoice_no : ''}}">
                                             <button  type="button" class="btn btn-small btn-primary" style="margin: 0"  id="search_invoice_button"><i class="fa fa-search"></i></button>
                                             <button  type="button" class="btn btn-small btn-danger" style="margin: 0;display: none"  id="clear_invoice_selected" ><i class="fa fa-close"></i></button>
                                            </div>
                                            </div>
                                         </div>
                                        {{-- @if ($config->bill_date_changeable && Auth::user()->userroles->role_name == 'Admin' || Auth::user()->userroles->role_name == 'SuperAdmin') --}}
                                        <div class="col-lg-2">
                                        <div class="bill-date-wrapper">
                                         <h3 class="order_section_sub_title">
                                             Return Date
                                         </h3>
                                         <div class="input-group input-group-outline">
                                         <input type="date" name="return_date" class="form-control" required
                                         id="" value="{{$isEditMode ? $order->return_date : date('Y-m-d',time())}}">
                                         </div>
                                        </div>
                                     </div>
                                        {{-- @endif --}}

                                        <input type="hidden" id="is_accounting_module" 
                                        value="{{isset(ConfigHelper::getStoreConfig()["use_accounting_module"]) ? ConfigHelper::getStoreConfig()["use_accounting_module"] : false }}">
            
                                        <div class="col-lg-5" id="invoice_pre_data" style="display: none">
                                        </div>

                                        <div class="col-lg-3" id="order_type_wrapper">
                                            <div class="order_type_wrapper">
                                                <div class="order_type">
                                                    <h3 class="order_section_sub_title">
                                                        Type
                                                    </h3>
                                                    <div class="order_type_items" style="display: flex; align-items: center; justify-content: space-between">   
                                                                {{-- <label for="posOrder" class="order-type-item" style="width: 49%">
                                                                <input type="radio" name="order_type" id="posOrder" value="pos" class="form-check-input order_type_val" checked>
                                                                    POS ORDER
                                                                </label> --}}
                                                      
                                                                <label for="normalOrder" class="order-type-item" style="width: 49%"  >
                                                                <input type="radio" name="order_type" id="normalOrder" value="normal" checked class="form-check-input order_type_val" {{ $isEditMode && $order->party_id ? 'checked'  : '' }}>
                                                                    Purchase Return
                                                                </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {{-- Customer  --}}
                                        <div class="col-lg-3" id="select_party_wrapper">
                                        <div class="select_party_wrapper">
                                            <h4 class="order_section_sub_title">
                                                Select Party
                                            </h4>
                                            <div class="select_party">
                                                
                                                <div class="input-group input-group-outline">
                                                    <select name="party_id" class="form-control" id="customer_select" >
                                                        <option value="">Select Party</option>
                                                        @foreach ($customers as $group => $parties)
                                                            <optgroup label="{{ ucfirst($group ?? '') }}">
                                                                @foreach ($parties as $party)
                                                                    <option value="{{ $party->id }}" 
                                                                        {{ $isEditMode && $order->party_id == $party->id ? 'selected' : '' }}>
                                                                        {{ $party->party_name }}
                                                                    </option>
                                                                @endforeach
                                                            </optgroup>
                                                        @endforeach
                                                    </select>
                                                  </div> 
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Customer  --}}
                                        <div class="col-lg-2">
                                               {{-- Total  --}}
                                                <div class="order_total_wrapper my-3 px-2" style="border: solid 2px; background : rgb(222, 26, 26)">
                                                    <div class="order_total">
                                                        <h3 class="page-title text-primary" style="color: white !important">Total: {{ConfigHelper::getStoreConfig()["symbol"]}} <span class="g_total ">{{ $isEditMode ? $order->net_total : 0 }}</span></h3>
                                                        <input type="hidden" name="total" id="gross_total" >
                                                    </div>
                                                </div>
                                                {{-- Total  --}}
                                        </div>
                                        
                                    </div>
                               {{-- ORder TYpe --}}
                               
                            {{-- Order TYpe --}}
                                        
                                </div>
                            </div>
                        </div>
                       
                        <hr style="color: black;background: #000;">
    
                        <div class="new_order_item_selection_wrapper">
                            
    
                            <div class="new_order_item_selection">
                            <div class="item_selection_wrapper">
                                
                                @if ($config->search_filter == 'type' )     
                                <div class="input-group input-group-outline" id="search_type_filter">
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
                            
                            <input type="hidden" value="{{$config->show_tp_in_order_form}}" id="show_tp_in_order_form">
                                  <table class="table table-sm table-responsive-sm table-striped table-bordered ">
                                    <thead>
                                        <th>Description</th>
                                        <th>UOM</th>
                                        {{-- @if ($config->show_tp_in_order_form)
                                        <th>TP
                                        </th>
                                        @endif --}}
                                        {{-- <th>Bag Size</th>
                                        <th>Bags</th> --}}
                                        <th>Rate</th>
                                        <th>Qty</th>
                                        <th>Tax</th>
                                        <th>Total</th>
                                    </thead>
                                    <tbody id="cartList">
                                        
                                        @if ($isEditMode && isset($order->order_details) && count($order->order_details))
                                           @foreach ($order->order_details as $item)
                                           <tr data-id="{{$item->item_details->barcode}}" class="itemsInCart">
                                            <td>{{$item->item_details->name}}
                                                <input type="hidden" name="item_id[]" value="{{$item->item_id}}">
                                                <input type="hidden" name="uom[]" value="1">
                                            </td>
                                            <td>
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
                                            {{-- <td> 
                                            
                                             @if ($item->item_details->uom != 0)
                                             <select name="uom[]" class="form-control uom" data-id="{{$item->item_details->uoms->base_unit_value}}">
                                                 <option value="1">{{$item->item_details->uoms->uom}}</option>
                                                 <option value="{{$item->item_details->uoms->base_unit_value}}" {{$item->is_base_unit ? 'selected' : ''}}>{{$item->item_details->uoms->base_unit}}</option>
                                             </select>
                                             @else
                                             <select name="uom[]" class="form-control uom" data-id="1" >
                                                 <option value="1">Default</option>
                                             </select>
                                             @endif
                                             </td> --}}
                                             {{-- @if ($config->show_tp_in_order_form)
                                             <td><input name="tp[]" readonly disabled type="number" step="0.01" placeholder="TP"
                                                min="1" class="form-control" value="{{$item->item_details->tp}}"></td>
                                             @endif --}}
                                             {{-- <td>
                                                <input name="bag_size[]" type="number" step="0.01" placeholder="Size"
                                                    min="0" class="form-control bag_size" value="{{$item->bag_size}}">
                                             </td>
                                             <td>
                                                <input name="bags[]" type="number" step="0.01" placeholder="Size"
                                                    min="0" class="form-control bags" value="{{$item->bags}}">
                                             </td> --}}
                                            <td><input name="rate[]" type="number" step="0.01" placeholder="Rate"
                                                    min="1" class="form-control rate" value="{{$item->returned_rate}}"></td>
                                            <td><input name="qty[]" type="number" step="0.01" data-item-id="{{$item->item_details->id}}" placeholder="Qty"
                                                    min="1" class="form-control pr_qty {{$isEditMode ? 'edit_qty' : ''}}" value="{{$item->returned_qty}}"></td>
                                            <td><input name="tax[]" type="number" step="0.01" placeholder="Tax"
                                                    min="0" class="form-control tax" value="{{$item->returned_tax}}"></td>
                                            <td class="total">{{$item->returned_total}}</td>
                                            <td> <i class="fa fa-trash" aria-hidden="true"></i></td>
                                            <td></td>
                                        </tr>
                                           @endforeach
                                        @endif
                                        
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4 text-left">Total</th>
                                            <th class="foot_g_total">{{$isEditMode ? $order->gross_total : 0}}</th>
                                        </tr>
                                    </tfoot>
                                  </table>
                            </div>
                        </div>
                    </div>

                     {{-- Other Dynamic Fields --}}
                     {{-- @if (isset($dynamicFields) && count($dynamicFields->fields) )
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
                     @endif --}}
 
             {{-- Dynamic field end --}}
             
             {{-- Refactor Bottom --}}
             <div class="bottom-sticky">
                <div class="card">
                    <div class="card-body">
    
                        <div class="row align-items-center justify-content-between">
                         
                           
                            <div class="col-lg-2">
                                <h4 class="order_section_sub_title">
                                    Discount:
                                </h4>
                                <div class="input-group input-group-outline">
                                    <div class="row">
                                        <div class="col">
                                            <input type="text" name="discount"  id="discount" class="form-control" 
                                            required value="{{$isEditMode ? ($order->discount_type  === 'PERCENT' ? '%'.$order->discount : ($order->discount)) : '%'}}" onkeypress="validationForSubmit()"  >
                                        </div>
                                        <div class="col" id="discountSection" style="display: {{$isEditMode && $order->discount_type === 'PERCENT' ? 'block' : 'none'}}">
                                            <input type="number"  id="discountValue" disabled readonly 
                                            class="form-control" required value="{{$isEditMode && $order->discount_type === 'PERCENT' ? number_format(($order->gross_total / 100 * $order->discount) , 2) : 0 }}" onkeypress="validationForSubmit()" >
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-1">
                                <h4 class="order_section_sub_title">
                                    Other Charges:
                                </h4>
                                <div class="input-group input-group-outline">
                                    <input type="number" name="other_charges" id="otherCharges" class="form-control" required value="{{$isEditMode ? $order->other_charges : 0}}" min="0" onkeypress="validationForSubmit()" >
                                </div>
                            </div>
                            <div class="col-lg-8">
                                <h4 class="order_section_sub_title">
                                    Reasone of return:
                                </h4>
                                <div class="input-group input-group-outline">
                                    <textarea style="max-height: 38px" name="reason" id="" class="d-block form-control" rows="3">{{$isEditMode ? $order->reason : ""}}</textarea>
                                </div>
                            </div>
                          
              
                          
                           <div class="col-lg">
                            <h4 class="order_section_sub_title">
                                Print 
                            </h4>
                            <label for="print_invoice" class="order-type-item">
                                <input type="checkbox" name="print_invoice" value="1" id="print_invoice" class="form-check-input order_type_val" >
                                   <i class="fa fa-print"></i>
                            </label>
                           </div>
                            <div class="col-lg-12">
                                <button class="btn btn-block btn-primary btn-lg" id="saveOrderBtn" style="width: 100%" disabled>Proceed</button>
    
                            </div>
                        </div>
    
                    </div>
                 </div>
             </div>

             <div class="short-keys-wrapper">
                <div class="row align-items-center">
                    <div class="col-lg-1">
                        <h5>Short Keys</h5>
                    </div>
                    <div class="col-lg-11">
                        <div class="short-keys">
                            <div class="short-key-item">
                                Ctrl + C = Swith Order Type
                            </div>
                            <div class="short-key-item">
                                / = Go to Search Product Input
                            </div>
                            <div class="short-key-item">
                                Alt + A = To Save Order
                            </div>
                        </div>
                    </div>
                </div>
                
             </div>
             {{-- Refactor Bottom --}}
            </div>
        </div>
    </form>
    </div>
    @if (session('openNewWindow'))
        <script>
            $(document).ready(function(){

                window.open("{{url("/invoice/".session('openNewWindow')."")}}","popupWindow", "width=300,height=600,scrollbars=yes,left="+($(window).width()-400)+",top=50");
            })
        </script>
    @endif
@section('scripts')
     {{-- Custom jS --}}
<script src="{{asset('js/sale_return.js')}}"></script>

<script>

$("#search_invoice_button").on("click",function(){
    search_invoice();
});

function search_invoice() {
    var tran_no = $('#invoice_no').val();
    if (tran_no) {
        showSpinner();
        $.ajax({
            url: '/api/purchase/invoice', // API endpoint
            type: 'POST',
            data: JSON.stringify({
                tran_no: tran_no, // Correctly passing the transaction number
                store_id: "{{ Auth::user()->store_id }}" // Use the store ID from authenticated user
            }),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Laravel's CSRF token for security
            },
            contentType: 'application/json', // Set content type to JSON
            dataType: "json", // Expected response type
            success: function (data) {
                hideSpinner();
                if (data) {
                    disabledFields({enabled : false});
                    auto_define_invoice_data(data);
                    console.log(data); // Log the returned data to the console for inspection
                }else{
                    swal('Not Found '+tran_no,"Purchase not found for document no: " + tran_no,'error')
                    emptyInvoiceField()
                   
                }
            },
            error: function (xhr, status, error) {
                // Error handling
                hideSpinner();
                swal('Not Found '+tran_no,"Purchase not found for document no: " + tran_no,'error');
                emptyInvoiceField()
                console.log('Error details:', xhr.responseText);
            }
        });
    } else {
        swal('Empty Purchase Invoice No', 'Please provide the invoice number.', 'error');
    }

    
}

function emptyInvoiceField(){
        $('#invoice_no').val('');
        $('#invoice_no').focus();
}


function auto_define_invoice_data(data){
   
    const party_id = +data?.party_id;
    $("#invoice_no").prop("readonly",true);
    $("#search_invoice_button").css("display","none");
    var parent_div = $("#invoice_pre_data");
    parent_div.css("display",'block');
    let partyName = 'Cash';

    if(+party_id){
        partyName = data?.party?.party_name;
    }

    const billDate = data?.bill_date && moment(data?.bill_date).isValid() ? moment(data?.bill_date).format('MM-D-Y') : moment(data?.created_at).format('MM-DD-Y')
    const billDateDiv = `<div id="selected_inv_bill_date" class="col-lg-6"><strong>Bill Date: ${billDate ?? ''}</strong></div>`
    const invNoDiv = `<div id="selected_inv_no" class="col-lg-6"><strong>Purchase No: ${data?.doc_num ?? ''}</strong></div>`
    const customerDiv = `<div id="selected_inv_customer" class="col-lg-6"><strong>Party: ${partyName ?? ''}</strong></div>`;
    const netTotal = `<div id="selected_inv_net_total" class="col-lg-6"><strong style="color:red">Original Net: {{ConfigHelper::getStoreConfig()["symbol"]}}${data?.net_amount ?? ''}</strong></div>`;
    const subDiv = `<div class="row g-2" style="font-size:18px">${billDateDiv+invNoDiv+customerDiv+netTotal}</div>`
    parent_div.append(subDiv);
    

}

$("#clear_invoice_selected").on("click", function(){
    clearSelectedInvoice();
})

function clearSelectedInvoice(){
    disabledFields({enabled: true});
    emptyInvoiceField();
}

function disabledFields(data = {enabled : false}){

    const enabled = data.enabled;

    if(enabled){
        // enabled
    $("#order_type_wrapper").css("display",'block');
    $("#order_type_wrapper input").prop("disabled",false);
    $("#invoice_no").prop("readonly",false);
    $("#select_party_wrapper").css("display",'block');
    $("#select_party_wrapper select").prop("disabled",false);
    $("#invoice_pre_data").html('').css("display",'none');
    $("#search_invoice_button").css("display","inline");
    $("#clear_invoice_selected").css("display","none");
    }else{
        // Diabled
    $("#order_type_wrapper").css("display",'none');
    $("#order_type_wrapper input").prop("disabled",true);

    $("#select_party_wrapper").css("display",'none');
    $("#select_party_wrapper select").prop("disabled",true);
    $("#search_invoice_button").css("display","none");
    $("#clear_invoice_selected").css("display","inline");
    }

}

@if($isEditMode && $order->invoice_no)
search_invoice();
@endif


</script>
<script>
    let submitted = 0;
    let isSubmitting = false;
    const inputs = document.querySelectorAll("#sale_form input");

    inputs.forEach((input) => {
    input.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {
            // alert("hiu");
        event.preventDefault();  // Prevent form submission
        }
    });
    });

    document.addEventListener("keydown", function (event) {
    if (event.altKey && event.key === "a") {
        event.preventDefault(); // Prevent the default behavior

        // Check if the "save" button is disabled and the gross total value is valid
        const inValid = document.getElementById("saveOrderBtn").disabled;
        const gTotal = document.getElementById("gross_total").value;
        const grossTotalNumber = +gTotal; // Convert to a number
        const form = document.getElementById("sale_form"); // Reference to the form

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

  document.addEventListener("keydown", function (event) {
  if (event.ctrlKey && event.key === "c") {
    event.preventDefault(); // Prevent the default "Save" behavior of the browser
    
    // Set the radio button 'normalOrder' to checked
    const normal = document.getElementById('normalOrder').checked; 
    const pos = document.getElementById('posOrder').checked; 

    if(!normal){
        document.getElementById('normalOrder').checked = true;
        $('.select_party_wrapper').css('display' , 'block');
        const test = document.getElementById("customer_select").focus();
        console.log(test);
    }else{
        document.getElementById('posOrder').checked = true;
    }
    // Trigger a 'click' event on the first element with class 'order-type-item'
    const orderTypeClick = document.getElementsByClassName('order-type-item')[0];
    
    if (orderTypeClick) {
      const clickEvent = new Event('click', { bubbles: true });
      orderTypeClick.dispatchEvent(clickEvent);  // Dispatch the custom click event
    }
  }
});


window.addEventListener('beforeunload', function (event) {
 if(!isSubmitting){
     // Customize the confirmation message
  event.preventDefault(); // Required for some browsers
  event.returnValue = ''; // Display the default confirmation message
  // For most modern browsers, the custom message will not be shown.
  return ''; // Some browsers still show this returned string
 }
});






</script>

<style>
      *{
            outline: none; /* Optional: Remove default focus outline */
        }
    *:focus {
            outline: 2px solid red !important;  /* Optional: Custom focus style */
    }
</style>
@endsection

@endsection

