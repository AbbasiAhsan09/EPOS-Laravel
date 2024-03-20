@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Purchase Invoices</h1>
    </div>
    <div class="col">
        <div class="btn-grp">
         
            <div class="row .row-customized">
                <div class="col-lg-12">
                    <div class="input-group input-group-outline">
                        
                        <select name="type" id="" class="form-control">
                            <option value="">--Select Status--</option>
                            <option value="all">All</option>
                            <option value="current">Current</option>
                            <option value="deleted">Deleted</option>
                        </select>
                      </div>
                  
                </div>
                
            </div>
        </div>
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

            @if (isset($dynamicFields) && count($dynamicFields->fields) )
                    @foreach ($dynamicFields->fields as $dynamicField)
                    @if ($dynamicField->show_in_table)
                    <th>{{$dynamicField->label ?? ""}}</th>
                    @endif
                    @endforeach
            @endif

            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($invoices as $key => $item)
                <tr >
                    <td>{{$key+1}}</td>
                    <td>{{$item->doc_num}}</td>
                    <td>
                      @if (isset($item->order->id))
                      <a href="{{url("/purchase/order/".$item->order->id."/edit")}}" style="font-style: italic">{{$item->order->doc_num ?? '(Null)'}}</a>                          
                      @endif
                    </td>
                    
                    <td>{{$item->party->party_name}}</td>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '.$item->total}}</td>
                    <td class="text-primary">
                      <b>  {{ConfigHelper::getStoreConfig()["symbol"].' '.($item->total - $item->discount) + $item->other_charges }}</b>
                    </td>
                    <td>{{$item->created_by_user->name}}</td>
                    <td>{{date('d.m.y | h:m A' , strtotime($item->created_at))}}</td>
                     {{-- Dynamic Fields --}}
                     
                @if (isset($dynamicFields) && count($dynamicFields->fields) )
                @foreach ($dynamicFields->fields as $dynamicField)
                        @if ($dynamicField->show_in_table)
                        <td>
                                @if (count($item->dynamicFeildsData))
                                    @foreach ($item->dynamicFeildsData as $dynamicFieldData)
                                        @if ($dynamicFieldData && $dynamicFieldData->field_id === $dynamicField->id)
                                        {{$dynamicFieldData->value}}
                                       
                                        @endif
                                    @endforeach
                                
                                @endif
                            </td>        
                            
                @endif
                @endforeach
                @endif
                
                {{-- Dynamic Feild End --}}
                
                    <td>
                        <div class="s-btn-grp">
                            <div class="dropdown">
                            <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4  dropdown-toggle" type="button" id="dropdownMenuButton{{$item->id}}" data-bs-toggle="dropdown" aria-expanded="true">
                                {{-- <i class="fa fa-list"></i> --}}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$item->id}}">
                                {{-- <li><a class="dropdown-item" href="#{{$item->id}}"><i class="fa fa-eye"></i> View</a></li> --}}
                                @if ($item->deleted_at === null)
                                {{-- <li><a class="dropdown-item popup" href="{{url("/invoice/".$item->id."")}}"><i class="fa fa-file-invoice"></i> Print Invoice</a></li> --}}
                                <li><a class="dropdown-item" href="{{url("/purchase/invoice/$item->id/edit")}}"><i class="fa fa-edit"></i> Edit</a></li>
                                <li><a class="dropdown-item"  data-bs-toggle="modal" data-bs-target="#paymentHistory{{$item->id}}"><i class="fa fa-dollar"></i> Transaction History</a></li>
                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}"><i class="fa fa-trash"></i> Delete</a></li>
                                @endif
                            </ul>
                            </div>
                        
                          </div>
{{-- 
                        <div class="s-btn-grp">
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$item->id}}">
                                <li>
                            <a class="btn btn-link text-dark text-sm mb-0 px-0 ms-4 {{$item->created_at != $item->updated_at ? 'text-primary' : ''}}" href="{{url("/purchase/invoice/$item->id/edit")}}"><i class="fa fa-edit"></i></a>
                                </li>
                                <li>
                                </li>
                            </ul>
                        </div> --}}
                    </td>
                </tr>

                
    {{--  Delete Modal  --}}
    
    <div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="newStoreModalLabel">Delete Sale: {{$item->doc_num}}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('invoice.destroy',$item->id)}}" method="POST">
                    @csrf
                    @method('delete')
                   <label class="form-label">Are you sure you want to delete {{$item->doc_num}}</label>
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


                      
    {{--  paymentHistory Modal  --}}
    
    <div class="modal fade" id="paymentHistory{{$item->id}}" tabindex="-1" aria-labelledby="paymentHistory" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="paymentHistory">Transaction History: {{$item->doc_num}}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
               @if (isset($item->transactions ) && count($item->transactions ))
               <ul>
                @foreach ($item->transactions as $transaction)
                <li>{{$item->doc_num}} | {{date('m-d-y',strtotime($transaction->created_at))}} | {{ConfigHelper::getStoreConfig()["symbol"].$transaction->amount}}</li>
                @endforeach
                </ul>
            @else
            <h6>There is no transaction currently for {{$item->doc_num}}.</h6>
            @endif
            </div>
           
          </div>
        </div>
      </div>
  
      {{--paymentHistory Modal --}}

            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {!! $invoices->links('pagination::bootstrap-4') !!}
    </div>
</div>
</div>



@endsection