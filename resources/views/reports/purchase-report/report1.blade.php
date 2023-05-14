@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-4">
        <h1 class="page-title">Purchase Report <small>{{session()->get('filter_deleted') ? '(Deleted)' : ''}}</small></h1>
    </div>
    <div class="col">
        <form action="{{route('purchase-report.index')}}" method="GET">
            <div class="btn-grp">
         
                <div class="row .row-customized">
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="start_date" value="{{session()->get('purchase-report-start-date')}}" class="form-control">
                          </div>
                      
                    </div>
                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <input type="date" name="end_date" value="{{session()->get('purchase-report-end-date')}}" placeholder="To"  class="form-control">
                          </div>
                      
                    </div>
                    <div class="col-lg-4">
                        <div class="input-group input-group-outline">
                            <select name="vendor" class="form-control" id="">
                                <option value="">All</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{$vendor->id}}" {{session()->get('vendor')  == $vendor->id ? 'selected' : ''}}>{{$vendor->party_name}}</option>
                                @endforeach
                            </select>
                          </div>
                      
                    </div>

                    <div class="col-lg-2">
                        <div class="input-group input-group-outline">
                            <select name="type" class="form-control" id="">
                                <option value="">Web</option>
                                <option value="pdf">PDF</option>
                            </select>
                          </div>
                      
                    </div>
                    <input type="hidden" name="filter_deleted" value="{{session()->get('filter_deleted') ? 'true' : 'false'}}">
                    <div class="col-lg-2">
                        <button class="btn btn-primary">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped ">
        <thead>
            <th>S#</th>
            <th>Doc #</th>
            <th>PO #</th>
            <th>Party</th>
            <th>Gross Total</th>
            <th>Net. Total</th>
            <th>Created By</th>
            <th>Created At</th>

            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($records as $key => $item)
                <tr >
                    <td>{{$key+1}}</td>
                    <td>{{$item->doc_num}}</td>
                    <td><a href="{{url("/purchase/order/".$item->order->id."/edit")}}" style="font-style: italic">{{$item->order->doc_num ?? '(Null)'}}</a></td>
                    
                    <td>{{$item->party->party_name}}</td>
                    <td>{{env('CURRENCY').' '.$item->total}}</td>
                    <td class="text-primary">
                      <b>  {{env('CURRENCY').' '.($item->total - $item->discount) + $item->other_charges }}</b>
                    </td>
                    <td>{{$item->created_by_user->name}}</td>
                    <td>{{date('d.m.y | h:m A' , strtotime($item->created_at))}}</td>
                    <td>
                        <div class="s-btn-grp">
                            <a class="btn btn-link text-dark text-sm mb-0 px-0 ms-4 {{$item->created_at != $item->updated_at ? 'text-primary' : ''}}" href="{{url("/purchase/invoice/$item->id/edit")}}"><i class="fa fa-edit"></i></a>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {!! $records->links('pagination::bootstrap-4') !!}
    </div>
</div>
</div>



@endsection