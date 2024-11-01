
@include("reports.header")
    @foreach ($ledgerData as $account)
    <div class="custom-box">
    <h5 class="title">{{ $account['account'] }} ({{ $account['account_type'] }})</h5>
    <table class="table table-bordered table-striped"  border="3">
        <thead style="background: rgb(197, 197, 197); color : black">
            <tr>
                <th width="100px">Date</th>
                <th>Description</th>
                <th width="100px">Debit</th>
                <th width="100px">Credit</th>
                <th width="100px">Balance</th>
            </tr>
        </thead>
        <tbody>
            <tr style="background: rgb(197, 250, 221)">
                <td colspan="4" style="text-align: right"><strong>Opening Balance</strong></td>
                <td><strong>{{ ConfigHelper::getStoreConfig()["symbol"].number_format($account['starting_balance'], 2) }}</strong></td>
            </tr>
            @foreach ($account['transactions'] as $transaction)
                <tr>
                    <td>{{ $transaction['transaction_date'] }}</td>
                    <td>{{ $transaction['description'] }}</td>
                    <td>{{ ConfigHelper::getStoreConfig()["symbol"].number_format($transaction['debit'], 2) }}</td>
                    <td>{{ ConfigHelper::getStoreConfig()["symbol"].number_format($transaction['credit'], 2) }}</td>
                    <td>{{ ConfigHelper::getStoreConfig()["symbol"].number_format($transaction['running_balance'], 2) }}</td>
                </tr>
            @endforeach
            <tr style="background: rgb(250, 197, 208)">
                <td colspan="4" style="text-align: right"><strong>Closing Balance</strong></td>
                <td><strong>{{ ConfigHelper::getStoreConfig()["symbol"].number_format($account['ending_balance'], 2) }}</strong></td>
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
    text-transform: capitalize;
    background: rgb(55, 55, 55);
    color: white;
    margin:0  
}
</style>
