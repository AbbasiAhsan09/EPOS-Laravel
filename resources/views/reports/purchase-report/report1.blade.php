@extends('layouts.app')
@section('content')
        
<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row row-customized">
            <div class="col">
                <h1 class="page-title">Purchase Report</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-3">
                <label for="">From Date: </label>
                <div class="input-group input-group-outline">
                  <input type="date" name="start_date"  class="form-control">
                  </div>
            </div>
            <div class="col-lg-3">
                <label for="">To Date: </label>
                <div class="input-group input-group-outline">
                  <input type="date" name="end_date"  class="form-control">
                  </div>
            </div>
            <div class="col-lg-3">
                <label for="">Vendors: </label>
                <div class="input-group input-group-outline">
                  <select name="vendor" id="" class="form-control">
                    <option value=""></option>
                  </select>
                  </div>
            </div>
        </div>

    </div>
</div>

@endsection