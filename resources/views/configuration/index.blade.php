@extends('layouts.app')
@section('content')
    
<div class="page-wrapper">        
        <div class="container-fluid">
            <div class="row row-customized">
                <div class="col">
                    <h1 class="page-title">Configuration</h1>
                </div>
            </div>
           <form action="{{route('configurations.store')}}" method="POST" enctype="multipart/form-data">
            @csrf
             <div class="form-group my-4">
                <div class="row">
                    <div class="col-lg-3">
                        <label for="">Business Name*</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="business" required  placeholder="Business Name" value="{{isset($currenConfig) ? $currenConfig->app_title : '' }}">
                    </div>
                    </div>
                      <div class="col-lg-3">
                        <label for="">Logo</label>
                    <div class="input-group input-group-outline">
                      <input type="file" class="form-control" name="logo"   placeholder="Logo" >
                    </div>
                    </div>

                     <div class="col-lg-3">
                        <label for="">Phone</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="phone" required  placeholder="Phone" value="{{isset($currenConfig) ? $currenConfig->phone : '' }}">
                    </div>
                    </div>

                    
                     <div class="col-lg-3">
                        <label for="">Address</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="address"   placeholder="Address" value="{{isset($currenConfig) ? $currenConfig->address : '' }}">
                    </div>
                    </div>

                    <div class="col-lg-3">
                        <label for="">NTN#</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="ntn"   placeholder="NTN No." value="{{isset($currenConfig) ? $currenConfig->ntn : '' }}">
                    </div>
                    </div>

                    <div class="col-lg-3">
                        <label for="">PTN#</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="ptn"   placeholder="PTN No." value="{{isset($currenConfig) ? $currenConfig->ptn : '' }}">
                    </div>
                    </div>

                    





                    <div class="col-lg-3">
                        <label for="">Invoice Message:</label>
                    <div class="input-group input-group-outline">
                      <textarea name="inv_message"   class="form-control">{{isset($currenConfig) ? $currenConfig->invoice_message : '' }}</textarea>
                    </div>
                    </div>

                    
                    <div class="col-lg-3">
                       
                    </div>

                    <div class="col-lg-3">
                         <div class="mt-3 d-flex">
                    <label for="" class="mb-0">Show NTN: </label>
                    <div class="form-check form-switch ps-0 ms-auto my-auto is-filled">
                        <input type="checkbox" class="form-check-input" id="" name="show_ntn" value="1" {{isset($currenConfig) ? ($currenConfig->show_ntn ? 'checked' : '') : '' }}> 
                    </div>
                </div>

                  <div class="mt-3 d-flex">
                    <label for="" class="mb-0">Show PTN: </label>
                    <div class="form-check form-switch ps-0 ms-auto my-auto is-filled">
                        <input type="checkbox" class="form-check-input" id="" name="show_ptn" value="1" {{isset($currenConfig) ? ($currenConfig->show_ptn ? 'checked' : '') : '' }}> 
                    </div>
                </div>


                  <div class="mt-3 d-flex">
                    <label for="" class="mb-0">Multiple Orders: </label>
                    <div class="form-check form-switch ps-0 ms-auto my-auto is-filled">
                        <input type="checkbox" class="form-check-input" id="" name="is_multi_order" value="1" {{isset($currenConfig) ? ($currenConfig->mutltiple_sales_order ? 'checked' : '') : '' }}> 
                    </div>
                </div>



                  <div class="mt-3 d-flex">
                    <label for="" class="mb-0">Track Inventory: </label>
                    <div class="form-check form-switch ps-0 ms-auto my-auto is-filled">
                        <input type="checkbox" class="form-check-input" id="" name="track_inventory" value="1" {{isset($currenConfig) ? ($currenConfig->inventory_tracking ? 'checked' : '') : '' }}> 
                    </div>
                </div>


                    </div>


                    <div class="col-lg-3">
                        @if (isset($currenConfig) && $currenConfig->logo)
                            <img src="{{asset("images/logo/$currenConfig->logo")}}" alt="Not Availabe" width="130px">
                        @endif
                    </div>

                </div>
               
            <button type="submit" class=" my-3 btn btn-primary" >Save</button>  
            </div>

           </form>
        </div>
</div>


@endsection