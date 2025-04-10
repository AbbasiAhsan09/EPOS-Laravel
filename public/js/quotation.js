// Orders JS
$(document).ready(function(){
    var storeId = $("#storeId").val();
    calculateOrders();
    var total_amount = 0;
    $('#product_id').change(function(){
        var id  = $(this).val();
        setTimeout(() => {
            addToCart(id,true);
        }, 500);
    })
    function redirectTosearchItemValue(){
        $('#searchItemValue').focus();
    }
    // Resting Item List 
    function removeItemsFromList(){
        $('#item_selection_list > button').remove();
    }

    function addItemInList(array){
        removeItemsFromList();
        array.forEach(element => {
                   
                   $('#item_selection_list').append(
                   '<button type="button" class="selection_list_item" data-id="'+ element.barcode +'">'+
                   '<h5>'+element.categories.category+' | '+element.name+'</h5>'+                   
                   '<p>Field: '+element.categories.field.name+' | Code: '+ element.barcode + '</p>'+ //    '<p>Lorem ipsum dolor sit amet.</p>'+
                           '</button>'
                   );
               });
    }

    $('#item_selection_list').on('click','.selection_list_item',function(){
        // console.log('working');
        var value = $(this).attr('data-id');
        addToCart(value);
        redirectTosearchItemValue();
    })

    // Adding Product in Order Cart
   function addToCart(id, addMulti){
    // console.log(addMulti);
    if(!addMulti){
        searchAndAppendProduct(id);
    }else{
        
        if($('.itemsInCart').length){
      setTimeout(() => {
        $('.itemsInCart').each(function(){
            var check = $(this).attr('data-id');
            if(check == id){
                var qty = $('td > input.pr_qty',this).val();
                
                $('td > input.pr_qty',this).val((qty *1)+1);
            }else{
                searchAndAppendProduct(id);
                return CheckProductIsExist(id);

                // console.log('run');
                // return false;
            }
            });
      }, 200);
        }else if($('.itemsInCart').length < 1){
            searchAndAppendProduct(id);
        }
    }
    $('#searchItemValue').val('');
    setTimeout(() => {
        removeItemsFromList();

        if($('.itemsInCart').length){
        calculateOrders();
        }

    }, 200);
    }

    // Search and Append Product in Cart
    function searchAndAppendProduct(id){
   
        if (!CheckProductIsExist(id)) {
            $.ajax({
                url : '/api/items/1/'+id+'/'+storeId,
                type : 'GET',
                success : function(e){
                        $('#cartList').append(
                            '<tr data-id="'+e.barcode+'" class="itemsInCart">'+
                            '<td>'+e.categories.field.name+' '+e.categories.category+' '+e.name+''+
                            '<input type="hidden" name="item_id[]" value="'+e.id+'">'+
                            '<input type="hidden" name="uom[]" value="1"></td>'+
                            // '<select name="uom[]" class="form-control uom" data-id="'+(e.uoms ? e.uoms.base_unit_value : '1')+'" '+(e.uoms == null ? 'readonly' : '')+'>'+
                            // '<option value="1">'+(e.uoms ? e.uoms.uom : 'Default')+'</option>'+    
                            // '<option value="'+(e.uoms ? e.uoms.base_unit_value : 1)+'">'+(e.uoms ? e.uoms.base_unit : 'Default')+'</option>'+    
                            // '</select>'+
                            '</td>'+
                            // '<td><input name="bag_size[]" type="number" step="0.01" placeholder="Size" min="0" class="form-control bag_size" value="0"></td>'+
                            // '<td><input name="bags[]" type="number" step="0.001" placeholder="Bags" min="0" class="form-control bags" value="0"></td>'+
                            '<td><input name="rate[]" type="number" step="0.01" placeholder="Rate" min="1" class="form-control rate" value="'+e.tp+'"></td>'+
                            // '<td><input name="mrp[]" type="number" step="0.01" placeholder="MRP" min="1" class="form-control mrp" value="'+e.mrp+'"></td>'+
                            '<td><input name="qty[]" type="number" step="0.01" placeholder="Qty"  min="1" class="form-control pr_qty" value="'+1+'"></td>'+
                            '<td><input name="tax[]" type="number" step="0.01" placeholder="Tax" min="0" class="form-control tax" value="'+e.taxes+'"></td>'+
                            '<td class="total">'+(e.mrp * 1)+'</td>'+
                            '<td>  <i class="fa fa-trash"></i><td>'+
                '</tr>'
                        );
                        removeItemsFromList();
                        console.log(e.uoms.base_unit_value);
                      
                }
            });
           } 
   
    }

    // Check if Product is Already Added
    function CheckProductIsExist(value){
        // console.log(value);
        var check = false;
        $('.itemsInCart').each(function(){
          var barcode = $(this).attr('data-id');
         
            if (value  == barcode ) {
                check = true;
            }
        });

        return check;

    }

    // Adding by barcode scan
    $('#searchItemValue').keypress(function(e){
        if(e.which == 13){
            var value = $(this).val();
            $.ajax({
                url : '/api/items/1/'+value+'/'+storeId,
                type : 'GET',
                success : function(res){
                    // console.log(res);
                    if(res.id){
                        setTimeout(() => {
                            addToCart(res.barcode,true);
                        }, 300);
                    }else{
                        swal('Not Found','Item was not found ','error');
                        $("#searchItemValue").val('');
                    }
                }
            })
        }
    })

    // Caling Ajax Query for getting products
    var timer; // Declare a variable to hold the timer ID
    var previousValue = ''; // Variable to store the previous input value
    
    $('#searchItemValue').keyup(function(e){
        var currentValue = $('#searchItemValue').val();
        
        // Check if the input value has changed significantly
        if (currentValue !== previousValue) {
            clearTimeout(timer); // Clear the previous timer
    
            timer = setTimeout(function(){
                var value = $('#searchItemValue').val();
                removeItemsFromList();
                
                if(currentValue){
                    // Perform AJAX request
                $.ajax({
                    url : '/api/items/0/'+value+'/'+storeId,
                    type: 'GET',
                    success : function(res){
                        if(res.length < 1){
                            removeItemsFromList();
                        }else{
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
    function calculateOrders(){
        // Step One
        var grand_total = 0;
      setTimeout(() => {
        $('.itemsInCart').each(function(){
            var base_unit = $('td > select',this).val();
            var rate = $('td > input.rate',this).val();
            var qty = $('td > input.pr_qty',this).val();
            var tax = $('td > input.tax',this).val();
            
            var total = (((rate * 1)  +  ((rate/100) * tax)) * qty); 
            grand_total += total;

            $('td.total',this).text(total.toFixed(2));
        })
        // console.log(grand_total);
        total_amount =  grand_total;
        $('.foot_g_total').text(grand_total.toFixed(2));
       $('#gross_total').val(grand_total.toFixed(2));
        checkDiscount();

    }, 500);
    
    validationForSubmit();
    }

    // Generate Grand Total
    // Calculate on change 
    $('.itemsInCart').on('click', '.rate', function(){
        calculateOrders();
        console.log('input');
    });
    $('.order-type-item').click(function(){
        orderType();
    });
    function orderType(){
        if($('#posOrder').is(':checked')){
            $('.select_party_wrapper').css('display' , 'block');
            $('.select_vendor_wrapper').css('display' , 'none');
            $('#customer_select').prop('required' , true);
            $('#customer_select').prop('disabled' , false);
            $('#vendor_select').prop('disabled' , true);
            $('#vendor_select').val('');
            $('.other-methods').css('display' , 'inline-block');
            // $('.other-methods input').prop('checked',false);
        }else{
            $('.select_party_wrapper').css('display' , 'none');
            $('.select_vendor_wrapper').css('display' , 'block');
            $('#customer_select').val('');
            $('.other-methods').css('display' , 'inline-block')
            $('#vendor_select').prop('required' , true);
            $('#customer_select').prop('disabled' , true);
            $('#vendor_select').prop('disabled' , false);
        }
        
    }
    
    orderType();
    $('body').keyup(function(){
        calculateOrders();
    })

    $('body').click(function(){
        calculateOrders();
    })
    
    $('body').on('change','select.uom',function(){
        console.log('working');
        var value = $(this).val();
        var parent = $(this).parent().parent();
        var rate = parent.find('input.rate').val();
        var base_static = $(this).attr('data-id');
        if(value != 1){
          var results = rate / value;
          parent.find('input.rate').val(results.toFixed(2));
          
        }else{
            var results = rate  * base_static;
          parent.find('input.rate').val(results.toFixed(2));
        }
          calculateOrders();
    })
    function validationForSubmit(){
        var returningVal = $('#returning-amount').val();
        if(returningVal < 0.00 && $('#posOrder').is(':checked')){
            $('#saveOrderBtn').prop('disabled' , false);
        }else{
            $('#saveOrderBtn').prop('disabled' , false);
        }
    }
    $('#discount').keyup(function(){
      checkDiscount();
    })
    function checkDiscount(){
        var checkVal = $('#discount').val();
        var other_charges = $('#otherCharges').val();
        var discount = 0;
        var total_after_disc_other_charges = 0;
        var received = $('#received-amount').val();

        checkVal = checkVal.charAt(0);
        if(checkVal == '%'){
            $('#discountSection').css('display' , 'block');
            var dis_val = $('#discount').val().substring(1);

            if(dis_val > 100){
                alert('Discount Value Cannot Be Greater Than Hundred');
                $('#discount').val('%100');
            }
            discount =  ((total_amount / 100) * (dis_val *1));
            $('#discountValue').val(discount.toFixed(2));

        }else{
            $('#discountSection').css('display' , 'none');
            discount = $('#discount').val();
        } 
        total_after_disc_other_charges = (((total_amount*1) - (discount*1)) + (other_charges *1));
        $('.g_total').text(total_after_disc_other_charges.toFixed(2));
        var returningAmount = received - total_after_disc_other_charges;
        $('#returning-amount').val(returningAmount.toFixed(0));
    }

    $('body').on('click','i.fa.fa-trash',function(){
        $(this).closest('tr').remove();
        calculateOrders();
    })
});
