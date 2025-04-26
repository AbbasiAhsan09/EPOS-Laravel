// Orders JS

$(document).on("change", ".unit_id", function () {
    
    var selectedUnit = $(this).find("option:selected");
    var conversionRate = selectedUnit.data("conversion_rate") || 1;
    var rate = selectedUnit.data("rate") || 1;
    var cost = selectedUnit.data("cost") || 1;
    var selectedValue = $(this).val();
    

    // Example logic (uncomment if needed)
    var parentRow = $(this).closest("tr");
    var qtyInput = parentRow.find(".pr_qty");
    var rateInput = parentRow.find(".rate");

    rateInput.val(rate);
});


$(document).ready(function () {




    var storeId = $("#storeId").val();
    var total_amount = 0;
    $('#product_id').change(function () {
        var id = $(this).val();
        setTimeout(() => {
            addToCart(id, true);
        }, 500);
    })
    function redirectTosearchItemValue() {
        $('#searchItemValue').focus();
    }
    // Resting Item List 
    function removeItemsFromList() {
        $('#item_selection_list > button').remove();
    }

    function addItemInList(array) {
        removeItemsFromList();
        array.forEach(element => {

            $('#item_selection_list').append(

                '<button type="button" class="selection_list_item" data-id="' + element.barcode + '" >' +
                '<h5>' + element.categories.category + ' | ' + element.name + '</h5>' +
                '<p>Field : ' + element.categories.field.name + " | Code : " + element.barcode + '</p>' +
                //    '<p>Lorem ipsum dolor sit amet.</p>'+
                '</button>'
            );
        });
    }

    $('#item_selection_list').on('click', '.selection_list_item', function () {
        var value = $(this).attr('data-id');
        addToCart(value);
        removeItemsFromList();
        redirectTosearchItemValue();
    })

    // Adding Product in Order Cart
    function addToCart(id, addMulti) {
        if (!addMulti) {
            searchAndAppendProduct(id);
        } else {

            if ($('.itemsInCart').length) {
                setTimeout(() => {
                    $('.itemsInCart').each(function () {
                        var check = $(this).attr('data-id');
                        if (check == id) {
                            var qty = $('td > input.pr_qty', this).val();

                            $('td > input.pr_qty', this).val((qty * 1) + 1);
                        } else {
                            searchAndAppendProduct(id);
                            return CheckProductIsExist(id);

                        }
                    });
                }, 200);
            } else if ($('.itemsInCart').length < 1) {
                searchAndAppendProduct(id);
            }
        }
        $('#searchItemValue').val('');
        setTimeout(() => {
            removeItemsFromList();

            if ($('.itemsInCart').length) {
                calculateOrders();
            }

        }, 200);
    }

    // Search and Append Product in Cart
    function searchAndAppendProduct(id) {
        var show_tp_in_order_form = $("#show_tp_in_order_form").val();
        var show_bag_sizing = $("#show_bag_sizing").val();

        if (!CheckProductIsExist(id)) {
            $.ajax({
                url: '/api/items/1/' + id + '/' + storeId,
                type: 'GET',
                success: function (e) {
                   
                    let defaultRate = e.mrp;
                    
                    if(e.unit_type_id && e.product_units && e.product_units.length > 0) {
                        defaultRate = e.product_units?.find((unit) => unit.default === true)?.unit_rate || e.mrp;
                        
                    }

                    $('#cartList').append(
                        '<tr data-id="' + e.barcode + '" class="itemsInCart">' +
                        '<td>' + e.name + '</td>' +
                        '<td> <input type="hidden" name="item_id[]" value="' + e.id + '">'
                        // +
                        // '<select name="uom[]" class="form-control uom" data-id="'+(e.uoms ? e.uoms.base_unit_value : '1')+'" '+(e.uoms == null ? 'readonly' : '')+'>'+
                        // '<option value="1">'+(e.uoms ? e.uoms.uom : 'Default')+'</option>'+    
                        // '<option value="'+(e.uoms ? e.uoms.base_unit_value : 1)+'">'+(e.uoms ? e.uoms.base_unit : 'Default')+'</option>'+    
                        // '</select>'
                        +
                        `   
                                    <select class="form-control unit_id" name="unit_id[]" data-unit_type_id="${e.unit_type_id}" ${!e.unit_type_id ? 'readonly' : ''}>
                                        ${!e.unit_type_id ? '<option value="">Single</option>' : ''}
                                        ${e.unit_type_id && e.product_units && e.product_units.length > 0
                            ? e.product_units
                                .slice() // make a copy so original isn't mutated
                                .sort((a, b) => (b.default === true) - (a.default === true)) // sort so 'default: true' comes first
                                .map(product_unit => `<option value="${product_unit.unit.id}" 
                                                    data-conversion_rate="${product_unit.conversion_rate}"
                                                    data-rate="${product_unit.unit_rate}"
                                                    data-cost="${product_unit.unit_cost}"
                                                    >${product_unit.unit.symbol}</option>`).join('')
                            : ''
                        }
                                    </select>
                                    `+
                        '</td>' + ((1 * show_tp_in_order_form) ? '<td><input readonly disabled type="number" step="0.01" placeholder="TP" min="0.01" class="form-control tp" value="' + e.tp + '"></td>' : '') +
                        `${ (1 * show_bag_sizing)  ? '<td><input name="bag_size[]" type="number" step="0.01" placeholder="Size" min="0" class="form-control bag_size" value="0"></td>' : ''}`+
                        `${(1 * show_bag_sizing)?'<td><input name="bags[]" type="number" step="0.01" placeholder="Bags" min="0" class="form-control bags" value="0"></td>':''}`+
                        '<td><input name="rate[]" type="number" step="0.01" placeholder="Rate" min="0.01" class="form-control rate" value="' + defaultRate + '"></td>' +
                        '<td><input name="qty[]" type="number" step="0.01" placeholder="Qty"  min="1" class="form-control pr_qty"  data-item-id="' + e.id + '" value="' + 1 + '"></td>' +
                        '<td><input name="tax[]" type="number" step="0.01" placeholder="Tax" min="0" class="form-control tax" value="' + e.taxes + '"></td>' +
                        '<td class="total">' + (e.mrp * 1) + '</td>' +
                        '<td>  <i  tabindex="0" class="fa fa-trash focusable"></i><td>' +
                        '</tr>'
                    );
                    removeItemsFromList();

                }
            });
        }

    }



    // Check if Product is Already Added
    function CheckProductIsExist(value) {

        var check = false;
        $('.itemsInCart').each(function () {
            var barcode = $(this).attr('data-id');

            if (value == barcode) {
                check = true;
            }
        });

        return check;

    }

    // Adding by barcode scan
    $('#searchItemValue').keypress(function (e) {
        if (e.which == 13) {
            var value = $(this).val();
            $.ajax({
                url: '/api/items/1/' + value + '/' + storeId,
                type: 'GET',
                success: function (res) {

                    if (res.id) {
                        setTimeout(() => {
                            addToCart(res.barcode, true);
                        }, 300);
                    } else {
                        swal('Not Found', 'Item was not found ', 'error');
                        $("#searchItemValue").val('');
                    }
                }
            })
        }
    })

    // Caling Ajax Query for getting products
    var timer; // Declare a variable to hold the timer ID
    var previousValue = ''; // Variable to store the previous input value

    $('#searchItemValue').keyup(function (e) {
        var currentValue = $('#searchItemValue').val();

        // Check if the input value has changed significantly
        if (currentValue !== previousValue) {
            clearTimeout(timer); // Clear the previous timer

            timer = setTimeout(function () {
                var value = $('#searchItemValue').val();
                removeItemsFromList();

                if (currentValue) {
                    // Perform AJAX request
                    $.ajax({
                        url: '/api/items/0/' + value + '/' + storeId,
                        type: 'GET',
                        success: function (res) {
                            if (res.length < 1) {
                                removeItemsFromList();
                            } else {
                                addItemInList(res);
                            }
                        }
                    });
                }

                previousValue = currentValue; // Update the previous value
            }, 500); // Set a delay of 500 milliseconds
        }
    });

    // Calculation New Orders
    function calculateOrders() {
        // Step One
        var grand_total = 0;
        setTimeout(() => {
            $('.itemsInCart').each(function () {
                var base_unit = $('td > select', this).val();
                var rate = $('td > input.rate', this).val();
                var qty = $('td > input.pr_qty', this).val();
                var tax = $('td > input.tax', this).val();

                var total = (((rate * 1) + ((rate / 100) * tax)) * qty);
                grand_total += total;

                $('td.total', this).text(total.toFixed(2));
            })

            total_amount = grand_total;
            $('.foot_g_total').text(grand_total.toFixed(2));
            $('#gross_total').val(grand_total.toFixed(2));
            checkDiscount();
        }, 500);
        CalculateRecievedAmountInPercentage();

        validationForSubmit();
    }

    // Generate Grand Total
    // Calculate on change 
    $('.itemsInCart').on('click', '.rate', function () {
        calculateOrders();

    });
    $('.order-type-item').click(function () {
        orderType();
    });
    function orderType() {
        $is_accounting_module = $("#is_accounting_module").val();
        // alert($is_accounting_module);
        if ($('#posOrder').is(':checked')) {
            $('.select_party_wrapper').css('display', 'none');
            $('#customer_select').prop('required', false);
            $('#customer_select').val('');
            $(".payment_methods_wrapper_container").css("display", 'block');
            $(".payment_methods input").prop("disabled", false);
            $('.other-methods').css('display', 'none');
            $('.other-methods input').prop('checked', false);
            $("#received-amount").prop("disabled", false);
        } else {
            $('.select_party_wrapper').css('display', 'block');
            $('#customer_select').prop('required', true);
            if ($is_accounting_module) {
                $('.other-methods').css('display', 'none')
                $(".payment_methods input").prop("disabled", true);
                $(".payment_methods_wrapper_container").css("display", 'none');
                $("#received-amount").prop("disabled", true);

            } else {
                $(".payment_methods input").prop("disabled", false);
                $("#received-amount").prop("disabled", false);
                $('.other-methods').css('display', 'inline-block')
            }
        }

    }

    orderType();
    $('body').keyup(function () {
        calculateOrders();
    })

    $('body').click(function () {
        calculateOrders();
    })

    $('body').on('change', 'select.uom', function () {

        var value = $(this).val();
        var parent = $(this).parent().parent();
        var rate = parent.find('input.rate').val();
        var base_static = $(this).attr('data-id');
        if (value != 1) {
            var results = rate / value;
            parent.find('input.rate').val(results.toFixed(2));

        } else {
            var results = rate * base_static;
            parent.find('input.rate').val(results.toFixed(2));
        }
        calculateOrders();
    })
    function validationForSubmit() {

        var returningVal = $('#returning-amount').val();
        if (returningVal < 0.00 && $('#posOrder').is(':checked')) {
            $('#saveOrderBtn').prop('disabled', true);
        } else {
            $('#saveOrderBtn').prop('disabled', false);
        }
    }
    $('#discount').keyup(function () {
        checkDiscount();
    })
    function checkDiscount() {
        var checkVal = $('#discount').val();
        var other_charges = $('#otherCharges').val();
        var discount = 0;
        var total_after_disc_other_charges = 0;
        var received = $('#received-amount').val();

        checkVal = checkVal.charAt(0);
        if (checkVal == '%') {
            $('#discountSection').css('display', 'block');
            var dis_val = $('#discount').val().substring(1);

            if (dis_val > 100) {
                alert('Discount Value Cannot Be Greated Than Hundred');
                $('#discount').val('%100');
            }
            discount = ((total_amount / 100) * (dis_val * 1));
            $('#discountValue').val(discount.toFixed(2));

        } else {
            $('#discountSection').css('display', 'none');
            discount = $('#discount').val();
        }
        total_after_disc_other_charges = (((total_amount * 1) - (discount * 1)) + (other_charges * 1));
        $('.g_total').text(total_after_disc_other_charges.toFixed(2));
        var returningAmount = received - total_after_disc_other_charges;
        $('#returning-amount').val(returningAmount.toFixed(0));
    }

    $('body').on('click', 'i.fa.fa-trash', function () {
        $(this).closest('tr').remove();
        calculateOrders();
    });

    $('body').on('keypress', 'i.fa.fa-trash', function () {
        $(this).closest('tr').remove();
        calculateOrders();
    });

    var checkInventory = $('#checkInventory').val();
    checkInventory = +checkInventory;

    // $('body').on('change', '.uom', function () {

    //     var curr = $(this);
    //     var qty_ins = $(this).parent().parent().find('.pr_qty');
    //     var qty = $(this).parent().parent().find('.pr_qty').val();
    //     var is_base_unit = curr.val() > 1 ? true : false;
    //     var item_id = $(this).parent().parent().find('.pr_qty').attr('data-item-id');
    //     var unit_value = $(this).parent().parent().find('.unit_id');

    //     console.log('unit_value', unit_value)
    //     var calcultationQty = 0;

    //     if (checkInventory) {
    //         $.ajax({
    //             url: '/check-inventory/' + item_id + '/' + (is_base_unit ? 1 : 0),
    //             type: 'GET',
    //             success: function (res) {

    //                 if ((res * 1) < qty) {
    //                     const lowInventoryCheck = +$("#lowInventoryCheck").val();

    //                     if (!lowInventoryCheck) {
    //                         swal('Low Quantity', `Required Qty is Not Available. Available Qty is ${Math.round(res)}`, 'error');
    //                         qty_ins.val(Math.round(res));
    //                         if ((res * 1) === 0) {
    //                             curr.parent().parent().closest('tr').remove();

    //                         }
    //                     } else {
    //                         swal('Low Quantity', `Required Qty is Not Available. Available Qty is ${Math.round(res)} this will be show as -Qty in inventory`, 'error');
    //                     }

    //                 }
    //             }
    //         });
    //     }
    // })

    $('body').on('change', '.pr_qty, .unit_id', function () {
       
        var qty_input = $(this).parent().parent().find('.pr_qty');
        var item_id = $(this).parent().parent().find('.pr_qty').attr('data-item-id');
        var unit_id = $(this).parent().parent().find('.unit_id').val();
        var required_qty = qty_input.val();
        
        var allowLowInventory = $('#lowInventoryCheck').val();
        
        if(!(1*allowLowInventory) && required_qty && (1*required_qty) > 0){
            $.ajax({
                url: `/check-inventory/?product=${item_id }&qty=${required_qty}&unit_id=${unit_id}`  ,
                type: 'GET',
                success: function (res) {
                    if(res && res.name){
                        const avl_stock = 1*res.avl_stock ;
                        const low_inventory = res.low_stock
                    
                        if(low_inventory){
                            swal('Low Quantity', `Required Qty is Not Available. Available Qty is ${avl_stock}`, 'error');
                            qty_input.val(avl_stock);
                        }
    
                    }
                }
            });
        }

        
    })


    // $('body').on('keyup', '.pr_qty', function () {
    //     var curr = $(this);
    //     var qty = $(this).val();
    //     var is_base_unit = curr.parent().parent().find('.uom').val() > 1 ? true : false;
    //     var item_id = curr.attr('data-item-id');

    //     if (checkInventory != 0) {
    //         $.ajax({
    //             url: '/check-inventory/' + item_id + '/' + (is_base_unit ? 1 : 0),
    //             type: 'GET',
    //             success: function (res) {

    //                 if ((res * 1) < qty) {
    //                     const lowInventoryCheck = +$("#lowInventoryCheck").val();
    //                     if (!lowInventoryCheck) {
    //                         swal('Low Quantity', `Required Qty is Not Available. Available Qty is ${Math.round(res)}`, 'error');
    //                         curr.val(Math.round(res));
    //                         if ((res * 1) === 0) {
    //                             curr.parent().parent().closest('tr').remove();

    //                         }
    //                     } else {
    //                         swal('Low Quantity', `Required Qty is Not Available. Available Qty is ${Math.round(res)} this will be show as -Qty in inventory`, 'error');
    //                     }
    //                 }
    //             }
    //         });
    //     }
    // })
    //   Calculate Recieved Amount in percentage
    
    function CalculateRecievedAmountInPercentage() {
        var rcvdAmountElem = $("#received-amount");
        var rcvPrcentage = $("#received-amount-in-percentage").val();

        if (typeof (1 * rcvPrcentage) === 'number' && rcvPrcentage > 0) {
            rcvPrcentage = 1 * rcvPrcentage;
            var bill_total = $(".g_total").text();
            var calulatedAmountRecieved = ((1 * bill_total / 100) * rcvPrcentage).toFixed(2);
            

            rcvdAmountElem.prop('readonly', true);
            rcvdAmountElem.val(calulatedAmountRecieved);

            if (rcvPrcentage > 100) {
                $("#received-amount-in-percentage").val(100);
                CalculateRecievedAmountInPercentage();
            }

        } else {
            rcvdAmountElem.prop('readonly', false);
            if (rcvPrcentage === '0') {
                rcvdAmountElem.val(0);

            }
        }

    }
});
