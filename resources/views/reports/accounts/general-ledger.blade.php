@extends('layouts.app')
@section('content')
<div class="container-fluid">
  <form action="">
    <div class="row d-flex align-items-end mb-3">
        <div class="col-lg-2">
            <h3>
                Ledger Report
            </h3>
        </div>
        <div class="col-lg-2">
            <label for="">From</label>
            <div class="input-group input-group-outline">
                <input type="date" name="from" value="{{request()->from ?? \Carbon\Carbon::now()->subDays(11)->toDateString()}}" class="form-control">
            </div>
        </div>
        <div class="col-lg-2">
            <label for="">To</label>
            <div class="input-group input-group-outline">
                <input type="date" name="to" value="{{request()->to ?? \Carbon\Carbon::now()->toDateString()}}" class="form-control">
            </div>
        </div>

        <div class="col-lg-4">
            <label for="From" class="form-label">Accounts:</label>
            <div class="input-group input-group-outline">
                <select class="js-example-basic-multiple" name="accounts[]" multiple="multiple" style="width: 100%; max-height: 30px;">
                    @foreach ($accounts as $key => $accountList)
                        <optgroup label="{{ucfirst($key)}}">
                            @foreach ($accountList as $account)
                                <option value="{{$account->id}}" {{request()->accounts && in_array($account->id, request()->accounts) ?  'selected': ''}}>
                                    {{$account->title}}  {{ $account->reference_type ? ' - '.$account->reference_type : '' }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                  </select>
            </div>
        </div>
        <div class="col-lg-1">
            <label for="">Type</label>
            <div class="input-group input-group-outline">
               <select name="type" id="" class="form-control">
                <option value="">Web</option>
                <option value="pdf">PDF</option>
               </select>
            </div>
        </div>
        <div class="col-lg-1">
            <button type="submit" class="btn btn-primary m-0">Filter</button>
        </div>
    </div>
  </form>
    @foreach ($ledgerData as $account)
    <div class="custom-box">
    <h5>{{ $account['account'] }} ({{ $account['account_type'] }})</h5>
    <p>{{ $account['description'] }}</p>
    <table class="table table-bordered table-striped"  border="3">
        <thead style="background: rgb(197, 197, 197); color : black">
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Debit</th>
                <th>Credit</th>
                <th>Balance</th>
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
                    <td>{{ ConfigHelper::getStoreConfig()["symbol"].number_format($transaction['debit'], 2) }}</td>
                    <td>{{ ConfigHelper::getStoreConfig()["symbol"].number_format($transaction['credit'], 2) }}</td>
                    <td>{{ ConfigHelper::getStoreConfig()["symbol"].number_format(abs($transaction['running_balance']), 2) }} {{$transaction['running_balance'] < 0 ? 'CR': "DR"}}</td>
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
</style>
@endsection