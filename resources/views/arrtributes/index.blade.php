@extends('layouts.app')
@section('content')

<div class="page-wrapper">
<div class="container">

    <div class="row row-customized">
        <div class="col">
            <h1 class="page-title">Product Arrtribute</h1>
        </div>
        <div class="col">
            <div class="btn-grp">
             
                <div class="row .row-customized">
                    <div class="col-lg-8">
                        <div class="input-group input-group-outline">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" onfocus="focused(this)" onfocusout="defocused(this)">
                          </div>
                      
                    </div>
                    <div class="col-lg-4">
                    <button class="btn btn-outline-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal">New Arrtribute</button>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-sm table-responsive-sm table-striped">
        <thead>
            <th>S#</th>
            <th>Arrtribute</th>
            
           
            <th>Status</th>
           
            <th>Actions</th>
        </thead>
        <tbody>
        @foreach ($items as $key => $item)
            <tr>
                <td>{{$key+1}}</td>
                <td>{{$item->arrtribute}}</td>
                <td>Active</td>
                <td>
                    <div class="s-btn-grp">
                        <button class="btn btn-link text-dark text-sm mb-0 px-0 ms-4" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}">
                            <i class="fa fa-edit"></i>
                        </button>
                        <button class="btn btn-link text-danger text-gradient px-3 mb-0" data-bs-toggle="modal" data-bs-target="#newStoreModal{{$item->id}}">
                            <i class="fa fa-trash"></i>
                        </button>
                     
                    </div>

                      <!-- Modal -->
  <div class="modal fade" id="newStoreModal{{$item->id}}" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Create New Arrtribute</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('add.arrtribute')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Arrtribute</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="arrtribute" required>
                        </div>
                    </div>
                        <div class="col-lg-12">
                            <table class="table table-sm table-responsive-sm ">
                               <thead>
                                <th width="90%">Value</th>
                                <th style="width:10px">Actions</th> 
                            </thead>
                            <tbody >
                                <tr>
                                    <td>
                        <div class="input-group input-group-outline">
                           
                            <input type="text" class="form-control" name="value[]" required>
                        </div>
                                    </td>    
                                    <td>
                                        <button class="btn btn-sm btn-success" type="button" >+</button>    
                                           
                                    </td>    
                                </tr>    
                            </tbody>   
                            </table>    
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
                </td>
                

            </tr>


            

        @endforeach
        </tbody>
    </table>
</div>
</div>

  
  <!-- Modal -->
  <div class="modal fade" id="newStoreModal" tabindex="-1" aria-labelledby="newStoreModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="newStoreModalLabel">Create New Arrtribute</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form action="{{route('add.arrtribute')}}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-lg-12">
                        <label for="">Arrtribute</label>
                        <div class="input-group input-group-outline">
                            <input type="text" class="form-control" name="arrtribute" required>
                        </div>
                    </div>
                        <div class="col-lg-12">
                            <table class="table table-sm table-responsive-sm ">
                               <thead>
                                <th width="90%">Value</th>
                                <th style="width:10px">Actions</th> 
                            </thead>
                            <tbody id="art-value-row">
                                <tr>
                                    <td>
                        <div class="input-group input-group-outline">
                           
                            <input type="text" class="form-control" name="value[]" required>
                        </div>
                                    </td>    
                                    <td>
                                        <button class="btn btn-sm btn-success" id="add-art-vl-row" type="button" >+</button>    
                                           
                                    </td>    
                                </tr>    
                            </tbody>   
                            </table>    
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
  <script>
    $(document).ready(function(){
        console.log('I am working');
    $('#add-art-vl-row').click(function(){
        console.log('12')
        $('#art-value-row').append('<tr>'+
       '<td><div class="input-group input-group-outline"><input type="text" name="value[]"  class="form-control" required></div></td> '+   
        '<td>'+
            
            '<button class="btn btn-sm btn-danger dlt-row" type="button"  >-</button>'+    
               
        '</td>'+    
    '</tr>')
    });



});

    //deleting rows
    $('table').on('click','.dlt-row',function(){
        console.log('working');
        $(this).closest('tr').remove();
    });
  </script>
@endsection