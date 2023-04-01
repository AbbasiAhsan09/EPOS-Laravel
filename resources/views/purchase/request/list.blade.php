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
                        
                        <select name="" id="" class="form-control">
                            <option value="">--Select Status--</option>
                            <option value="1">Approved</option>
                            <option value="0">Un-Approved</option>
                            <option value="3">In-Process</option>
                            <option value="2">Rejected</option>
                        </select>
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
            <th>Created By</th>
            <th>Type</th>
            <th>Store</th>
            <th>Required Before</th>
            <th>Created At</th>
            <th>Status</th>

            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($requests as $key => $item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->created_by->name}}</td>
                    <td>{{$item->type}}</td>
                    <td>{{'Default'}}</td>
                    <td>{{$item->required_on}}</td>
                    <td>{{date('d.m.y | h:m A' , strtotime($item->created_at))}}</td>
                    <td>
                        @if ($item->status == 1)
                            <span class="badge  badge-sm bg-gradient-success">Approved</span>
                        @elseif($item->status == 0)
                        <span class="badge  badge-sm bg-gradient-warning">Unapproved</span>
                        @elseif($item->status == 2)
                        <span class="badge  badge-sm bg-gradient-danger">Rejected</span>
                        @elseif($item->status == 3)
                        <span class="badge  badge-sm bg-gradient-info">In Process</span>
                        @endif
                    </td>
                    <td>
                        <div class="s-btn-grp">
                            
                            <a class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" >
                                <i class="fa fa-eye"></i>
                            </a>
                            <a class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" href="{{url("/purchase/request/$item->id/edit")}}">
                                <i class="fa fa-edit"></i>
                            </a>
                            <a class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}">
                                <i class="fa fa-trash"></i>
                            </button>
                            
                         
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {!! $requests->links('pagination::bootstrap-4') !!}
    </div>
</div>
</div>

@endsection