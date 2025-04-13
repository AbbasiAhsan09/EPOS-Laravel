@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="card p-5">
            <div class="row">
                <div class="col-6">
                    <h4>{{ $isEditMode ? 'Edit Product' : 'Add Product' }}</h4>
                </div>
                <div class="col-6">
                    <a href="/products" class="btn btn-primary float-end">Back</a>
                </div>
            </div>
                @if ($isEditMode && $product)
                    <form action="{{ route('update.product', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                @else
                <form action="{{ route('add.product') }}" method="POST" enctype="multipart/form-data" id="new_product">
                    @csrf
                @endif
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Name *</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" value="{{$isEditMode && $product ? $product->name :  old('product') }}" id="product_name"
                                name="product" required>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        @livewire('field-category')
                    </div>
                    <div class="col-lg-6">
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="">Code *</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" name="code" id="product_code"
                                        value="{{$isEditMode && $product ? $product->barcode  :  old('code') }}" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="">Brand </label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" name="brand" value="{{$isEditMode && $product ? $product->brand :  old('brand') }}">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="">Unit Type</label>
                                <div class="input-group input-group-outline">
                                    <select name="unit_type_id" id="unit_type_id" class="form-control">
                                        <option value="">Single Unit</option>
                                        @foreach ($unit_types as $unit_type)
                                            <option value="{{ $unit_type->id }}"  {{ $isEditMode && $product && $product->unit_type_id === $unit_type->id ? 'selected' : '' }}>{{ $unit_type->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="">TAX % </label>
                                <div class="input-group input-group-outline">
                                    <input type="number" step="0.01" class="form-control" name="tax"
                                        value="{{ $isEditMode && $product ? $product->tax : old("tax") }}" min="0">
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <label for="">Sale Price </label>
                                <div class="input-group input-group-outline">
                                    <input type="number" step="0.01" class="form-control" name="mrp"
                                      
                                        value="{{ $isEditMode && $product ? $product->mrp : old("mrp") }}" 
                                         min="0">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <label for="">Cost *</label>
                                <div class="input-group input-group-outline">
                                    <input type="number" step="0.01" class="form-control" name="tp"
                                     
                                        value="{{ $isEditMode && $product ? $product->tp : old("tp") }}" 
                                         min="0">
                                </div>
                            </div>


                            <div class="col-lg-6">
                                <label class="form-label">Opening Inventory </label>
                                <div class="input-group input-group-outline">
                                    <input type="number" class="form-control" step="0.01" name="opening_stock"
                                        
                                        value="{{ $isEditMode && $product ? $product->opening_stock : old("opening_stock") }}" 
                                         min="0"
                                        onfocus="focused(this)" onfocusout="defocused(this)">
                                </div>

                            </div>

                            <div class="col-lg-6">
                                <label class="form-label">Opening Stock Unit Cost </label>
                                <div class="input-group input-group-outline">
                                    <input type="number" class="form-control" step="0.01" name="opening_stock_unit_cost"
                                       
                                        value="{{ $isEditMode && $product ? $product->opening_stock_unit_cost : old("opening_stock_unit_cost") }}" 

                                        min="0" onfocus="focused(this)" onfocusout="defocused(this)">
                                </div>

                            </div>

                            <div class="col-lg-12">
                                <label class="form-label">Low Stock Alert </label>
                                <div class="input-group input-group-outline">
                                    <input type="number" class="form-control" name="low_stock"
                                        value="{{ $isEditMode && $product ? $product->low_stock : old("low_stock") }}" 
                                         min="0"
                                        onfocus="focused(this)" onfocusout="defocused(this)">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="container">
                            <div class="avatar-upload">
                                <div class="avatar-edit">
                                    <input type='file' id="imageUpload" name="image" class="imageUpload"
                                        accept=".png, .jpg, .jpeg" />
                                    <label for="imageUpload"></label>
                                </div>
                                <div class="avatar-preview">
                                    <div class="imagePreview"
                                        style="background-image: url(https://www.trianglelearningcenter.org/wp-content/uploads/2020/08/placeholder.png);">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (isset($dynamicFields) && count($dynamicFields->fields))
                        @foreach ($dynamicFields->fields as $dynamicField)
                            <div class="col-lg-6">
                                <label class="form-label">{{ $dynamicField->label }} </label>
                                <div class="input-group input-group-outline">
                                    {{-- <textarea name="description" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)" rows="3">{{old('description')}}</textarea> --}}
                                    <input
                                        type="{{ $dynamicField->type === 'input' && $dynamicField->datatype === 'string' ? 'text' : 'number' }}"
                                        class="form-control" name="dynamicFields[][{{ $dynamicField->name }}]"
                                        {{ $dynamicField->required ? 'required' : '' }}
                                        value="{{ old('opening_stock') ? old('opening_stock') : 0 }}" min="0"
                                        onfocus="focused(this)" onfocusout="defocused(this)">

                                </div>
                            </div>
                        @endforeach
                    @endif
                    <div class="col-lg-12">
                        <label class="form-label">Description </label>
                        <div class="input-group input-group-outline">
                            <textarea name="description" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)"
                                rows="3">{{  $isEditMode && $product ? $product->description : old('description') }}</textarea>

                        </div>
                    </div>
                    <div class="col-lg-12">
                        
                        <table class="mt-5 table table-bordered table-sm table-responsive" id="unit_table">
                            <thead>
                                <th>Unit</th>
                                <th>Convert To</th>
                                <th>Conversion Rate</th>
                                <th>Cost</th>
                                <th>Rate</th>
                                <th>Barcode</th>
                                <th>Active</th>
                                <th>Default</th>
                            </thead>
                            <tbody>
                                {{-- @dump($isEditMode && $product && $product->unit_type_id && $product->product_units && count($product->product_units)) --}}
                                @if ($isEditMode && $product && $product->unit_type_id && $product->product_units && count($product->product_units))
                                    @foreach ($product->product_units as $key => $product_unit)
                                    
                                    <tr id="unit_row_{{ $key }}" class="unit_row" data-id="{{ $product_unit->unit_id }}" data-name="{{ $product_unit->unit->name }}"
                                    data-conversion_unit="{{ $product_unit->unit->conversion_unit->name ?? "-" }}" data-is_base="{{ $product_unit->unit->is_base }}"
                                    data-conversion_unit_id="{{ $product_unit->unit->conversion_unit_id }}">
                                    
                                    <td> {{ $product_unit->unit->name ?? '-' }}
                                         <input type="hidden" name="unit_conversion[{{ $product_unit->unit_id }}][id]" value="{{ $product_unit->unit_id }}">
                                    </td>
                                    <td>
                                       {{ $product_unit->unit->conversion_unit->name ?? '-' }}
                                        <input type="hidden" name="unit_conversion[{{$product_unit->unit_id}}][conversion_unit_id]" value="{{ $product_unit->unit->conversion_unit_id ?? "" }}">    
                                    </td>
                                    <td><input type="number" class="form-control {{ $product_unit->unit->is_base ? ' non-editable-field' : '' }}}" 
                                            name="unit_conversion[{{ $product_unit->unit_id }}][qty]" 
                                            value="{{ $product_unit->conversion_rate ?? 1}}"
                                           {{ $product_unit->unit->is_base || $product_unit->unit->pre_defined ? 'readonly' : '' }}
                                            min="0.1" 
                                            step="any"
                                        ></td>
                                    <td><input type="number" class="form-control" name="unit_conversion[{{ $product_unit->unit_id }}][cost]" value="{{ $product_unit->unit_cost ?? 0 }}" step="any" min="0"></td>
                                    <td><input type="number" class="form-control" name="unit_conversion[{{ $product_unit->unit_id }}][rate]" value="{{ $product_unit->unit_rate ?? 0 }}" step="any" min="0"> </td>
                                    <td><input type="text" class="form-control" name="unit_conversion[{{ $product_unit->unit_id }}][barcode]" value="{{ $product_unit->unit_barcode ?? 0 }}"></td>
                                    <td class="d-flex align-items-center justify-content-center">
                                        <input type="checkbox" class="{{ $product_unit->unit->is_base ? ' non-editable-field' : '' }}" 
                                        name="unit_conversion[{{ $product_unit->unit_id }}][is_active]" value="1" {{ $product_unit->is_active ? 'checked' : '' }}></td>
                                    <td class=""><input type="radio" name="unit_conversion[default]" value="{{ $product_unit->unit_id }}" required
                                         {{ $product_unit->unit_id === $product->default_unit_id ? 'checked' : '' }}> </td>
                                </tr>
                                        

                                    @endforeach
                                    
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-3">
                        <div class="mt-3 d-flex">

                            <label for="checkInventroyAdd" class="mb-0">Stock Item </label>
                            <div class="form-check form-switch ps-0 ms-auto my-auto is-filled">
                                <input type="checkbox" id="checkInventroyAdd" class="form-check-input" checked
                                    name="check_inv" value="1">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Save</button>
                </div>
        </div>
        </form>
    </div>
    </div>

@if (!$isEditMode || !$product->unit_type_id)
    <script>
        $(document).ready(function() {
            $('#unit_table').hide();
        });
    </script>
@else
    
@endif
    <script>
        $(document).ready(function() {
            
            
            
            updateActiveCheckboxes()
        });

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(input).css('background-image', 'url(' + e.target.result + ')');
                    $(input).hide();
                    $(input).fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $(".imageUpload").change(function() {
            var $avatarPreview = $(this).closest('.avatar-upload').find('.avatar-preview');
            var $imagePreview = $avatarPreview.find('.imagePreview'); // Cache the reference
            console.log({
                $imagePreview
            });
            if (this.files && this.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $imagePreview.css('background-image', 'url(' + e.target.result + ')');
                    $imagePreview.hide().fadeIn(650);
                };
                reader.readAsDataURL(this.files[0]);
            }
        });



        function generateCode(text) {
            // Extract initials from the text
            let initials = text
                .split(' ') // Split the text by spaces
                .map(word => word[0]) // Get the first letter of each word
                .join('') // Join them to form initials
                .toUpperCase(); // Convert initials to uppercase

            // Generate a random 3-digit number
            let randomNumber = Math.floor(100 + Math.random() * 900); // Generates number between 100 and 999

            // Combine initials with random number
            return initials + randomNumber;
        }


        $("#product_name").on("input", function() {
            let text = $(this).val();

            if (text.trim()) {
                $("#product_code").val(generateCode(text));
            }

        });



        $("#unit_type_id").change(function() {
            let unitTypeId = $(this).val();
            if (unitTypeId) {
                $('#unit_table').show();
            } else {
                $('#unit_table').hide();
            }
            getUnitsAndCreateTable(unitTypeId)

        });

        function getUnitsAndCreateTable(unitTypeId) {

            var storeId = $("#storeId").val();

            $("#unit_table tbody").empty(); // Clear the table body before appending new data

            $.ajax({
                url: `/api/units/${unitTypeId}/${storeId}`,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    const units = response?.units || [];

                    units.forEach((element, index) => {
                        
                        $('#unit_table tbody').append(`
                            <tr id="unit_row_${index}" class="unit_row" data-id="${element.id}" data-name="${element.name}"
                                data-conversion_unit="${element.conversion_unit?.name || '-'}" data-is_base="${element.is_base}"
                                data-conversion_unit_id="${element.conversion_unit_id}">
                                
                                <td> ${element.name}
                                     <input type="hidden" name="unit_conversion[${element.id}][id]" value="${element.id}">
                                </td>
                                <td>
                                    ${element?.conversion_unit?.name || '-'}
                                    <input type="hidden" name="unit_conversion[${element.id}][conversion_unit_id]" value="${element.conversion_unit_id}">    
                                </td>
                                <td><input type="number" class="form-control ${element.is_base ? ' non-editable-field' : ''}" 
                                        name="unit_conversion[${element.id}][qty]" 
                                        value="${element.pre_defined ? element.default_conversion_factor : 1}"
                                        ${element.is_base || element.pre_defined ? 'readonly' : ''}
                                        min="0.1" 
                                        step="any"
                                    ></td>
                                <td><input type="number" class="form-control" name="unit_conversion[${element.id}][cost]" value="0" step="any" min="0"></td>
                                <td><input type="number" class="form-control" name="unit_conversion[${element.id}][rate]" value="0" step="any" min="0"></td>
                                <td><input type="text" class="form-control" name="unit_conversion[${element.id}][barcode]" value=""></td>
                                <td class="d-flex align-items-center justify-content-center">
                                    <input type="checkbox" class="${element.is_base ? ' non-editable-field' : ''}" 
                                    name="unit_conversion[${element.id}][is_active]" value="1" ${element.is_base ? 'checked' : ''}></td>
                                <td class=""><input type="radio" name="unit_conversion[default]" value="${element.id}" ${element.is_base ? 'checked' : ''}> </td>
                            </tr>
                        `);
                    });
                    updateActiveCheckboxes()
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching units:', error);
                }
            });

        }


        function updateActiveCheckboxes() {
            const activeUnitIds = [];
          
            // First pass: gather all active unit IDs
            $('.unit_row').each(function() {
                const $row = $(this);
                const unitId = $row.data('id');
                const isBase = $row.data('is_base');
                const $checkbox = $row.find('input[type="checkbox"]');

                if (isBase || $checkbox.prop('checked')) {
                    activeUnitIds.push(unitId);
                }
            });


            $(document).on('change', 'input[type="checkbox"]', function() {
                const $checkbox = $(this);
                const $row = $checkbox.closest('.unit_row');
                const isBase = $row.data('is_base');

                if (isBase) {
                    $checkbox.prop('checked', true); // reset back to checked
                } else {
                    updateActiveCheckboxes(); // re-evaluate the state of all checkboxes
                }
            });


            // Second pass: update checkboxes
            $('.unit_row').each(function() {
                const $row = $(this);
                const isBase = $row.data('is_base');
                const conversionUnitId = $row.data('conversion_unit_id');
                const $checkbox = $row.find('input[type="checkbox"]');
                const $default = $row.find('input[type="radio"]');

                if (isBase) {
                    $checkbox.prop('checked', true);
                }
                if (activeUnitIds?.includes(conversionUnitId)) {
                    $checkbox.prop('disabled', false);
                }
                if (!activeUnitIds?.includes(conversionUnitId) && !isBase) {
                    $checkbox.prop('disabled', true);
                    $checkbox.prop('checked', false);
                }

        

                if ($checkbox.prop('checked')) {
                    $default.prop('disabled', false);
                } else {
                    $default.prop('disabled', true);
                    $default.prop('checked', false);
                }
            });
        }
    </script>
@endsection
