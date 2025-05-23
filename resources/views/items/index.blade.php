@extends('layouts.app')
@section('content')
    @include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/hGkaaHxzxlo'])

    <div class="page-wrapper">
        <div class="container-fluid">

            <div class="row row-customized">
                <div class="col">
                    <h1 class="page-title">Products</h1>
                </div>
                <div class="col">
                    <div class="btn-grp">

                        <div class="row .row-customized">
                            <div class="col-lg-6">
                                <form action="{{ url('products') }}" method="GET">
                                    <div class="input-group input-group-outline">
                                        <label class="form-label">Search</label>
                                        <input type="text" class="form-control" value="{{ session()->get('filter') }}"
                                            onfocus="focused(this)" name="filter" onfocusout="defocused(this)">
                                    </div>
                                </form>

                            </div>
                            <div class="col-lg-6">
                                <a class="btn btn-outline-primary btn-sm mb-0"  href="{{ url('products/create') }}"
                                    >New Product</a>
                                <button class="btn btn-outline-secondary btn-sm mb-0" data-bs-toggle="modal"
                                    data-bs-target="#CsvModal">Import</button>


                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @error('any')
                {{ $message }}
            @enderror
            <table class="table table-sm table-responsive-sm table-striped">
                <thead>
                    <th>ID</th>
                    <th>Brand</th>
                    <th>Category</th>
                    <th>Product</th>
                    <th>Code</th>
                    <th>UOM</th>
                    <th>Added On</th>

                    @if (Auth::user()->userroles->role_name == 'Admin')
                        <th>Actions</th>
                    @endif
                </thead>
                <tbody>
                    @foreach ($items as $key => $item)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $item->brand }}</td>
                            <td>{{ $item->categories->category ?? '' }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->barcode }}</td>
                            <td>{{ $item->uom ? $item->uoms->uom : 'Default' }}</td>
                            <td>{{ date('d, M Y | h:m A', strtotime($item->created_at)) }}</td>

                            @if (Auth::user()->userroles->role_name == 'Admin')
                                <td>
                                    <div class="s-btn-grp">
                                        <a class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" href="{{ url('products/create/' . $item->id) }}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button class="btn btn-link text-danger text-gradient px-3 mb-0"
                                            data-bs-toggle="modal" data-bs-target="#deleteModal{{ $item->id }}">
                                            <i class="fa fa-trash"></i>
                                        </button>

                                    </div>
                                </td>
                            @endif
                        </tr>
                        {{-- Delete Modal --}}
                        <!-- Modal -->
                        <div class="modal fade" id="deleteModal{{ $item->id }}" tabindex="-1"
                            aria-labelledby="newStoreModalLabel" aria-hidden="true" data-backdrop="static"
                            data-keyboard="false">
                            <div class="modal-dialog">
                                <div class="modal-content ">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="newStoreModalLabel">Delete Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete {{ $item->name }}?</p>
                                    </div>
                                    <div class="modal-footer">
                                        <form action="{{ route('delete.product', $item->id) }}" method="POST">
                                            @csrf
                                            @method('delete')
                                            <button class=" btn" type="button">No</button>
                                            <button class="btn btn-primary" type="submit">Yes</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Delete MOdal --}}
                        <!-- Modal -->
                        <div class="modal fade" id="newStoreModal{{ $item->id }}" tabindex="-1"
                            aria-labelledby="newStoreModalLabel" aria-hidden="true" data-backdrop="static"
                            data-keyboard="false">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content ">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="newStoreModalLabel">Update Product</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('update.product', $item->id) }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            @method('put')
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <label for="">Name *</label>
                                                    <div class="input-group input-group-outline">
                                                        <input type="text" class="form-control" name="product" required
                                                            value="{{ $item->name }}">
                                                    </div>
                                                </div>
                                               
                                                <div class="col-lg-6">
                                                    @livewire('field-category', ['selectedCategory' => $item->category, 'required' => false])
                                                </div>

                                                <div class="col-lg-6">
                                                    <div class="row">
                                        
                                                        <div class="col-lg-6">
                                                            <label for="">Code *</label>
                                                            <div class="input-group input-group-outline">
                                                                <input type="text" class="form-control" name="code"
                                                                    required value="{{ $item->barcode }}">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <label for="">Brand </label>
                                                            <div class="input-group input-group-outline">
                                                                <input type="text" class="form-control" name="brand"
                                                                    value="{{ $item->brand }}">
                                                            </div>
                                                        </div>
                                                        {{-- <div class="col-lg-6">
                        <label for="">Arrtribute *</label>
                        <div class="input-group input-group-outline">

                        <select name="arrt" id="" class="form-control" disabled >
                            <option value="">None</option>
                            @foreach ($arrt as $art)
                                <option value="{{$art->id}}">{{$art->arrtribute}}</option>
                            @endforeach
                        </select>
                    </div>
                    </div> --}}
                                                        <div class="col-lg-6">
                                                            <label for="">UOM </label>
                                                            <div class="input-group input-group-outline">

                                                                <select name="uom" id=""
                                                                    class="form-control"
                                                                    {{ Auth::user()->role_id != 1 ? 'readonly' : '' }}>
                                                                    <option value="0"
                                                                        {{ $item->uom == 0 ? 'selected' : '' }}>Default
                                                                    </option>

                                                                    @foreach ($uom as $unit_of_m)
                                                                        <option value="{{ $unit_of_m->id }}"
                                                                            {{ $unit_of_m->id == $item->uom ? 'selected' : '' }}>
                                                                            {{ $unit_of_m->uom }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <label for="">TAX % </label>
                                                            <div class="input-group input-group-outline">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    name="tax" value="{{ $item->taxes }}"
                                                                    min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <label for=""> Rate </label>
                                                            <div class="input-group input-group-outline">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    name="mrp" value="{{ $item->mrp }}"
                                                                    min="0">
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <label for="">Cost </label>
                                                            <div class="input-group input-group-outline">
                                                                <input type="number" step="0.01" class="form-control"
                                                                    name="tp" value="{{ $item->tp }}"
                                                                    min="0">
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-6">
                                                            <label class="form-label">Opening Inventory </label>
                                                            <div class="input-group input-group-outline">
                                                                <input type="number" class="form-control" step="0.01"
                                                                    name="opening_stock"
                                                                    value="{{ $item->opening_stock }}" min="0"
                                                                    onfocus="focused(this)" onfocusout="defocused(this)">
                                                            </div>

                                                        </div>

                                                        <div class="col-lg-6">
                                                            <label class="form-label">Opening Stock Unit Cost </label>
                                                            <div class="input-group input-group-outline">
                                                                <input type="number" class="form-control" step="0.01"
                                                                    name="opening_stock_unit_cost"
                                                                    value="{{ $item->opening_stock_unit_cost }}"
                                                                    min="0" onfocus="focused(this)"
                                                                    onfocusout="defocused(this)">
                                                            </div>

                                                        </div>
                                                        <div class="col-lg-12">
                                                            <label class="form-label">Low Stock Alert </label>
                                                            <div class="input-group input-group-outline">
                                                                <input type="number" class="form-control"
                                                                    name="low_stock" value="{{ $item->low_stock }}"
                                                                    min="0" onfocus="focused(this)"
                                                                    onfocusout="defocused(this)">
                                                            </div>

                                                        </div>
                                                        {{--  --}}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="container">

                                                        <div class="avatar-upload">
                                                            <div class="avatar-edit">
                                                                <input type='file'
                                                                    id="imageUpload-{{ $item->id }}"
                                                                    class="imageUpload" name="image"
                                                                    accept=".png, .jpg, .jpeg" />
                                                                <label for="imageUpload-{{ $item->id }}"></label>
                                                            </div>
                                                            <div class="avatar-preview">

                                                                <div class="imagePreview"
                                                                    style="background-image: url({{ $item->img ? asset('/images/' . Auth::user()->store_id . '/products/' . $item->img) : 'https://www.trianglelearningcenter.org/wp-content/uploads/2020/08/placeholder.png' }});">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="text-align: center">
                                                        @include('items.component.barcode', [
                                                            'code' => $item->barcode,
                                                        ])
                                                    </div>

                                                </div>
                                                <div class="col-lg-12">
                                                    <label class="form-label">Description </label>
                                                    <div class="input-group input-group-outline">
                                                        <textarea name="description" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)"
                                                            rows="3">{{ $item->description }}</textarea>

                                                    </div>
                                                </div>
                                                <div class="col-lg-3">
                                                    <div class="mt-3 d-flex">

                                                        <label for="checkInventroyAdd" class="mb-0">Check Inventory
                                                        </label>
                                                        <div class="form-check form-switch ps-0 ms-auto my-auto is-filled">
                                                            <input type="checkbox" id="checkInventroyAdd"
                                                                class="form-check-input"
                                                                {{ $item->check_inv ? 'checked' : '' }} name="check_inv"
                                                                value="1">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                    </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        {{-- Modal --}}
                    @endforeach
                </tbody>
            </table>
            {{ $items->links('pagination::bootstrap-4') }}
        </div>
    </div>


    <!-- Modal -->
    <div class="modal fade" id="newStoreModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true"
        data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content ">
                <div class="modal-header">
                    <h5 class="modal-title" id="newStoreModalLabel">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('add.product') }}" method="POST" enctype="multipart/form-data"
                        id="new_product">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <label for="">Name *</label>
                                <div class="input-group input-group-outline">
                                    <input type="text" class="form-control" value="{{ old('product') }}"
                                        id="product_name" name="product" required>
                                </div>
                            </div>
                            {{-- <div class="col-lg-6">
                        <label for="">Category *</label>
                        <div class="input-group input-group-outline">
                            <select name="category" id="" class="form-control">
                            <option value="">Select Category</option>
                            @foreach ($categories as $cat)
                                <option value="{{$cat->id}}">{{$cat->category}}</option>
                            @endforeach
                        </select>
                        </div>
                    </div> --}}
                            <div class="col-lg-6">
                                @livewire('field-category')
                            </div>
                            <div class="col-lg-6">
                                <div class="row">
                                    <div class="col-lg-6">
                                        <label for="">Code *</label>
                                        <div class="input-group input-group-outline">
                                            <input type="text" class="form-control" name="code" id="product_code"
                                                value="{{ old('code') }}" required>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="">Brand </label>
                                        <div class="input-group input-group-outline">
                                            <input type="text" class="form-control" name="brand"
                                                value="{{ old('brand') }}">
                                        </div>
                                    </div>

                                    {{-- <div class="col-lg-6">
                                <label for="">Arrtribute </label>
                                <div class="input-group input-group-outline">
        
                                <select name="arrt" id="" class="form-control" disabled>
                                    <option value="">None</option>
                                    @foreach ($arrt as $arrt)
                                        <option value="{{$arrt->id}}">{{$arrt->arrtribute}}</option>
                                    @endforeach
                                </select>
                            </div>
                            </div> --}}
                                    <div class="col-lg-6">
                                        <label for="">UOM *</label>
                                        <div class="input-group input-group-outline">

                                            <select name="uom" id="" class="form-control">
                                                <option value="0">Default</option>
                                                @foreach ($uom as $uom)
                                                    <option value="{{ $uom->id }}"
                                                        {{ old('uom') == $uom->id ? 'selected' : '' }}>{{ $uom->uom }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="">TAX % </label>
                                        <div class="input-group input-group-outline">
                                            <input type="number" step="0.01" class="form-control" name="tax"
                                                value="{{ old('tax') ? old('tax') : 0 }}" min="0">
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="">Sale Price </label>
                                        <div class="input-group input-group-outline">
                                            <input type="number" step="0.01" class="form-control" name="mrp"
                                                value="{{ old('mrp') ? old('mrp') : 0 }}" min="0">
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <label for="">Cost *</label>
                                        <div class="input-group input-group-outline">
                                            <input type="number" step="0.01" class="form-control" name="tp"
                                                value="{{ old('tp') ? old('tp') : 0 }}" min="0">
                                        </div>
                                    </div>


                                    <div class="col-lg-6">
                                        <label class="form-label">Opening Inventory </label>
                                        <div class="input-group input-group-outline">
                                            <input type="number" class="form-control" step="0.01"
                                                name="opening_stock"
                                                value="{{ old('opening_stock') ? old('opening_stock') : 0 }}"
                                                min="0" onfocus="focused(this)" onfocusout="defocused(this)">
                                        </div>

                                    </div>

                                    <div class="col-lg-6">
                                        <label class="form-label">Opening Stock Unit Cost </label>
                                        <div class="input-group input-group-outline">
                                            <input type="number" class="form-control" step="0.01"
                                                name="opening_stock_unit_cost"
                                                value="{{ old('opening_stock_unit_cost') ? old('opening_stock_unit_cost') : 0 }}"
                                                min="0" onfocus="focused(this)" onfocusout="defocused(this)">
                                        </div>

                                    </div>

                                    <div class="col-lg-12">
                                        <label class="form-label">Low Stock Alert </label>
                                        <div class="input-group input-group-outline">
                                            <input type="number" class="form-control" name="low_stock"
                                                value="{{ old('low_stock') ? old('low_stock') : 0 }}" min="0"
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
                                                value="{{ old('opening_stock') ? old('opening_stock') : 0 }}"
                                                min="0" onfocus="focused(this)" onfocusout="defocused(this)">

                                        </div>
                                    </div>
                                @endforeach
                            @endif
                            <div class="col-lg-12">
                                <label class="form-label">Description </label>
                                <div class="input-group input-group-outline">
                                    <textarea name="description" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)"
                                        rows="3">{{ old('description') }}</textarea>

                                </div>
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

                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn brn-primary">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal --}}



    <!-- CSV Modal -->
    <div class="modal fade" id="CsvModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="newStoreModalLabel">Import CSV File</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('import.product') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">

                            <div class="col-lg-12">
                                <label for="">Upload File </label>
                                <div class="input-group input-group-outline">
                                    <input type="file" class="form-control" name="file" required>
                                </div>
                            </div>



                        </div>

                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Import</button>

                </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Csv Modal --}}

    <script>
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
    </script>

@endsection
