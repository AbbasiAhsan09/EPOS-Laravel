

@extends('layouts.app')
@section('content')
<div class="p-5 pt-0">
    <div class="row align-items-center">
        <div class="col-lg-2">
            <h4>Ledger Report </h4>
        </div>
        <div class="col-lg-10">
            <form action="/account/report/general-ledger">
                <div class="row align-items-end">
                    <div class="col-lg-3">
                        <label for="">Account</label>
                        <div class="input-group input-group-outline">
                        <select name="account_id" id="" class="form-control">
    
                            <option value="">All </option>
                            @foreach ($accounts as $account)
                            <option value="{{$account->id}}" {{session("general_account_id") && session("general_account_id") === $account->id ? 'selected' : '' }}>
                                {{$account->title}} - {{ucfirst($account->type)}} {{$account->reference_type ? '- ('.ucfirst($account->reference_type).')' : '' }}
                            </option>
                            @endforeach
                        </select>
                        </div>
                    </div>
    
                    <div class="col-lg-3">
                        <label for="">From</label>
                        <div class="input-group input-group-outline">
                            <input type="date" name="from" id="" class="form-control" value="{{session("general_ledger_from") ?? null}}">
                        </div>
                    </div>
    
                    <div class="col-lg-3">
                        <label for="">To</label>
                        <div class="input-group input-group-outline">
                            <input type="date" name="to" id="" class="form-control" value="{{session("general_ledger_to") ?? null}}">
                        </div>
                    </div>
                    <div class="col-lg-2">
                        <label for="">Type</label>
                        <div class="input-group input-group-outline">
                            <select name="type" id="" class="form-control">
                                <option value="web">Web</option>
                                <option value="pdf">PDF</option>
                            </select>
                        </div>
                    </div>
    
                    <div class="col-lg-1">
                        <button class="btn btn-primary" style="height: fit-content">Generate</button>
                    </div>
    
                </div>
            </form>
        </div>
    </div>
    {{-- <div class="p-5"> --}}
        @foreach ($data as $item)
        <h5> {{$item->parent->title ? '('.$item->parent->title.') - ' : '' }}  {{ $item->title ?? "" }} ({{ ucfirst($item->type) ?? "" }}) {{$item->reference_type ? ' ('.ucfirst($item->reference_type).')' : ''}}</h5>

        <table class="table table-bordered table-responsive-sm" style="border: solid 2px">
            <thead style="background: rgb(197, 197, 197); color : black">
                <th>Date</th>
                <th width="300px" style="width: 300px">Description</th>
                <th>Credit</th>
                <th>Debit</th>
                <th>Balance</th> <!-- Add balance header -->
            </thead>
            @php
                $balance = 0; // Initialize balance for each account
            @endphp
            @foreach ($item->transactions as $transaction)
                @php
                    if (in_array($item->type, ['expenses', 'assets'])) {
                        $balance +=  $transaction->debit - $transaction->credit;
                    }else{
                        $balance +=  $transaction->credit - $transaction->debit;
                    }
                    // Update the balance by adding credit and subtracting debit
                    
                @endphp
                <tr>
                    <td>{{ ($transaction->transaction_date) }}</td>
                    <td style="width: 300px">{{ $transaction->note }}</td>
                    <td>{{ number_format($transaction->credit, 2) }}</td>
                    <td>{{ number_format($transaction->debit, 2) }}</td>
                    <td>{{ number_format($balance, 2) }}</td> <!-- Display running balance -->
                </tr>
            @endforeach
            <tfoot  style="background: rgb(197, 197, 197); color : black">
                <th colspan="2">Total</th>
                <th>{{ number_format($item->transactions->sum('credit'), 2) }}</th> <!-- Sum of all credits -->
                <th>{{ number_format($item->transactions->sum('debit'), 2) }}</th> <!-- Sum of all debits -->
                <th>{{ number_format($balance, 2) }}</th>
                {{-- <th>Debit</th> --}}
            </tfoot>
        </table>
    @endforeach
    {{-- </div> --}}
</div>


@endsection
