@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Purchase Quotations</h1>
    </div>
    <div class="col">
        <div class="btn-grp">
         
            <div class="row .row-customized">
                <div class="col-lg-8">
                    <div class="input-group input-group-outline">
                        
                        <select name="" id="" class="form-control">
                            <option value="">--Select Status--</option>
                            <option value="1">Approved</option>
                        </select>
                      </div>
                  
                </div>
                <div class="col-lg-4">
                <a href="{{url("/purchase/quotation/create")}}" class="btn btn-outline-primary btn-sm mb-0" >Create Quotation</a>

                </div>
            </div>
        </div>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped ">
        <thead>
            <th>S#</th>
            <th>Doc #</th>
            <th>PR #</th>
            <th>Type</th>
            <th>Party</th>
            <th>Gross Total</th>
            <th>Net. Total</th>
            <th>Created By</th>
            <th>Created At</th>

            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($quotations as $key => $item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->doc_num}}</td>
                    <td><a href="" style="font-style: italic">{{$item->req_num ?? '(Null)'}}</a></td>
                    <td>{{$item->type}}</td>
                    <td>{{$item->party->party_name}}</td>
                    <td>{{env('CURRENCY').' '.$item->gross_total}}</td>
                    <td class="text-primary">
                      <b>  {{env('CURRENCY').' '.($item->gross_total - $item->discount) + $item->other_charges }}</b>
                    </td>
                    <td>{{$item->created_by_user->name}}</td>
                    <td>{{date('d.m.y | h:m A' , strtotime($item->created_at))}}</td>
                    <td>
                        <div class="s-btn-grp">
                            
                            <a class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" >
                                <i class="fa fa-eye"></i>
                            </a>
                            <a class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" href="{{url("/purchase/quotation/$item->id/edit")}}">
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
        {!! $quotations->links('pagination::bootstrap-4') !!}
    </div>
</div>
</div>

@endsection