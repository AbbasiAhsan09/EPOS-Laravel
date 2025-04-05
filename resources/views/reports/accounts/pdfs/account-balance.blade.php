@extends('reports.layout')
@section("report_content")

<div class="custom-box">
    <table class="table table-responsive-sm table-sm table-striped table-bordered ">
        <thead class="table-dark">
            <tr>
                <th style="width: 100px">S.No</th>
                <th>Account Title</th>
                <th>Account Type</th>
                <th>Current Balance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($accounts as $key => $account)
                <tr>
                    <td style="width: 100px">{{ $key + 1 }}</td>
                    <td>{{ $account->title }}</td>
                    <td>{{ ucfirst($account->type) }}</td>
                    <td>
                        <strong>
                            {{ ConfigHelper::getStoreConfig()['symbol'] . ($account->remaining_balance > 0 ? number_format($account->remaining_balance, 2) : '(' . number_format(abs($account->remaining_balance), 2) . ')') }}
                        </strong>
                    </td>
                </tr>
            @endforeach
        <tfoot class="table-dark">
            <th colspan="3">Total</th>
            <th colspan="1">
                {{ ConfigHelper::getStoreConfig()['symbol'] . ($total_balance > 0 ? number_format($total_balance, 2) : '(' . number_format(abs($total_balance), 2) . ')') }}
            </th>
        </tfoot>
        </tbody>
    </table>
</div>

@endsection