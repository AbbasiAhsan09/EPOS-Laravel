@extends('layouts.app')
@section('content')
@include('comp.tvModal', ['src' => 'https://www.youtube.com/embed/hGkaaHxzxlo'])

<div class="page-wrapper">
<div class="">
 
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Accounts</h1>
    </div>
    <div class="col">
        <div class="btn-grp">
         
            <div class="row .row-customized">
                {{-- <div class="col-lg-4">
                    <div class="input-group input-group-outline">
                        <label class="form-label">Search</label>
                        <input type="text" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)">
                      </div>
                  
                </div> --}}
                <div class="col-lg-3">
                <button class="btn btn-outline-primary btn-sm mb-0 w-100" data-bs-toggle="modal" data-bs-target="#accountModal">Add {{request()->has('head_accounts') ? 'Head' : ''}} Account</button>
                </div>
                <div class="col-lg-3">
                  <a class="btn btn-outline-info btn-sm mb-0 w-100" href="/account/journal">Journal Entry</a>
                  </div>
                <div class="col-lg-5">
                  <a class="btn btn-outline-secondary btn-sm mb-0 w-100" href="/account/transactions">Recent Transactions</a>
                  </div>
            </div>
        </div>
    </div>
</div> 
  <div class="btn-group w-100 mt-4">
    <a href="/account" class="btn btn-outline-secondary btn-sm mb-0 {{request()->has('head_accounts') ? '' : 'btn-primary'}}">Accounts</a>
    <a href="/account?head_accounts" id="head_account_btn" class="btn btn-outline-secondary btn-sm mb-0 {{!request()->has('head_accounts') ? '' : 'btn-primary'}} ">Head Accounts</a>
  </div>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>ID</th>
            <th>Title</th>
            <th>Description</th>
            <th>Type</th>
            <th>Opening Balance</th>
            <th>Balance</th>
           
            <th>Actions</th>
        </thead>
        <tbody>
           @foreach ($items as $key => $item)
           {{-- @dump($item) --}}
           <tr>
            <td>{{$item->id}}</td>
            <td>{{$item->title}}</td>
            <td>{{$item->description}}</td>
            <td>{{ucfirst($item->type ?? "")}}</td>
            <td>{{ $item->opening_balance}}</td>
            <td>N/A</td>
            
            <td>
              {{-- @if (!$item->reference_type && !$item->reference_id && $item->title !== 'Cash Sales') --}}
              <div class="s-btn-grp">
                <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" data-bs-toggle="modal" data-bs-target="#accountModal{{$item->id}}">
                    <i class="fa fa-edit"></i>
                </button>
                {{-- <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}">
                    <i class="fa fa-trash"></i>
                </button> --}}
             
              </div>  
              {{-- @else
              @if ($item->title === 'Cash Sales')
              System Defined
              @else
              {{ucfirst($item->reference_type)}}
                  
              @endif
              @endif --}}
            </td>
        </tr>

          <!-- Modal -->
 <div class="modal fade" id="accountModal{{$item->id}}" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="accountModalLabel">Edit Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('account.update',$item->id)}}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Title</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="title" value="{{$item->title ?? ""}}" required>
                    </div>
                    </div>
                    <div class="col-lg-12">
                        <label for="">Description</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="description" value="{{$item->description ?? ""}}">
                    </div>
                    </div>
                    <div class="col-lg-6">
                      <label for="">Chart of Account</label>
                  <div class="input-group input-group-outline">
                    {{-- <input type="number" min="1" step="0.01" class="form-control" name="base_unit_value" required > --}}
                      <select name="coa_id"  class="form-control coa_select" required>
                          <option value="">Select COA</option>
                          @foreach ($coas as $coa)
                          <option value="{{ $coa->id }}"
                            @if (!request()->has('head_accounts'))
                            {{isset($item->parent->parent->id) && $item->parent->parent->id  === $coa->id ? 'selected' : '' }}
                            @else
                            {{isset($item->parent->id) && $item->parent->id  === $coa->id ? 'selected' : '' }}                                
                            @endif
                            >{{ ucfirst($coa->title) }}</option>
                          @endforeach
                      </select>
                  </div>
                  </div>

                  @if (!request()->has('head_accounts'))
                  <div class="col-lg-6">
                    <label for="">Head Account</label>
                <div class="input-group input-group-outline" data-selected-id="{{$item->parent_id ?? ''}}">
                  {{-- <input type="number" min="1" step="0.01" class="form-control" name="base_unit_value" required > --}}
                    <select name="parent_id"  class="form-control head_select" required>
                        <option value="">Select Head</option>
                    </select>
                </div>
                </div>
                  @endif
                    <div class="col-lg-6">
                        <label for="">Opening Balance </label>
                    <div class="input-group input-group-outline">
                      <input type="number"  step="0.01" class="form-control" value="{{$item->opening_balance}}" name="opening_balance" >
                    </div>
                    </div>
                    
                   
                    
                </div>
           
        </div>
        <div class="modal-footer">
          
          <button type="submit" class="btn btn-primary">Update</button>


        </div>
    </form>
      </div>
    </div>
  </div> 
  {{-- Modal --}}



    <!-- Delete Modal -->
    <div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="accountModalLabel">Delete UOM</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form action="{{route('delete.uom',$item->id)}}" method="POST">
                  @csrf
                  @method('put')
                 <label class="form-label">Are you sure you want to delete {{$item->uom}}</label>
             
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
  <div class="modal fade" id="accountModal" tabindex="-1" aria-labelledby="accountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="accountModalLabel">Create {{request()->has('head_accounts') ? 'Head' : ''}} Account</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('account.add')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Title</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="title" required>
                    </div>
                    </div>
                    <div class="col-lg-12">
                        <label for="">Description</label>
                    <div class="input-group input-group-outline">
                      <input type="text" class="form-control" name="description"">
                    </div>
                    </div>
                    <div class="col-lg-6">
                        <label for="">Chart of Account</label>
                    <div class="input-group input-group-outline">
                      {{-- <input type="number" min="1" step="0.01" class="form-control" name="base_unit_value" required > --}}
                        <select name="coa_id"  class="form-control coa_select" required>
                            <option value="">Select COA</option>
                            @foreach ($coas as $coa)
                            <option value="{{ $coa->id }}">{{ ucfirst($coa->title) }}</option>
                            @endforeach
                        </select>
                    </div>
                    </div>

                    @if (!request()->has('head_accounts'))
                    <div class="col-lg-6">
                      <label for="">Head Account</label>
                  <div class="input-group input-group-outline">
                    {{-- <input type="number" min="1" step="0.01" class="form-control" name="base_unit_value" required > --}}
                      <select name="parent_id"  class="form-control head_select" required>
                          <option value="">Select Head</option>
                      </select>
                  </div>
                  </div>
                    @endif
                   


                    <div class="col-lg-6">
                        <label for="">Opening Balance </label>
                    <div class="input-group input-group-outline">
                      <input type="number"  step="0.01" class="form-control" name="opening_balance" >
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
{{-- 
  <script>
    $(document).ready(function() {
        $('#head_account_btn').on('click', function() {
            var currentUrl = window.location.href;

            // Check if the URL already has query parameters
            if (currentUrl.indexOf('?') > -1) {
                // If 'head_accounts' is already in the URL, don't add it again
                if (!currentUrl.includes('head_accounts')) {
                    // Append 'head_accounts' to the existing query string
                    window.location.href = currentUrl + '&head_accounts=true';
                }
            } else {
                // If there are no query parameters, add 'head_accounts' as the first one
                window.location.href = currentUrl + '?head_accounts=true';
            }
        });
    });
</script> --}}

<script>
      $(document).ready(function() {

        $('.coa_select').change(function(){
          var selectedCoaId = $(this).val();
          var $headSelect = $(this).closest('.modal-body').find('.head_select');
          if($headSelect && selectedCoaId){
            // Clear previous head account options
            $headSelect.empty().append('<option value="">Select Head</option>');

            if(selectedCoaId){
               // Make AJAX call to fetch head accounts
              $.ajax({
                  url: `/api/account/coa/${+selectedCoaId}/{{Auth::user()->store_id}}`, // Replace with your API endpoint
                  method: 'GET',
                  data: { coa_id: selectedCoaId },
                  success: function(response) {
                      // Assuming response is an array of head accounts
                      $.each(response, function(index, headAccount) {
                          // Append head account options
                          $headSelect.append('<option value="' + headAccount.id + '">' + headAccount.title + '</option>');
                      });
                  },
                  error: function(xhr, status, error) {
                      console.error('Error fetching head accounts:', error);
                      // Handle error (optional)
                  }
              });
            }
          }
         
        })

      });
</script>
@endsection