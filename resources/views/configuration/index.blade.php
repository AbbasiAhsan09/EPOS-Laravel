@extends('layouts.app')
@section('content')
    
<div class="page-wrapper">        
        <div class="container-fluid">
            <div class="row row-customized">
                <div class="col">
                    <h1 class="page-title">Configuration</h1>
                </div>
            </div>
            <div class="form-group my-4">
                <div class="row">
                    <div class="col-lg-3">
                        <label for="">Organization Name*</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="uom" required  placeholder="Organization Name">
                    </div>
                    </div>
                </div>
                <div class="mt-3 d-flex">
                    <label for="" class="mb-0">Navbar: </label>
                    <div class="form-check form-switch ps-0 ms-auto my-auto is-filled">
                        <input type="checkbox" class="form-check-input" id=""> 
                    </div>
                </div>
              
            </div>
        </div>
</div>


@endsection