@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container-fluid">
  <div class="row row-customized">
    <div class="col">
        <h1 class="page-title">Purchase Orders</h1>
    </div>
    <div class="col">
        <div class="btn-grp">
         
            <div class="row .row-customized">
                <div class="col-lg-8">
                    {{-- <div class="input-group input-group-outline">
                        
                        <select name="" id="" class="form-control">
                            <option value="">--Select Status--</option>
                            <option value="1">Approved</option>
                        </select>
                      </div> --}}
                  
                </div>
                <div class="col-lg-4">
                <a href="{{url("/purchase/order/create")}}" class="btn btn-outline-primary btn-sm mb-0" >Create PO</a>

                </div>
            </div>
        </div>
    </div>
</div>
    <table class="table table-sm table-responsive-sm table-striped ">
        <thead>
            <th>S#</th>
            <th>Doc #</th>
            <th>Quotation #</th>
            <th>Type</th>
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
            @foreach ($orders as $key => $item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->doc_num}}</td>
                    <td><a href="" style="font-style: italic">{{$item->quotation_num ?? '(Null)'}}</a></td>
                    <td>{{$item->type ?? "STANDARD"}}</td>
                    <td>{{$item->party->party_name ?? 'deleted '}}</td>
                    <td>{{ConfigHelper::getStoreConfig()["symbol"].' '.$item->sub_total}}</td>
                    <td class="text-primary">
                      <b>  {{ConfigHelper::getStoreConfig()["symbol"].' '.($item->sub_total - $item->discount) + $item->other_charges }}</b>
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
                                <li><a class="dropdown-item" href="{{url("/purchase/invoice/$item->id/create")}}"><i class="fa fa-file-invoice"></i> Create Invoice</a></li>
                                <li><a class="dropdown-item" href="{{url("/purchase/order/$item->id/edit")}}"><i class="fa fa-edit"></i> Edit</a></li>
                                <li><a class="dropdown-item popup" href="{{url("/purchase/order/print/$item->id/")}}"><i class="fa fa-file-invoice"></i> Print</a></li>
                                @if (count($item->invoices))
                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}"><i class="fa fa-file-invoice" ></i> Invoices</a></li>
                                @endif
                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#dltModal{{$item->id}}"><i class="fa fa-trash"></i> Delete</a></li>
                            </ul>
                            </div>
                         
                        </div>
                    </td>
                </tr>


                
          <!-- Modal -->
                <div class="modal fade" id="newStoreModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                        <h5 class="modal-title" id="newStoreModalLabel">Invoices : </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                        <ul>
                            @foreach ($item->invoices as $invoice)
                            <li>
                                <a href="{{route('invoice.edit',$invoice->id)}}">{{'Inv # '.$invoice->doc_num .' | Date: '.date('d.m.y' , strtotime($invoice->created_at))}}</a>
                            </li>
                                
                            @endforeach
                        </ul>
                        </div>
                        
                    </div>
                    </div>
                </div>
                {{-- Modal --}}



                                
    {{--  Delete Modal  --}}
    
    <div class="modal fade" id="dltModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="newStoreModalLabel">Delete P.O: {{$item->doc_num}}</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form action="{{route('order.destroy',$item->id)}}" method="POST">
                    @csrf
                    @method('delete')
                   <label class="form-label">Are you sure you want to delete {{$item->doc_num}}</label>
                   <label for="form-label text-danger"><p class="text-danger " style="color: red; font-weight: 600">{{count($item->invoices)  ? "There are purchase invoices created for this order!" : ""}}</p></label>
            </div>
            <div class="modal-footer">
              {{-- <button type="button" class="btn btn-outline-primary">No</button> --}}
              <button type="submit" class="btn btn-primary" {{count($item->invoices)  ? "disabled" : ""}}>Yes</button>
            </div>
        </form>
          </div>
        </div>
      </div>
  
      {{--Delete Modal --}}



            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {!! $orders->links('pagination::bootstrap-4') !!}
    </div>
</div>
</div>

<script>
    $('.popup').click(function(event) {
        event.preventDefault();
        window.open($(this).attr("href"), "popupWindow", "width=300,height=600,scrollbars=yes,left="+($(window).width()-400)+",top=50");
    });
</script>

@endsection