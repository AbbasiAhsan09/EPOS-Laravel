@extends('layouts.app')
@section('content')

<div class="container-fluid">
    <div class="row d-flex align-items-end">
        <div class="col-lg-3">
            <h2>Labour Bills</h2>
        </div>
        <div class="col-lg-9">
            <form action="{{route("labour-work.index")}}">
                <div class="row d-flex align-items-end">
                    <div class="col-lg-2">
                        <div class="select_party_wrapper">
                            <label for="">From</label>
                            <div class="input-group input-group-outline">
                            <input type="date" name="from" value="{{session()->get("labour_from")}}"   class="form-control">
                            </div>
                        </div> 
                    </div>
    
                    <div class="col-lg-2">
                        <div class="select_party_wrapper">
                            <label for="">To</label>
                            <div class="input-group input-group-outline">
                            <input type="date" name="to" value="{{session()->get("labour_to")}}"  class="form-control">
                            </div>
                        </div> 
                    </div>
    
                    <div class="col-lg-2">
                        <div class="select_party_wrapper">
                            <label for="">Status</label>
                            <div class="input-group input-group-outline">
                                <select name="status" id="" class="form-control">
                                    <option value="">All</option>
                                    <option value="open" {{session()->get("labour_status") == 'open' ? 'selected' : ''}}>Open</option>
                                    <option value="close" {{session()->get("labour_status") == 'close' ? 'selected' : ''}}>Closed</option>
                                </select>
                            </div>
                        </div> 
                    </div>
    
    
                    <div class="col-lg-2">
                        <div class="select_party_wrapper">
                            <label for="">Labour</label>
                            <div class="input-group input-group-outline">
                                <select name="labour_id" id="" class="form-control">
                                    <option value="">All</option>
                                    @foreach ($labours as $labour)
                                        <option {{session()->get("labour_id") == $labour->id ? 'selected' : ''}} value="{{$labour->id}}">{{$labour->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                    </div>
                    <div class="col-lg-1">
                        <div class="select_party_wrapper">
                            <label for="">Type</label>
                            <div class="input-group input-group-outline">
                                <select name="type" id="" class="form-control">
                                    <option value="">Web</option>
                                    <option value="pdf">PDF</option>
                                    
                                </select>
                            </div>
                        </div> 
                    </div>
    
                    <div class="col-lg-3" >
                        
                        <button  class="btn btn-secondary m-0" >Filter</button>
                        <a href="/labour-work/create" class="btn btn-outline-primary m-0" >Create Bill</a>
    
                    </div>
    
    
                </div>
            </form>
        </div>
    </div>
    <table class="table table-sm-responsive table-bordered table-striped mt-2" border="2">
        <thead>
            <th>Status</th>
            <th>Doc #</th>
            <th>Labour</th>
            <th>Open Date</th>
            <th>Close Date</th>
            <th>Created Date</th>
            <th>Net Total</th>
            <th>Action</th>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr style="{{!empty($item->end_date) ? 'background: rgb(233, 187, 187); color : black' : ''}}">
                    <td>
                        @if (!empty($item->end_date))
                        <span class="badge badge-sm bg-gradient-danger">Closed</span>
                        @else
                        <span class="badge badge-sm bg-gradient-success">Open</span>
                        @endif
                    </td>
                    <td>{{$item->doc_no ?? ""}}</td>
                    <td>{{$item->labour->name ?? ""}}</td>
                    <td>{{date("m-d-Y", strtotime($item->start_date))}}</td>
                    <td>{{$item->end_date ? date("m-d-Y", strtotime($item->end_date)) : '-'}}</td>
                    <td>{{date("m-d-Y", strtotime($item->created_at))}}</td>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($item->net_total,2)}}</td>
                    <td>
                        <div class="s-btn-grp">
                            <a href="/labour-work/{{$item->id}}/edit" class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" >
                                <i class="fa fa-edit"></i>
                            </a>
                            <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}">
                                <i class="fa fa-trash"></i>
                            </button>
                         
                        </div>
                      
                    </td>
                </tr>
            @endforeach
            <tfoot>
                <th colspan="6">Grand Total</th>
                <th colspan="2">{{ConfigHelper::getStoreConfig()["symbol"].number_format($items->sum("net_total"),2)}}</th>
            </tfoot>
        </tbody>
    </table>
</div>

@endsection