
@extends('reports.layout')
@section("report_content")

    @foreach ($ledgerData as $account)
    <div class="custom-box">
    <h5 class="title" >{{ $account['account'] }} LEDGER  </h5>
    <table class="table table-bordered table-striped"  border="3">
        <thead style="background: rgb(197, 197, 197); color : black">
            <tr>
                <th width="60px">Date</th>
                <th>Description</th>
                <th width="60px">Debit</th>
                <th width="60px">Credit</th>
                <th width="100px">Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background: rgb(197, 250, 221)">
                <td colspan="4" style="text-align: right"><strong>Opening Balance</strong></td>
                <td><strong>{{ ConfigHelper::getStoreConfig()["symbol"].number_format(abs($account['starting_balance']), 2) }} {{$account['starting_balance'] < 0 ? 'CR': "DR"}}</strong></td>
            </tr>
            @foreach ($account['transactions'] as $transaction)
                <tr>
                    <td>{{ $transaction['transaction_date'] }}</td>
                    <td style="{{strpos($transaction["description"], 'reverse') !== false || strpos($transaction["description"], 'reversed') ? 'background : red; color : white' :''}}">
                        @if (strpos($transaction["description"], 'reverse') !== false || strpos($transaction["description"], 'reversed'))
                            <p>
                                <strong>Reversed Entry</strong>
                            </p>
                        @endif
                        @include("reports.accounts.component.transaction_description",['data' => $transaction['data']])
                    </td>
                    <td>{{number_format($transaction['debit'], 2) }}</td>
                    <td>{{number_format($transaction['credit'], 2) }}</td>
                    <td>{{number_format(abs($transaction['running_balance']), 2) }} {{$transaction['running_balance'] < 0 ? 'CR': "DR"}}</td>
                </tr>
            @endforeach
            <tr style="background: rgb(250, 197, 208)">
                <td colspan="4" style="text-align: right"><strong>Closing Balance</strong></td>
                <td><strong>{{ ConfigHelper::getStoreConfig()["symbol"].number_format(abs($account['ending_balance']), 2) }} {{$account['ending_balance'] < 0 ? 'CR': "DR"}}</strong></td>
            </tr>
        </tbody>
    </table>
    </div>
@endforeach
</div>

<script>
    $(document).ready(function() {
    $('.js-example-basic-multiple').select2();
});
</script>

<style>
        table{
        width: 100% ;
    }
    
    table, th, td {
  border: 1px solid gray;
  border-collapse: collapse;
}
.custom-box > h2, .custom-box > h3, .custom-box > h4, .custom-box > h5{
    text-align: center;
    text-transform: uppercase;
    background: rgb(55, 55, 55);
    color: white;
    margin:0  
}
td, p, th {
    font-size: 12px !important;
    
}
p{
    line-height: 0.2
}
</style>

@endsection