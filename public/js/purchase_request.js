// Orders JS
$(document).ready(function(){
    calculateOrders();
    var total_amount = 0;
    // Resting Item List 
    function removeItemsFromList(){
        $('#item_selection_list > div').remove();
    }

    function addItemInList(array){
        removeItemsFromList();
        array.forEach(element => {
                   
                   $('#item_selection_list').append(
                   '<div class="selection_list_item" data-id="'+element.barcode+'">'+
                               '<h5>'+element.categories.field.name+' - '+element.categories.category+' - '+element.name+" - " + element.barcode + '</h5>'+
                               '<p>Lorem ipsum dolor sit amet.</p>'+
                           '</div>'
                   );
               });
    }

    $('#item_selection_list').on('click','.selection_list_item',function(){
        // console.log('working');
        var value = $(this).attr('data-id');
        addToCart(value);
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
                url : '/api/items/1/'+id,
                type : 'GET',
                success : function(e){
                        $('#cartList').append(
                        '<tr data-id="'+e.barcode+'" class="itemsInCart">'+
                                    '<td>'+e.categories.field.name+' '+e.categories.category+' '+e.name+'</td>'+
                                    '<td> <input type="hidden" name="item_id[]" value="'+e.id+'">'+
                                    '<select name="uom[]" class="form-control uom" data-id="'+(e.uoms ? e.uoms.base_unit_value : '1')+'" '+(e.uoms == null ? 'readonly' : '')+'>'+
                                    '<option value="1">'+(e.uoms ? e.uoms.uom : 'Default')+'</option>'+    
                                    '<option value="'+(e.uoms ? e.uoms.base_unit_value : 1)+'">'+(e.uoms ? e.uoms.base_unit : 'Default')+'</option>'+    
                                    '</select>'+
                                    '</td>'+
                                    '<td><input name="rate[]" type="number" step="0.01" placeholder="Rate" min="1" class="form-control rate" value="'+e.mrp+'"></td>'+
                                    '<td><input name="qty[]" type="number" step="0.01" placeholder="Qty"  min="1" class="form-control pr_qty" value="'+1+'"></td>'+
                                    '<td><input name="tax[]" type="number" step="0.01" placeholder="Tax" min="0" class="form-control tax" value="'+e.taxes+'"></td>'+
                                    '<td class="total">'+(e.mrp * 1)+'</td>'+
                                    '<td>  <i class="fa fa-trash"></i><td>'+
                        '</tr>'
                        );
                        removeItemsFromList();
                        // console.log(e.uoms.base_unit_value);
                        // swal('product');  
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
                url : '/api/items/1/'+value,
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
    $('#searchItemValue').keyup(function(e){
        var value = $('#searchItemValue').val();
        console.log(value);
        removeItemsFromList();
       if(e.which == 40){
        $.ajax({
            url : '/api/items/0/'+value,
            type: 'GET',
            success : function(res){
                if(res.length < 1){
                    // console.log('not found');
                    removeItemsFromList();
                }else{
                    addItemInList(res);
                } 
            }
        })
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
       $('.g_total').text(grand_total.toFixed(2));
       $('#gross_total').val(grand_total.toFixed(2));

    }, 500);
    
    }

    // Generate Grand Total
    // Calculate on change 
    $('.itemsInCart').on('click', '.rate', function(){
        calculateOrders();
    });
   
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

    $('body').on('click','i.fa.fa-trash',function(){
        $(this).closest('tr').remove();
        calculateOrders();
    })
});
