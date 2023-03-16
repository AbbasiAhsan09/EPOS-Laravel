@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Purchase Requistion</h1>
    </div>
    <div class="col">
        <div class="btn-grp">
         
            <div class="row .row-customized">
                <div class="col-lg-8">
                    <div class="input-group input-group-outline">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)">
                      </div>
                  
                </div>
                <div class="col-lg-4">
                <a href="{{url("/purchase/request/create")}}" class="btn btn-outline-primary btn-sm mb-0" >Create Requistion</a>

                </div>
            </div>
        </div>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>S#</th>
            <th>Category Name</th>
            <th>Status</th>
           
            <th>Actions</th>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>
</div>

@endsection