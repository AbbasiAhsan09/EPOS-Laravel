@extends('layouts.app')
@section('content')
@include("includes.spinner")
@php
     $isEditMode = isset($history->id) && !empty($history->id);
 @endphp
<div class="container-fluid">
    <div class="general-form-wrapper">
        <h4 class="form-title">{{$isEditMode  ? "Edit" : "Create" }}  Labour Bill {{$isEditMode ? ": ".$history->doc_no : ''}}</h4>
        
        @if ($isEditMode)
        
        <form action="{{route("labour-work.update", $history->id)}}" id="labour_history_form" method="POST">
            
        @else
            <form action="{{route("labour-work.store")}}" id="labour_history_form" method="POST">
            
        @endif
        {{-- csrf --}}
        @csrf
        @method($isEditMode ? "put" : "post")
            <div class="general-form">
                <div class="row">
                    <div class="col-lg-2">
                        <div class="select_party_wrapper">
                            <h4 class="order_section_sub_title">
                                Select Labour
                            </h4>
                            <div class="select_party">
                                
                                <div class="input-group input-group-outline">
                                    <select name="labour_id" class="form-control" required>
                                        <option value="">Select Labour</option>
                                        @foreach ($labours as $labour)
                                            <option value="{{$labour->id}}" {{$isEditMode && $history->labour_id === $labour->id ? 'selected' : '' }}>{{$labour->name}}</option>
                                        @endforeach
                                    </select>
                                  </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <div class="select_party_wrapper">
                            <h4 class="order_section_sub_title">
                                Open Date
                            </h4>
                            <div class="select_party"> 
                                <div class="input-group input-group-outline">
                                    <input type="date" name="start_date" 
                                    value="{{$isEditMode ? $history->start_date : date("Y-m-d",time())}}"
                                    class="form-control" id="start_date" required>
                                  </div> 
                            </div>
                        </div>
                    </div>
    
                    <div class="col-lg-2">
                        <div class="select_party_wrapper">
                            <h4 class="order_section_sub_title">
                                Closed Date
                            </h4>
                            <div class="select_party"> 
                                <div class="input-group input-group-outline">
                                    <input type="date" name="end_date" 
                                    value="{{$isEditMode ? $history->end_date : null}}"
                                    class="form-control" id="end_date">
                                  </div> 
                            </div>
                        </div>
                    </div>
    
                    <div class="col-lg-1">
                        <div class="select_party_wrapper">
                            <h4 class="order_section_sub_title">
                                Paid
                            </h4>
                            <div class="select_party"> 
                                <div class="input-group input-group-outline">
                                    <select id="is_paid_selection" name="is_paid"  
                                    
                                    class="form-control {{$isEditMode  && $history->is_paid ? 'readonly' : '' }}  ">
                                        <option value="0">No</option>
                                        <option value="1" {{$isEditMode  && $history->is_paid ? 'selected' : '' }}>Yes</option>
                                    </select>
                                  </div> 
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2" id="paid_date">
                        <h4 class="order_section_sub_title">
                           Paid On
                        </h4>
                        <div class="input-group input-group-outline">
                        <input type="date" name="paid_date" value="{{$isEditMode && $history->is_paid && $history->paid_date ? $history->paid_date : '' }}" {{$isEditMode && $history->is_paid && $history->paid_date ? 'readonly' : ''}} id="paid_date_input"  class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <h4 class="order_section_sub_title">
                            Net. Total
                        </h4>
                        <h3>{{ConfigHelper::getStoreConfig()["symbol"]}}<span id="grand_total">{{$isEditMode ? number_format($history->net_total,2) : "0.00"}}</span></h3>
                    </div>
                </div>
                <hr>
                <div class="work-history-table-wrapper">
                    <table class=" table table-sm-responsive table-striped table-bordered" border="2" id="history-table">
                        <thead>
                            <th width="100px">Date</th>
                            <th>Description</th>
                            <th width="150px">Rate</th>
                            <th width="150px">Qty</th>
                            <th width="200px">Total</th>
                            <th width="100px">Action</th>
                        </thead>
                        <tbody>
                          @if ($isEditMode && $history->items->count() > 0)
                              @foreach ($history->items as $key => $item)
                              <tr>
                            
                                <td>
                                    <input type="date"  class="form-control" name="date[]" value="{{$item->date}}" required>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="description[]" value="{{$item->description}}">
                                </td>
                                <td>
                                    <input type="number" step="0.01" value="{{$item->rate}}" min="1" class="form-control"  name="rate[]" required>
                                </td>
                                <td>
                                    <input type="number" step="0.01" value="{{$item->qty}}"  min="1" class="form-control" name="qty[]" required>
                                </td>
                                <td>
                                <strong>Rs.<span class="item-total">{{number_format($item->total,2)}}</span></strong>
                                </td>
                                <td>
                                    <div class="button-group">
                                        <button type="button" class="btn btn-primary btn-sm add-row">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                        @if ($key > 0)
                                        <button type="button" class="btn btn-danger btn-sm remove-row">
                                            <i class="fa fa-close"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                              @endforeach
                          @else
                          <tr>
                            
                            <td>
                                <input type="date" class="form-control" name="date[]" value="{{date("Y-m-d", time())}}" required>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="description[]">
                            </td>
                            <td>
                                <input type="number" step="0.01" value="0" min="1" class="form-control" name="rate[]" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" value="0" min="1" class="form-control" name="qty[]" required>
                            </td>
                            <td>
                            <strong>Rs.<span class="item-total">1000</span></strong>
                            </td>
                            <td>
                                <div class="button-group">
                                    <button type="button" class="btn btn-primary btn-sm add-row">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                
                                </div>
                            </td>
                        </tr>
                          @endif
                        
                        </tbody>
                    </table>
                </div>
                <h6 class="text-right p-2">Total : {{ConfigHelper::getStoreConfig()["symbol"]}}<span id="sub-total">{{$isEditMode ? number_format($history->total,2) : "0.00"}}</span></h6>
                <div class="bottom-work-history p-2">
                    <div class="row g-2">
                        <div class="col-lg-2">
                            <div class="select_party_wrapper">
                                <h4 class="order_section_sub_title">
                                    Other Charges
                                </h4>
                                <div class="input-group input-group-outline">
                                <input type="number" name="other_charges" value="{{$isEditMode ? $history->other_charges : 0}}" id="other_charges" class="form-control" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-2">
                            <div class="select_party_wrapper">
                                <h4 class="order_section_sub_title">
                                    bonus & Allownces
                                </h4>
                                <div class="input-group input-group-outline">
                                <input type="number" name="bonus" id="bonus" value="{{$isEditMode ? $history->bonus : 0}}" class="form-control" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8">
                            <div class="select_party_wrapper">
                                <h4 class="order_section_sub_title">
                                    Extra Notes
                                </h4>
                                <div class="input-group input-group-outline">
                                <input type="text" name="notes" id="" value="{{$isEditMode ? $history->notes : ''}}" class="form-control" >
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <button id="save_button" disabled class="btn btn-primary btn-block w-100">{{$isEditMode ? "Update" : "Save"}}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
    // Function to calculate the total for each row
    function calculateTotal(row) {
        let rate = parseFloat($(row).find('input[name="rate[]"]').val()) || 0;
        let qty = parseFloat($(row).find('input[name="qty[]"]').val()) || 0;
        let total = rate * qty;
        $(row).find('.item-total').text(total.toFixed(2));
    }

    function format_currency(amount) {
    return amount.toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    $(document).on('click change input keyup', '*', function (event) {
        calculateFullTotal(); // Call the function when any event occurs
    });

    let submitting = false;
    $("#labour_history_form").on("submit", function(){
        const form = document.getElementById("labour_history_form");
        
        if(form.checkValidity() && !submitting){
            submitting = true;
            showSpinner()
        }else{
            hideSpinner()
        }
    })
    function calculateFullTotal() {
    var trs = $("#history-table tbody tr"); // Get all rows within the table body
    let total = 0;
    let grand_total = 0;
    trs.each(function (i, tr) {
        // Find the rate and quantity inputs in the current row
        const rate = parseFloat($(tr).find('input[name="rate[]"]').val()) || 0;
        const qty = parseFloat($(tr).find('input[name="qty[]"]').val()) || 0;

        // Calculate the total for this row
        const rowTotal = rate * qty;

        // Update the total in the row's .item-total element
        $(tr).find('.item-total').text(format_currency(rowTotal));

        // Add the row total to the grand total
        total += rowTotal;
    });

    $('#sub-total').text(format_currency(total));
    const bonus_amount = +$("#bonus").val() ?? 0;
    const other_charges = +$("#other_charges").val() ?? 0;
    grand_total = total + bonus_amount + other_charges;
    $("#grand_total").text(format_currency(grand_total));
    if(!grand_total || !total){
        $("#save_button").prop("disabled",true);
    }else{
        $("#save_button").prop("disabled",false);
    }


}
    // Function to add a new row
    function addRow() {
        let newRow = `
            <tr>
                <td>
                    <input type="date" value="{{date("Y-m-d", time())}}" class="form-control" name="date[]" required>
                </td>
                <td>
                    <input type="text" class="form-control" name="description[]">
                </td>
                <td>
                    <input type="number" step="0.01" min="1" class="form-control" value="0" name="rate[]" required>
                </td>
                <td>
                    <input type="number" step="0.01" min="1" class="form-control" value="0" name="qty[]" required>
                </td>
                <td>
                    <strong>Rs.<span class="item-total">0.00</span></strong>
                </td>
                <td>
                    <div class="button-group">
                        <button type="button" class="btn btn-primary btn-sm add-row">
                            <i class="fa fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm remove-row">
                            <i class="fa fa-close"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        $('table tbody').append(newRow);
    }

    // Event listener for adding new rows
    $(document).on('click', '.add-row', function (e) {
        e.preventDefault();
        addRow();
    });

    // Event listener for removing rows
    $(document).on('click', '.remove-row', function (e) {
        e.preventDefault();
        if ($('table tbody tr').length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert('There must be at least one row.');
        }
    });


    // Event listener for recalculating total when rate or quantity changes
    $(document).on('input', 'input[name="rate[]"], input[name="qty[]"]', function () {
        let row = $(this).closest('tr');
        calculateTotal(row);
    });

    // Initial calculation for the first row
    calculateTotal($('table tbody tr').first());
});


$(document).ready(function() {
    
    $("#is_paid_selection").on("change", function() {
        paid_selection();
    });

    function paid_selection() {
        const paid = $("#is_paid_selection").val();

        if (+paid) {
            
            $("#paid_date").show();
            $("#paid_date_input").prop("disabled", false).prop("required",true)
        } else {
            $("#paid_date").hide();
            $("#paid_date_input").prop("disabled", true).prop("required",false);
        }
    }


    paid_selection();
});


</script>
@endsection