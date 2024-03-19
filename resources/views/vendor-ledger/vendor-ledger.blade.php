@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Vendor Ledger</h1>
    </div>
    <div class="col">
        <form action="{{ route('vendor-ledger.index') }}" method="GET">
            <div class="row flex-end">
                <div class="col-lg-3">
                    <label for="">From</label>
                     <div class="input-group input-group-outline">  
                       <input type="date" class="form-control" name="start_date" value="{{session()->get('p_start_date')}}">
                      </div>   
                </div>
                <div class="col-lg-3">
                    <label for="">To</label>
                     <div class="input-group input-group-outline">  
                       <input type="date" class="form-control" name="end_date" value="{{session()->get('p_end_date')}}">
                      </div>   
                </div>
                <div class="col-lg-3">
                    <label for="">Vendors</label>
                     <div class="input-group input-group-outline">  
                      <select name="vendor_id" id="" class="select2Style form-control ">
                        <option value="">All</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{$vendor->id}}" {{session()->get('l_vendor_id') == $vendor->id ? 'selected' : ''}}>{{$vendor->party_name}}</option>
                        @endforeach

                      </select>
                      </div>   
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-primary btn-block">Search</button>
                </div>
                {{-- <div class="col-lg-5">
                <button class="btn btn-outline-secondary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">Filter</button>
                <a href="/sales/add" class="btn btn-outline-primary btn-sm mb-0" >Create Order</a href="/sales/add">

                </div> --}}
            </div>
        </form>
     
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
           <th>Customer</th>
           <th>Balance</th>
           <th>Actions</th>
        </thead>
        <tbody>
           @foreach ($items as $key => $item)
           @if (round($item->purchases->sum('net_amount') - $item->purchases->sum('recieved')) > 0.99)
           <tr>
            <td>{{ $item->party_name }}</td>
            {{-- <td>{{$item->purchases}}</td> --}}
            <td>{{ ConfigHelper::getStoreConfig()["symbol"].round($item->purchases->sum('net_amount') - $item->purchases->sum('recieved'))  }}</td>
            <td>
                <div class="s-btn-grp">
                    <div class="dropdown">
                    <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4  dropdown-toggle" type="button" id="dropdownMenuButton{{$item->id}}" data-bs-toggle="dropdown" aria-expanded="true">
                        {{-- <i class="fa fa-list"></i> --}}
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$item->id}}">
                        <li><a class="dropdown-item" href="{{route('vendor-ledger.show',$item->id)}}">
                            <i class="fa fa-info-circle"></i> Details</a>
                        </li>
                    </ul>
                    </div>
                 
                </div>
            </td>

        </tr>
           @endif
           @endforeach
        </tbody>
    </table>
    
    {{$items->links('pagination::bootstrap-4')}}

</div>
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        $('.select2Style').select2({
                    placeholder: "All",
                    allowClear: true
                });
    });
        </script>
@endsection

@endsection