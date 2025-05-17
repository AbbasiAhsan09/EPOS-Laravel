@extends('layouts.app')
@section('content')
<div class="container-fluid">
    <form action="{{route("voucher.index")}}" method="GET">
    <div class="row d-flex align-items-center mb-2">
        <div class="col-lg-3">
            <select name="voucher_type_id" class="form-control">
                <option value="">All</option>
                @foreach ($all_voucher_types as $item_voucher_type)
                    <option value="{{$item_voucher_type->id}}" {{request()->voucher_type_id && request()->voucher_type_id == $item_voucher_type->id ? "selected" : ''}}>{{ucfirst($item_voucher_type->name)}}s</option>
                @endforeach
            </select>
            {{-- @if ($voucher_type)
                <h4>{{$voucher_type->name ?? ""}}s</h4>
            @else
                <h4>{{"Vouchers"}}</h4>
            @endif --}}
        </div>
        <div class="col-lg-12">
            
                <div class="row d-flex align-items-end">
                    <div class="col-lg-2">
                        <label for="">Doc #</label>
                        <div class="input-group input-group-outline">
                            <input type="text" name="search" id="" value="{{request()->search ?? ""}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">From</label>
                        <div class="input-group input-group-outline">
                            <input type="date" name="from" value="{{request()->from ?? ""}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">To</label>
                        <div class="input-group input-group-outline">
                            <input type="date" name="to" value="{{request()->to ?? ""}}" class="form-control">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Account</label>
                        <div class="input-group input-group-outline">
                            <select name="from_account_id" id="" class="form-control">
                                <option value="">All</option>
                                @foreach ($accounts as $key => $from_accountList)
                                <optgroup label="{{ ucfirst($key) }}">
                                    @foreach ($from_accountList as $from_account)
                                        <option 
                                        {{request()->from_account_id && request()->from_account_id == $from_account->id ? 'selected' : '' }} 
                                            value="{{ $from_account->id }}" >
                                            {{ $from_account->title }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
    
                    <div class="col-lg-2">
                        <label for="">Bank</label>
                        <div class="input-group input-group-outline">
                            <select name="account_id" id="" class="form-control">
                                <option value="">All</option>
                                @foreach ($all_accounts as $key => $all_accountList)
                                <optgroup label="{{ ucfirst($key) }}">
                                    @foreach ($all_accountList as $account)
                                        <option 
                                        {{request()->account_id && request()->account_id == $account->id ? 'selected' : '' }}
                                        value="{{ $account->id }}" >
                                            {{ $account->title }}
                                        </option>
                                    @endforeach
                                </optgroup>
                                @endforeach
                            </select>
                        </div>
                    </div>
    
                    <div class="col-lg-1">
                        <label for="">Type</label>
                        <div class="input-group input-group-outline">
                            <select name="type" id="" class="form-control">
                                <option value="">Web</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                    </div>
    
                    <div class="col-lg-1">
                        <button class="btn btn-primary m-0 " type="submit">Filter</button>
                    </div>
    
    
                </div>
            
        </div>
        

    </div>
</form>
    <table class="table table-responsive-sm table-striped table-bordered" border="3">
        <thead>
            <th>Doc #</th>
            <th>Date</th>
            <th>Bank</th>
            <th>Account</th>
            <th>Total</th>
            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($vouchers as $voucher)
                <tr>
                    <td>{{$voucher->doc_no ?? ""}}</td>
                    <td>{{ date('d/m/Y', strtotime($voucher->date)) }}</td>
                    <td>{{$voucher->account->title ?? ""}} - {{$voucher->account->type ?? ""}}</td>
                    <td>{{$voucher->account_from->title ?? ""}} - {{$voucher->account_from->type ?? ""}}</td>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($voucher->total,2)}}</td>
                    <td>
                        <div class="s-btn-grp">
                            <div class="dropdown">
                            <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4  dropdown-toggle" type="button" id="dropdownMenuButton{{$voucher->id}}" data-bs-toggle="dropdown" aria-expanded="true">
                                {{-- <i class="fa fa-list"></i> --}}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$voucher->id}}">  
                                <li><a class="dropdown-item" href="{{url('/voucher/create/'.$voucher->voucher_type_id.'/'.$voucher->id.'')}}"><i class="fa fa-edit"></i> Edit</a></li>
                                <li><a class="dropdown-item" href="{{url('/voucher/detail/'.$voucher->id.'')}}" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a></li>
                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#dltModal{{$voucher->id}}"><i class="fa fa-trash"></i> Delete</a></li>
                            </ul>
                            </div>
                        
                          </div>
                    </td>
                </tr>

                 {{--  Delete Modal  --}}
    
                <div class="modal fade" id="dltModal{{$voucher->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="newStoreModalLabel">Delete Voucher: {{$voucher->doc_no}}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('voucher.delete',$voucher->id)}}" method="POST">
                                @csrf
                                @method('delete')
                            <label class="form-label">Are you sure you want to delete {{$voucher->doc_no}}</label>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-outline-primary">No</button>
                        <button type="submit" class="btn btn-primary">Yes</button>
                        </div>
                    </form>
                    </div>
                    </div>
                </div>
            
                {{--Delete Modal --}}


            @endforeach
        </tbody>
    </table>
</div>
@endsection