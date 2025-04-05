@extends('layouts.app')
@section('content')
@include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/eNhzTjJ2rIE'])

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-4">
        <h1 class="page-title">Purchase Returns</h1>
    </div>
    <div class="col-lg-8">
        <div class="btn-grp">
         
            <form action="{{route("index.purchase_return")}}" method="GET">
              <div class="row align-items-end">
             
                <div class="col-lg-2">
                    <label for="">From</label>
                    <div class="input-group input-group-outline">
                    <input type="date" class="form-control" name="start_date" id="start_date" value="{{session()->get('preturn_start_date') ?? null}}" >
                    </div>
                </div> 

                <div class="col-lg-2">
                    <label for="">To</label>
                    <div class="input-group input-group-outline">
                    <input type="date" class="form-control" name="end_date" id="end_date" value="{{session()->get('preturn_end_date') ?? null}}" >
                    </div>
                </div> 

                <div class="col-lg-2">
                    <label for="">Party</label>
                    <div class="input-group input-group-outline">
                        <select name="party_id" class="form-control">
                            <option value="">All</option>
                            @foreach ($parties as $group => $partiesGroups)
                                    <optgroup label="{{ucfirst($group)}}">
                                        @foreach ($partiesGroups as $party)
                                            <option {{$party->id == session()->get('preturn_party_id') ? 'selected' : null }} value="{{$party->id}}">{{$party->party_name}}</option>
                                        @endforeach
                                    </optgroup>
                            @endforeach
                        </select>
                    </div>
                </div> 

                <div class="col-lg-2">
                    <label for="">Type</label>
                    <div class="input-group input-group-outline">
                        <select name="type" class="form-control">
                            <option value="web">WEB</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div> 

                <div class="col-lg-4">
                <button class="btn btn-secondary  mb-0" type="submit">Filter</button>
                <a href="/sales/return" class="btn btn-primary  mb-0" >Create Return</a href="/sales/add">

                </div>
            </div>
            </form>
        </div>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>ID</th>
            <th>Transaction #</th>
            <th>Party</th>
            <th>Created at</th>
            <th>User</th>
            <th>Net Amount</th>
            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 )
            <th>Actions</th>
            @endif
        </thead>
        <tbody>
           @foreach ($items as $key => $item)
           <tr >
         <td>{{$item->id}}</td>
         <td  class="{{$item->deleted_at !== null ? 'text-danger' : ''}}">{{$item->doc_no}}</td>
         <td>{{isset($item->party) ? $item->party->party_name : 'Cash'}}</td>    
        <td>{{date('d-M-y | h:m:A' , strtotime($item->created_at))}}</td>
        <td>{{$item->user->name}}</td>  
        <td> {{ConfigHelper::getStoreConfig()["symbol"].$item->net_total}}</td> 
            @if (Auth::user()->role_id == 1 || Auth::user()->role_id == 2 )
            <td>
                <div class="s-btn-grp">
                  <div class="dropdown">
                  <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4  dropdown-toggle" type="button" id="dropdownMenuButton{{$item->id}}" data-bs-toggle="dropdown" aria-expanded="true">
                      {{-- <i class="fa fa-list"></i> --}}
                  </button>
                  <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$item->id}}">
                      {{-- <li><a class="dropdown-item" href="#{{$item->id}}"><i class="fa fa-eye"></i> View</a></li> --}}
                      @if ($item->deleted_at === null)
                      <li><a class="dropdown-item" href="{{url('/purchase/return/'.$item->id.'')}}"><i class="fa fa-edit"></i> Edit</a></li>
                      <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}"><i class="fa fa-trash"></i> Delete</a></li>
                      @endif
                  </ul>
                  </div>
              
                </div>
              </td>
            @endif

        </tr>

    {{--  Delete Modal  --}}
    
    <div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newStoreModalLabel">Delete Purchase Return: {{$item->doc_no}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form action="{{route('delete.purchase_return',$item->id)}}" method="POST">
                  @csrf
                  @method('delete')
                 <label class="form-label">Are you sure you want to delete {{$item->doc_no}}</label>
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
    
    {{$items->links('pagination::bootstrap-4')}}

</div>
</div>



@endsection