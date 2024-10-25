@extends('layouts.app')
@section('content')
    <div class="container-fluid">

        @php
            $isEditMode = isset($voucher->id) ? true : false;
            $voucher = isset($voucher->id) ? $voucher : null;
        @endphp

        @if (!$isEditMode)
            <form action="{{ route('voucher.store') }}" method="POST">
                @method('post')
            @else
                <form action="{{ route('voucher.store') }}" method="POST">
                @method('put')
        @endif
        @csrf
        <div class="card p-4">
            <div class="row d-flex align-items-center mb-3">
                <div class="col-lg-3">
                    <h4>{{ $isEditMode ? 'Edit' : 'Create' }} {{ $voucher_type->name }} </h4>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">{{ ucfirst($voucher_type->type) }} From Account</label>

                    <div class="input-group input-group-outline">
                        <select name="account_from_id" id="" class="form-control" required>
                            <option value="">Select Account</option>
                            @foreach ($from_accounts as $key => $from_accountList)
                                <optgroup label="{{ ucfirst($key) }}">
                                    @foreach ($from_accountList as $from_account)
                                        <option value="{{ $from_account->id }}">{{ $from_account->title }}</option>
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
                            value="{{ date('Y-m-d', time()) }}">
                    </div>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">{{ ucfirst($voucher_type->type) }} In Account</label>
                    <div class="input-group input-group-outline">
                        <select name="account_id" id="" class="form-control" required>
                            <option value="">Select Account</option>
                            @foreach ($accounts as $key => $accountList)
                                <optgroup label="{{ ucfirst($key) }}">
                                    @foreach ($accountList as $account)
                                        <option value="{{ $account->id }}"
                                            @if (!$isEditMode) {{ !$isEditMode && $voucher_type->account_id === $account->id ? 'selected' : '' }}
                        @else
                        {{ $voucher->account_id === $account->id ? 'selected' : '' }} @endif>
                                            {{ $account->title }}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach

                        </select>
                    </div>
                </div>
                <div class="col-lg-2">
                    <label class="form-label">Reference No.</label>
                    <div class="input-group input-group-outline">
                        <input type="text" class="form-control" name="reference_no" 
                            value="">
                    </div>
                </div>
                <div class="col-lg-1">
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
                </div>
                
            </div>
            <input type="hidden" name="voucher_type_id" value="{{$voucher_type->id}}">

            <table class="table table-responsive-sm table-stripped table-bordered" border="2">
                <thead>
                    <th style="width: 100px">Reference No.</th>
                    <th>Description</th>
                    <th>Amount</th>
                    <th>Action</th>
                </thead>
                <tbody id="voucher_entry_tbody">
                    <tr>
                        <td style="width: 200px">
                            <div class="input-group input-group-outline">
                                <input type="text" name="reference[]" class="form-control">
                            </div>
                        </td>
                        <td>
                            <div class="input-group input-group-outline">
                                <input type="text" name="description[]" class="form-control">
                            </div>
                        </td>
                        <td style="width: 200px">
                            <div class="input-group input-group-outline">
                                <input type="number" name="amount[]" min="0.1" step="0.01" required
                                    class="form-control voucher_amount">
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
                </tbody>
            </table>
            <div class="mb-5">
                <label for="">Note</label>
                <div class="input-group input-group-outline">
                    <textarea name="note" cols="30" rows="5" class="form-control"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Save</button>
        </div>
        </form>
    </div>


    <script>
        $(document).ready(function() {
            calculateTotal();
        })

        const tbody = document.getElementById("voucher_entry_tbody");

        function appendRow() {
            const row = document.createElement("tr");

            row.innerHTML = `
              <td style="width: 200px">
                  <div class="input-group input-group-outline">
                      <input type="text" name="reference[]" class="form-control">
                  </div>
              </td>
              <td>
                  <div class="input-group input-group-outline">
                      <input type="text" name="description[]" class="form-control">
                  </div>
              </td>
              <td style="width: 200px">
                  <div class="input-group input-group-outline">
                      <input type="number" name="amount[]" min="0.1" required step="0.01" class="form-control voucher_amount">
                  </div>
              </td>
              <td style="width: 100px">
                  <button type="button" class="btn btn-small btn-primary" onclick="appendRow()">
                      <i class="fa fa-plus"></i>
                  </button>
                  <button type="button" class="btn btn-small btn-danger" onclick="removeRow(this)">
                      <i class="fa fa-minus"></i>
                  </button>
              </td>
          `;

            tbody.appendChild(row);
            updateRemoveButtons();
        }

        function removeRow(button) {
            if (tbody.rows.length > 1) {
                button.closest("tr").remove();
                updateRemoveButtons();
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
        $(document).on("keyup keydown click change", ".voucher_amount", calculateTotal);

        function calculateTotal() {
            let totalAmount = 0;

            // Loop through each element with the class .voucher_amount
            $(".voucher_amount").each(function() {
                // Convert the value to a number and add it to totalAmount
                const amount = parseFloat($(this).val()) || 0; // Use 0 if value is not a number
                totalAmount += amount;
            });
            const totalFormatted = `{{ ConfigHelper::getStoreConfig()['symbol'] }}${formatNumber(totalAmount)}`;
            console.log('totalFormatted', totalFormatted)
            console.log("Total Amount:", totalAmount);

            // Optionally, display the total amount in an element
            // $("#totalAmountDisplay").text(totalAmount.toFixed(2));
        }
    </script>
@endsection
