@extends('layouts.app')
@section('content')

<div class="page-wrapper">
    <div class="container-fluid">
      <div class="row row-customized">
        <div class="col">
            <h1 class="page-title">Journal Entries</h1>
        </div>
      </div>
    </div>
</div>
<form action="{{route("journal.post")}}" method="POST">
    @csrf
    <button class="btn btn-primary">Save</button>

<table class="table table-responsive table-bordered" id="journal-entry-table">
    <thead>
        <th style="width: 200px;">Date</th>
        <th style="width: 300px;">Account</th>
        <th >Description</th>
        <th style="width: 200px;">Credit</th>
        <th style="width: 200px;">Debit</th>
        <th>Balance</th>
        <th >Action</th>
    </thead>
    <tbody>
        <tr class="journal-entry">
            <td>
                <div class="input-group input-group-outline">
                    <input type="date" name="transaction_date[]" class="form-control transaction_date" required 
                           value="{{date('Y-m-d')}}">
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline">  
                    <select name="account_id[]" id="" class="  form-control " required>
                        <option value="">Select Account</option>
                        @foreach ($accounts as $account)
                            <option value="{{$account->id}}">{{$account->title}}</option>
                        @endforeach
                    </select>
                </div>  
            </td>
            <td>
                <div class="input-group input-group-outline">
                    <input type="text" class="form-control" name="note[]">
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline">
                    <input type="number" min="0" class="form-control" value="0" required name="credit[]">
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline">
                    <input type="number" min="0" class="form-control" value="0" required name="debit[]">
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline">
                <input type="text" value="N/A" class="form-control balance"  disabled >
                </div>
            </td>
            <td>
                <button class="btn btn-primary add-row">
                    <i class="fa fa-add"></i> 
                </button>
                <button class="btn btn-danger remove-row">
                    <i class="fa fa-trash"></i> 
                </button>
            </td>
        </tr>
    </tbody>
</table>
</form>

<script>
$(document).ready(function() {
 
    function toggleFields() {
        $('tbody .journal-entry').each(function() {
            var credit = $(this).find('input[name="credit[]"]').val();
            var debit = $(this).find('input[name="debit[]"]').val();

            if (credit > 0) {
                $(this).find('input[name="debit[]"]').prop('disabled', true);
            } else {
                $(this).find('input[name="debit[]"]').prop('disabled', false);
            }

            if (debit > 0) {
                $(this).find('input[name="credit[]"]').prop('disabled', true);
            } else {
                $(this).find('input[name="credit[]"]').prop('disabled', false);
            }
        });
    }

    $(document).on('input', 'input[name="credit[]"], input[name="debit[]"]', function() {
        toggleFields();
    });

    toggleFields();

    // Set today's date for the first row
    setTodayDate();

    // Function to set today's date in 'YYYY-MM-DD' format
    function setTodayDate() {
        var today = new Date().toISOString().split('T')[0];
        $('.transaction_date').val(today);
    }

    // Function to initialize Select2
    

    // Function to add a new row
    function addRow() {
        // Clone the first row
        var newRow = $('#journal-entry-table tbody tr:first').clone();

        // Clear the inputs in the cloned row
        newRow.find('input').val('');
        newRow.find('input.balance').val('N/A');
        newRow.find('select').val('').trigger('change');
        newRow.find('input[name="credit[]"]').val(0);
        newRow.find('input[name="debit[]"]').val(0);

        // Set today's date for the new row
        newRow.find('.transaction_date').val(new Date().toISOString().split('T')[0]);

        // Append the new row
        $('#journal-entry-table tbody').append(newRow);
        toggleFields();

    }


    // Function to remove a row, but leave at least one row in the table
    function removeRow(button) {
        var rowCount = $('#journal-entry-table tbody tr').length;

        // Only allow removal if more than one row exists
        if (rowCount > 1) {
            $(button).closest('tr').remove();
        }
    }

    // Event listener for adding a new row
    $(document).on('click', '.add-row', function(e) {
        e.preventDefault();
        addRow();
    });

    // Event listener for removing a row
    $(document).on('click', '.remove-row', function(e) {
        e.preventDefault();
        removeRow(this);
    });
});

</script>

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
