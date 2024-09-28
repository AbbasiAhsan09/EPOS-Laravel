

@extends('layouts.app')
@section('content')
@foreach ($ledger as $item)
    <table class="table table-bordered table-responsive-sm" style="border: solid 2px">
        <tbody>
            <tr>
                <th style="width: 300px">Title</th>
                <th>{{$item->account->title ?? ""}}</th>
            </tr>
            <tr>
                <th style="width: 300px">Total Debit</th>
                <th>{{$item->total_debit ?? ""}}</th>
            </tr>
            <tr>
                <th style="width: 300px">Total Credit</th>
                <th>{{$item->total_credit ?? ""}}</th>
            </tr>
            <tr>
                <th style="width: 300px">Balance</th>
                <th>{{($item->total_debit ?? 0) - ($item->total_credit ?? 0) }}</th>
            </tr>
        </tbody>
    </table>
@endforeach


{{-- <table class="table table-bordered table-responsive-sm" style="border: solid 2px">
    <tbody>
        <tr>
            <th style="width: 300px">Title</th>
            <th>Over All Summary</th>
        </tr>
        <tr>
            <th style="width: 300px">Total Debit</th>
            <th>{{$ledger->sum('total_debit') ?? 0}}</th>
        </tr>
        <tr>
            <th style="width: 300px">Total Credit</th>
            <th> {{$ledger->sum('total_credit') ?? 0}}</th>
        </tr>
        <tr>
            <th style="width: 300px">Balance</th>
            <th>({{($ledger->sum('total_debit') ?? 0 ) - ($ledger->sum('total_credit') ?? 0)}})  {{($ledger->sum('total_debit') ?? 0 ) - ($ledger->sum('total_credit') ?? 0) < 0 ? 
                'Payable' : ((($ledger->sum('total_debit') ?? 0 ) - ($ledger->sum('total_credit') ?? 0)) > 0 ? 'Receivable'  :   'You are all set')}}</th>
        </tr>
    </tbody>
</table> --}}

@endsection
