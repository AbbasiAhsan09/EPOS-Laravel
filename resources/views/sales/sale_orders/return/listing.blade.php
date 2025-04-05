@extends('layouts.app')
@section('content')
@include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/eNhzTjJ2rIE'])

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col-lg-4">
        <h1 class="page-title">Sale Returns</h1>
    </div>
    <div class="col-lg-8">
        <div class="btn-grp">
         
            <form action="{{route("index.return")}}" method="GET">
              <div class="row align-items-end">
             
                <div class="col-lg-2">
                    <label for="">From</label>
                    <div class="input-group input-group-outline">
                    <input type="date" class="form-control" name="start_date" id="start_date" value="{{session()->get('sreturn_start_date') ?? null}}" >
                    </div>
                </div> 

                <div class="col-lg-2">
                    <label for="">To</label>
                    <div class="input-group input-group-outline">
                    <input type="date" class="form-control" name="end_date" id="end_date" value="{{session()->get('sreturn_end_date') ?? null}}" >
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
                                            <option {{$party->id == session()->get('sreturn_party_id') ? 'selected' : null }} value="{{$party->id}}">{{$party->party_name}}</option>
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
                      <li><a class="dropdown-item popup" href="{{url("/invoice/return/".$item->id."")}}"><i class="fa fa-file-invoice"></i> Print Return Invoice</a></li>
                      <li><a class="dropdown-item" href="{{url('/sales/return/'.$item->id.'')}}"><i class="fa fa-edit"></i> Edit</a></li>
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
            <h5 class="modal-title" id="newStoreModalLabel">Delete Sale Return: {{$item->doc_no}}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form action="{{route('delete.return',$item->id)}}" method="POST">
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

  <!-- Modal -->
  <div class="modal fade" id="newStoreModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Filter</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
       <div class="modal-body">
            <form id="search_filter">
             
                <div class="row">
                    <div class="col-lg-6">
                        <label for="">Start Date</label>
                    <div class="input-group input-group-outline">
                      <input type="date" class="form-control" id="start_date" value="{{session()->get('start_date')}}" required>
                    </div>
                    </div> 
                    <div class="col-lg-6">
                      <label for="">End Date</label>
                  <div class="input-group input-group-outline" >
                    <input type="date" class="form-control" id="end_date" value="{{session()->get('end_date')}}" required>
                  </div>
                  </div>  
                </div>
           
        </div> 
        <div class="modal-footer">
          
          <button type="submit" class="btn btn-primary">Save</button>
        </div>
    </form>
      </div>
    </div>
  </div>
  {{-- Modal --}}

  @section('scripts')
      <script>
        $('#type_search').change(function(){
          window.location.replace('/sales/returns?type='+$(this).val());
        });

        $('#search_filter').submit(function(e){

          e.preventDefault();
          var start_date = $('#start_date').val();
          var end_date = $('#end_date').val();
          var type = $('#type_search').val();
          window.location.replace('/sales?type='+type+'&start_date='+start_date+'&end_date='+end_date);
        
        });

    $('.popup').click(function(event) {
        event.preventDefault();
        window.open($(this).attr("href"), "popupWindow", "width=300,height=600,scrollbars=yes,left="+($(window).width()-400)+",top=50");
    });

    $('.process_status_select').change(function(){
      var current  = $(this);
      var order_id = current.attr('data-sales-id');
      var status = current.val();

      var params = `sales/change-status?status=${status}&order_id=${order_id}`;
      var url = `{{url('${params}')}}`;
      window.location = url;
      console.log(url);
      
    })

    $("#process_status_parent .nav-link").click(function(){
    
      
            // Get the current URL
            var currentUrl = window.location.href;

            // Check if the "status" parameter is already present
            var statusParam = 'status=';
            var statusIndex = currentUrl.indexOf(statusParam);

            if (statusIndex !== -1) {
                // Extract the value of the existing "status" parameter
                var statusValue = currentUrl.substring(statusIndex + statusParam.length);
                var ampersandIndex = statusValue.indexOf('&');
                if (ampersandIndex !== -1) {
                    statusValue = statusValue.substring(0, ampersandIndex);
                }

                // Remove the existing "status" parameter
                currentUrl = currentUrl.replace(new RegExp('[?&]' + statusParam + statusValue), '');
            }

            // Add the "status=pending" parameter
            var newUrl = currentUrl + (currentUrl.includes('?') ? '&' : '?') + 'status='+$(this).attr('data-value');

            // Redirect to the new URL
            window.location.href = newUrl;

    })
      </script>
  @endsection

@endsection