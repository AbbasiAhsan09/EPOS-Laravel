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
                    <div class="input-group input-group-outline">
                        
                        <select name="" id="" class="form-control">
                            <option value="">--Select Status--</option>
                            <option value="1">Approved</option>
                        </select>
                      </div>
                  
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

            <th>Actions</th>
        </thead>
        <tbody>
            @foreach ($orders as $key => $item)
                <tr>
                    <td>{{$key+1}}</td>
                    <td>{{$item->doc_num}}</td>
                    <td><a href="" style="font-style: italic">{{$item->quotation_num ?? '(Null)'}}</a></td>
                    <td>{{$item->type ?? "STANDARD"}}</td>
                    <td>{{$item->party->party_name}}</td>
                    <td>{{env('CURRENCY').' '.$item->sub_total}}</td>
                    <td class="text-primary">
                      <b>  {{env('CURRENCY').' '.($item->sub_total - $item->discount) + $item->other_charges }}</b>
                    </td>
                    <td>{{$item->created_by_user->name}}</td>
                    <td>{{date('d.m.y | h:m A' , strtotime($item->created_at))}}</td>
                    <td>
                        <div class="s-btn-grp">
                            <div class="dropdown">
                            <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4  dropdown-toggle" type="button" id="dropdownMenuButton{{$item->id}}" data-bs-toggle="dropdown" aria-expanded="true">
                                {{-- <i class="fa fa-list"></i> --}}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton{{$item->id}}">
                                <li><a class="dropdown-item" href="#{{$item->id}}"><i class="fa fa-eye"></i> View</a></li>
                                <li><a class="dropdown-item" href="{{url("/purchase/invoice/$item->id/create")}}"><i class="fa fa-file-invoice"></i> Create Invoice</a></li>
                                <li><a class="dropdown-item" href="{{url("/purchase/order/$item->id/edit")}}"><i class="fa fa-edit"></i> Edit</a></li>
                                @if (count($item->invoices))
                                <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}"><i class="fa fa-file-invoice" ></i> Invoices</a></li>
                                @endif
                                <li><a class="dropdown-item" href="#"><i class="fa fa-trash"></i> Delete</a></li>
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
                                <a href="{{route('invoice.show',$invoice->id)}}">{{'Inv # '.$invoice->doc_num .' | Date: '.date('d.m.y' , strtotime($invoice->created_at))}}</a>
                            </li>
                                
                            @endforeach
                        </ul>
                        </div>
                        
                    </div>
                    </div>
                </div>
                {{-- Modal --}}




            @endforeach
        </tbody>
    </table>
    <div class="d-flex justify-content-center">
        {!! $orders->links('pagination::bootstrap-4') !!}
    </div>
</div>
</div>

@endsection