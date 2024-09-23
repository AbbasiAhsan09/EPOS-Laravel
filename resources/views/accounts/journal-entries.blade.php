@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Transactions</h1>
    </div>
    <div class="col-lg-8">
        <div class="btn-grp">
         
            <div class="row row-customized">
                <div class="col-lg-12">
                    <form action="{{url('/account/transactions')}}" method="GET">
                     <div class="row justify-content-center">
                      <div class="col-lg-2">
                        <label class="form-label">From</label>
                        <div class="input-group input-group-outline">
                          <input type="date" class="form-control" value="{{ (session('j_entry_from')) ? session('j_entry_from') : ''  }}" name="from"
                           >
                        </div>
                      </div>
                      <div class="col-lg-2">
                        <label class="form-label">To</label>
                        <div class="input-group input-group-outline">
                          <input type="date" class="form-control" value="{{ (session('j_entry_to')) ? session('j_entry_to') : ''  }}" name="to"
                           >
                        </div>
                      </div>
                      <div class="col-lg-2">
                        <label class="form-label">Account</label>
                        <div class="input-group input-group-outline">
                          <select name="account_id" id="" class="form-control">
                            <option value="">All</option>
                            @foreach ($accounts as $account)
                            <option value="{{$account->id}}" {{ (session('j_entry_account_id')) && (session('j_entry_account_id')) == $account->id ? 'selected' : ''  }}>{{$account->title}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-2">
                        <label class="form-label">T. Type</label>
                        <div class="input-group input-group-outline">
                          <select name="transaction_type" id="" class="form-control">
                            <option value="">All</option>
                            @foreach (['credit', 'debit'] as $value)
                            <option value="{{$value}}"  {{ (session('j_entry_transaction_type')) && (session('j_entry_transaction_type')) === $value ? 'selected' : ''  }}>{{$value === 'credit' ? 'Received' : 'Paid'}}</option>
                            @endforeach
                          </select>
                        </div>
                      </div>

                      <div class="col-lg-2">
                        <button class="btn btn-primary" type="submit">Filter</button>
                      </div>

                     </div>
                    </form>
                  
                </div>
                <div class="col-lg-4">
            </div>
        </div>
    </div>
</div>
  <h1>Total Credit : {{$entries->sum("credit")}}</h1>
  <h1>Total Debit : {{$entries->sum("debit")}}</h1>
    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>S#</th>
            <th>Date</th>
            <th>Account</th>
            <th>Description</th>
            <th>Credit</th>
            <th>Debit</th>
            <th>Actions</th>
        </thead>
        <tbody>
           @foreach ($entries as $key => $item)
           <tr>
            {{-- @dd($item) --}}
            <td>{{$key+1}}</td>
            <td>{{$item->transaction_date ?? ""}}</td>
            <td>{{ $item->account->title ?? ""}}</td>
            <td>{{$item->note ?? ""}}</td>
            <td><strong>{{$item->credit ?? 0}}</strong></td>
            <td><strong>{{$item->debit ?? 0}}</strong></td>
     
           @if (isset($item->account->pre_defined) && $item->account->pre_defined)
           <td>System Manages</td>
           @else
           <td>
            <div class="s-btn-grp">
              {{-- <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}">
                  <i class="fa fa-edit"></i>
              </button> --}}
              <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}">
                  <i class="fa fa-trash"></i>
              </button>
           
          </div>
          </td>
           @endif
        </tr>

          <!-- Modal -->
  {{-- <div class="modal fade" id="newStoreModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Edit Transaction</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('journal.update',$item->id)}}" method="POST">
                @csrf
                @method('put')
                <div class="row">
                
                  <div class="col-lg-12">
                    <label for="">Field</label>
              <div class="input-group input-group-outline">
               <select name="field" id="" class="form-control" required>
                <option value="">Select Field</option>
                @foreach ($fields as $field)
                    <option value="{{$field->id}}" {{$item->parent_cat == $field->id ? 'selected' : ''}}>{{$field->name}}</option>
                @endforeach
               </select>
              </div>
                </div>

                    <div class="col-lg-12">
                      <label for="">Category Name</label>
                <div class="input-group input-group-outline">
                  <input type="text" class="form-control" name="category" required  value="{{$item->category}}">
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
  </div> --}}
  {{-- Modal --}}




    <!-- Modal -->
    <div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="newStoreModalLabel">Delete Transaction</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
              <form action="{{route('journal.delete',$item->id)}}" method="POST">
                  @csrf
                  @method('delete')
                  <div class="row">
                  
                  <label class="form-label">Are you sure you want to delete transaction?</label>
                  </div>
             
          </div>
          <div class="modal-footer">
            
            <button type="button" class="btn btn-outline-primary">No</button>
            <button type="submit" class="btn btn-primary">Yes</button>
          </div>
      </form>
        </div>
      </div>
    </div>
    {{-- Modal --}}
           @endforeach
        </tbody>
    </table>
    {{$entries->links('pagination::bootstrap-4')}}

</div>
</div>

  
  <!-- Modal -->
  {{-- <div class="modal fade" id="newStoreModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Create New Category</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('add.category')}}" method="POST">
                @csrf
                <div class="row">
                  <div class="col-lg-12">
                    <label for="">Field</label>
              <div class="input-group input-group-outline">
               <select name="field" id="" class="form-control" required>
                <option value="">Select Field</option>
                @foreach ($fields as $field)
                    <option value="{{$field->id}}">{{$field->name}}</option>
                @endforeach
               </select>
              </div>
                </div>

                    <div class="col-lg-12">
                        <label for="">Category Name</label>
                  <div class="input-group input-group-outline">
                    <input type="text" class="form-control" name="category" required>
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
  </div> --}}
  {{-- Modal --}}
@endsection