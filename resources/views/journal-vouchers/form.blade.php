@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        @php
            $isEditMode = isset($voucher->id) ? true : false;
            $voucher = isset($voucher->id) ? $voucher : null;
        @endphp

        @if (!$isEditMode)
            <form action="{{ route('journal-voucher.store') }}" method="POST">
                @method('post')
            @else
                <form action="{{ route('journal-voucher.update',$voucher->id) }}" method="POST">
                @method('put')
        @endif
        @csrf
        <div class="card p-4">
            <div class="row d-flex align-items-center mb-3">
                <div class="col-lg-3">
                    <h4>{{ $isEditMode ? 'Edit' : 'Create' }} Journal Voucher </h4>
                    <strong>{{$isEditMode ? $voucher->doc_no : (isset($last_id) ? 'JV/'.$last_id+1 : '')}}</strong>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Account</label>
                    <div class="input-group input-group-outline">
                        <select name="jv_account_id" id="" class="form-control" required>
                            <option value="">Select Account</option>
                            @foreach ($accounts as $key => $accountList)
                                <optgroup label="{{ ucfirst($key) }}">
                                    @foreach ($accountList as $account)
                                        <option value="{{ $account->id }}" {{$isEditMode && $voucher->account_id === $account->id ? 'selected' : ''}}>
                                            {{ $account->title }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Voucher Date</label>
                    <div class="input-group input-group-outline">
                        <input type="date" class="form-control" name="date" required
                        value="{{$isEditMode ? $voucher->date : date('Y-m-d', time())}}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Reference No.</label>
                    <div class="input-group input-group-outline">
                        <input type="text" class="form-control" name="reference_no" 
                            value="{{$isEditMode ? $voucher->reference_no : ''}}">
                    </div>
                </div>
                {{-- <div class="col-lg-1">
                    <label class="form-label">Mode</label>
                    <div class="input-group input-group-outline">
                        <select name="mode" class="form-control" id="">
                            @foreach (['cash', 'cheque', 'credit_card', 'offset', 'online', 'pay_order'] as $mode)
                                <option value="{{ $mode }}"
                                    {{ $isEditMode && $mode == $voucher->mode ? 'selected' : '' }}>{{ ucfirst($mode) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div> --}}
                
            </div>
         

            <table class="table table-responsive-sm table-stripped table-bordered" border="2">
                <thead>
                    <th style="width: 100px">Reference No.</th>
                    <th>Account</th>
                    <th>Description</th>
                    <th>Credit</th>
                    <th>Debit</th>
                    <th>Mode</th>
                    <th>Action</th>
                </thead>
                <tbody id="voucher_entry_tbody">
                    @if ($isEditMode)
                        @foreach ($voucher->entries as $entry)
                        <tr>
                            <td style="width: 200px">
                                <div class="input-group input-group-outline">
                                    <input type="text" name="reference[]" value="{{$entry->reference_no}}" class="form-control">
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-outline">
                                    <select name="account_id[]" id="" class="form-control" required>
                                        <option value="">Select Account</option>
                                        @foreach ($accounts as $key => $detailAccountList)
                                            <optgroup label="{{ ucfirst($key) }}">
                                                @foreach ($detailAccountList as $detail_account)
                                                    <option value="{{ $detail_account->id }}" {{$isEditMode && $entry->account_id === $detail_account->id ? 'selected' : ''}}>
                                                        {{ $detail_account->title }}
                                                    </option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
            
                                    </select>
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-outline">
                                    <input type="text" name="description[]" value="{{$entry->description}}" class="form-control">
                                </div>
                            </td>
                            <td style="width: 200px">
                                <div class="input-group input-group-outline">
                                    <input type="number" name="credit[]" value="{{$entry->credit}}" min="0" step="0.01" 
                                        class="form-control credit_amount">
                                </div>
                            </td>
                            <td style="width: 200px">
                                <div class="input-group input-group-outline">
                                    <input type="number" name="debit[]" value="{{$entry->debit}}" min="0" step="0.01" 
                                        class="form-control debit_amount">
                                </div>
                            </td>
                            <td>
                                <div class="input-group input-group-outline">
                                    <select name="mode[]" id="" class="form-control">
                                        @foreach (['cash', 'cheque', 'credit_card', 'offset', 'online', 'pay_order'] as $mode)
                                            <option value="{{ $mode }}"
                                            {{ $isEditMode && ($mode == $entry->mode)? 'selected' : '' }}>{{ ucfirst($mode) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                            <td style="width: 100px">
                                <button type="button" class="btn btn-small btn-primary" onclick="appendRow()">
                                    <i class="fa fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-small btn-danger" onclick="removeRow(this)" disabled>
                                    <i class="fa fa-minus"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td style="width: 200px">
                            <div class="input-group input-group-outline">
                                <input type="text" name="reference[]"  class="form-control">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-outline">
                                <select name="account_id[]" id="" class="form-control" required>
                                    <option value="">Select Account</option>
                                    @foreach ($accounts as $key => $detailAccountList)
                                        <optgroup label="{{ ucfirst($key) }}">
                                            @foreach ($detailAccountList as $detail_account)
                                                <option value="{{ $detail_account->id }}">
                                                    {{ $detail_account->title }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
        
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-outline">
                                <input type="text" name="description[]"  class="form-control">
                            </div>
                        </td>
                        <td style="width: 200px">
                            <div class="input-group input-group-outline">
                                <input type="number" name="credit[]"   min="0" step="0.01" 
                                    class="form-control credit_amount">
                            </div>
                        </td>
                        <td style="width: 200px">
                            <div class="input-group input-group-outline">
                                <input type="number" name="debit[]"  min="0" step="0.01" 
                                    class="form-control debit_amount">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-outline">
                                <select name="mode[]" id="" class="form-control">
                                    @foreach (['cash', 'cheque', 'credit_card', 'offset', 'online', 'pay_order'] as $mode)
                                        <option value="{{ $mode }}"
                                            {{ $isEditMode && ($mode == $entry->mode)? 'selected' : '' }}>{{ ucfirst($mode) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                        <td style="width: 100px">
                            <button type="button" class="btn btn-small btn-primary" onclick="appendRow()">
                                <i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-small btn-danger" onclick="removeRow(this)" disabled>
                                <i class="fa fa-minus"></i>
                            </button>
                        </td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3">Total</th>
                        <th><span id="totalCredit">0.00</span></th>
                        <th><span id="totalDebit">0.00</span></th>
                    </tr>
                </tfoot>
            </table>
            <div class="mb-5">
                <label for="">Note</label>
                <div class="input-group input-group-outline">
                    <textarea name="note" cols="30" rows="5" class="form-control">{{$isEditMode ? $voucher->note : ''}}</textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Save</button>
        </div>
        </form>
    </div>


    <script>
        $(document).ready(function() {
            calculateTotal();
            updateRemoveButtons()


          // Use event delegation for credit amount input
            $(document).on('input', 'input.credit_amount', function () {
                // Get the current row
                const row = $(this).closest('tr');

                // Get debit and credit input fields within this row
                const debit = row.find('input.debit_amount');
                const credit = row.find('input.credit_amount');

                // If the debit field has a value, clear the credit field
                if (debit.val().length > 0) {
                    debit.val('');
                }
            });

            // Use event delegation for debit amount input
            $(document).on('input', 'input.debit_amount', function () {
                // Get the current row
                const row = $(this).closest('tr');

                // Get debit and credit input fields within this row
                const credit = row.find('input.credit_amount');

                // If the credit field has a value, clear the debit field
                if (credit.val().length > 0) {
                    credit.val('');
                }
            });

        })

        const tbody = document.getElementById("voucher_entry_tbody");

        function appendRow() {
            const row = document.createElement("tr");

            row.innerHTML = `
                <tr>
                        <td style="width: 200px">
                            <div class="input-group input-group-outline">
                                <input type="text" name="reference[]" class="form-control">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-outline">
                                <select name="account_id[]" id="" class="form-control" required>
                                    <option value="">Select Account</option>
                                    @foreach ($accounts as $key => $detailAccountList)
                                        <optgroup label="{{ ucfirst($key) }}">
                                            @foreach ($detailAccountList as $detail_account)
                                                <option value="{{ $detail_account->id }}">
                                                    {{ $detail_account->title }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
        
                                </select>
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-outline">
                                <input type="text" name="description[]"  class="form-control">
                            </div>
                        </td>
                        <td style="width: 200px">
                            <div class="input-group input-group-outline">
                                <input type="number" name="credit[]"   min="0" step="0.01" 
                                    class="form-control credit_amount">
                            </div>
                        </td>
                        <td style="width: 200px">
                            <div class="input-group input-group-outline">
                                <input type="number" name="debit[]"  min="0" step="0.01" 
                                    class="form-control debit_amount">
                            </div>
                        </td>
                         <td>
                                <div class="input-group input-group-outline">
                                    <select name="mode[]" id="" class="form-control">
                                        @foreach (['cash', 'cheque', 'credit_card', 'offset', 'online', 'pay_order'] as $mode)
                                            <option value="{{ $mode }}"
                                                {{ $isEditMode && $mode == $voucher->mode ? 'selected' : '' }}>{{ ucfirst($mode) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </td>
                        <td style="width: 100px">
                            <button type="button" class="btn btn-small btn-primary" onclick="appendRow()">
                                <i class="fa fa-plus"></i>
                            </button>
                            <button type="button" class="btn btn-small btn-danger" onclick="removeRow(this)" disabled>
                                <i class="fa fa-minus"></i>
                            </button>
                        </td>
                    </tr>
          `;

            tbody.appendChild(row);
            updateRemoveButtons();
        }

        function removeRow(button) {
            if (tbody.rows.length > 1) {
                button.closest("tr").remove();
                updateRemoveButtons();
                setTimeout(() => {
                    calculateTotal()
                }, 0);
            }
        }

        function updateRemoveButtons() {
            const rows = tbody.querySelectorAll("tr");
            rows.forEach((row, index) => {
                const removeButton = row.querySelector(".btn-danger");
                removeButton.disabled = rows.length === 1; // Disable if only one row remains
            });
        }


        function formatNumber(amount, locale = 'en-US') {
            return new Intl.NumberFormat(locale, {
                style: 'decimal',
                minimumFractionDigits: 2,
                maximumFractionDigits: 2,
            }).format(amount);
        }


        // Attach calculateTotal to events on .voucher_amount elements
        $(document).on("keyup keydown click change", ".debit_amount",".credit_amount", calculateTotal);

        function calculateTotalCredit(){
            let totalCredit = 0;

            $(".credit_amount").each(function() {
                // Convert the value to a number and add it to totalAmount
                const amount = parseFloat($(this).val()) || 0; // Use 0 if value is not a number
                totalCredit += amount;
            });
            const totalFormatted = `{{ ConfigHelper::getStoreConfig()['symbol'] }}${formatNumber(totalCredit)}`;
            console.log('totalFormatted', totalFormatted)
            // alert(totalFormatted);
            $("#totalCredit").text(totalFormatted);
        }


        function calculateTotalDebit(){
            let totalDebit = 0;

            $(".debit_amount").each(function() {
                // Convert the value to a number and add it to totalAmount
                const amount = parseFloat($(this).val()) || 0; // Use 0 if value is not a number
                totalDebit += amount;
            });
            const totalFormatted = `{{ ConfigHelper::getStoreConfig()['symbol'] }}${formatNumber(totalDebit)}`;
            console.log('totalFormatted', totalFormatted)
            $("#totalDebit").text(totalFormatted);
        }

        function calculateTotal() {

            calculateTotalDebit();
            calculateTotalCredit();
            // let totalAmount = 0;

            // Loop through each element with the class .voucher_amount
            // $(".voucher_amount").each(function() {
            //     // Convert the value to a number and add it to totalAmount
            //     const amount = parseFloat($(this).val()) || 0; // Use 0 if value is not a number
            //     totalAmount += amount;
            // });
            // const totalFormatted = `{{ ConfigHelper::getStoreConfig()['symbol'] }}${formatNumber(totalAmount)}`;
            // $("#grandTotal").text(totalFormatted);

            // Optionally, display the total amount in an element
            // $("#totalAmountDisplay").text(totalAmount.toFixed(2));
        }
    </script>
@endsection
