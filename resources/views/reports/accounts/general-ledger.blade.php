@extends('layouts.app')
@section('content')
@foreach ($ledgerData as $account)
    <h3>{{ $account['account'] }} ({{ $account['account_type'] }})</h3>
    <p>{{ $account['description'] }}</p>
    <p>Starting Balance: {{ number_format($account['starting_balance'], 2) }}</p>

    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($account['transactions'] as $transaction)
                <tr>
                    <td>{{ $transaction['date'] }}</td>
                    <td>{{ $transaction['description'] }}</td>
                    <td>{{ number_format($transaction['debit'], 2) }}</td>
                    <td>{{ number_format($transaction['credit'], 2) }}</td>
                    <td>{{ number_format($transaction['running_balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p>Ending Balance: {{ number_format($account['ending_balance'], 2) }}</p>
@endforeach

@endsection