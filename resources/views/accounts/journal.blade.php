@extends('layouts.app')
@section('content')
<form action="{{route("journal.post")}}" method="POST">
    @csrf

<div class="page-wrapper">
    <div class="container-fluid">
      <div class="row row-customized align-items-between">
        <div class="col">
            <h1 class="page-title">General Journal</h1>
        </div>
        <div class="col-lg-4">
            <a class="btn btn-outline-secondary btn-sm mb-0 w-100" href="/account/transactions">Recent Transactions</a>
        </div>
        <div class="col-lg-1">
            
            <button class="btn btn-primary btn-sm mb-0 w-100">Save</button>
            
        </div>
       
      </div>
    </div>
</div>


<table class="table table-responsive table-bordered" id="journal-entry-table">
    <thead>
        <th style="width: 200px;">Date</th>
        <th style="width: 300px;">Account</th>
        <th >Description</th>
        <th style="width: 200px;">Credit</th>
        <th style="width: 200px;">Debit</th>
        <th>From Account</th>
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
                    <select name="account_id[]" id="" class=" account_selection  form-control " required>
                        <option value="">Select Account</option>
                        @foreach ($accounts as $key => $accountList)
                            <optgroup   label="{{ucfirst($key)}}">
                        @foreach ($accountList as $account)
                        <option value="{{$account->id}}">{{$account->title}} {{$account->reference_type ? '('.ucfirst($account->reference_type).')' : '' }}</option>
                        @endforeach
                    </optgroup>
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
                    <input type="number" min="0" class="form-control credit" value="0"  required name="credit[]">
                </div>
            </td>
            
            <td>
                <div class="input-group input-group-outline">
                    <input type="number" min="0" class="form-control debit" value="0"  required name="debit[]">
                </div>
            </td>
            <td>
                <div class="input-group input-group-outline">  
                    <select name="source_account[]" id="" class=" account_selection  form-control " >
                        <option value="">Select Account</option>
                        @foreach ($accounts as $key => $accountList)
                            <optgroup   label="{{ucfirst($key)}}">
                        @foreach ($accountList as $account)
                        <option value="{{$account->id}}">{{$account->title}} {{$account->reference_type ? '('.ucfirst($account->reference_type).')' : '' }}</option>
                        @endforeach
                    </optgroup>
                        @endforeach
                    </select>
                </div> 
            </td>
          
            <td>
                <button class="btn btn-primary add-row" type="button">
                    <i class="fa fa-add"></i> 
                </button>
                <button class="btn btn-danger remove-row" type="button">
                    <i class="fa fa-trash"></i> 
                </button>
            </td>
        </tr>
    </tbody>
</table>
</form>

<script>
$(document).ready(function () {
    // Set today's date as the default value for all date inputs
    var today = new Date().toISOString().split('T')[0];
    $('input[type="date"]').val(today);

    // Function to add a new row
    $(document).on('click', '.add-row', function () {
        // Clone the last row in the table
        var newRow = $('#journal-entry-table tbody tr.journal-entry:last').clone();

        // Reset input values for the cloned row
        newRow.find('input').val('');
        newRow.find('input[type="number"]').val('0');
        newRow.find('select').prop('selectedIndex', 0);

        // Set default date to today for the new row
        newRow.find('input[type="date"]').val(today);

        // Update the names of the cloned inputs to include a unique index
        $('#journal-entry-table tbody tr.journal-entry').each(function(index) {
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    $(this).attr('name', name.replace(/\[\d+\]/, '[' + index + ']'));
                }
            });
        });

        // Append the new row after the last row in the table
        $('#journal-entry-table tbody').append(newRow);
    });


    // Function to remove a row
    $(document).on('click', '.remove-row', function () {
        if ($('.journal-entry').length > 1) {
            $(this).closest('tr').remove();
        } else {
            alert("You can't remove the last row!");
        }
    });

    // Function to handle credit and debit field behavior
    $(document).on('input', '.credit', function () {
        var $row = $(this).closest('tr');
        var creditValue = parseFloat($(this).val());

        if (creditValue > 0) {
            $row.find('.debit').val(0).prop('readonly', true); // Disable debit if credit > 0
        } else {
            $row.find('.debit').prop('readonly', false); // Enable debit if credit = 0
        }
    });

    $(document).on('input', '.debit', function () {
        var $row = $(this).closest('tr');
        var debitValue = parseFloat($(this).val());

        if (debitValue > 0) {
            $row.find('.credit').val(0).prop('readonly', true); // Disable credit if debit > 0
        } else {
            $row.find('.credit').prop('readonly', false); // Enable credit if debit = 0
        }
    });

    // Function to ensure account_id and source_account are not the same
    $(document).on('change', '.account_selection', function () {
        var $row = $(this).closest('tr');
        var accountId = $row.find('select[name="account_id[]"]').val();
        var sourceAccountId = $row.find('select[name="source_account[]"]').val();

        // If both account_id and source_account are the same, show an alert
        if (accountId && sourceAccountId && accountId === sourceAccountId) {
            alert("Account and Source Account cannot be the same. Please choose different accounts.");
            $(this).val(''); // Reset the current selection
        }
    });


       // Function to confirm submission if account selection is empty
       $('form').on('submit', function (e) {
        var emptyAccounts = false;

        // Check if any .account_selection fields are empty
        $('.account_selection').each(function () {
            if ($(this).val() === "") {
                emptyAccounts = true;
                return false; // Break the loop if any are empty
            }
        });

        // If any account_selection fields are empty, confirm with the user
        if (emptyAccounts) {
            if (!confirm("Some entries are not double entries are you sure you want to procceed ?")) {
                e.preventDefault(); // Prevent form submission if the user cancels
            }
        }
    });


});


</script>
@endsection
