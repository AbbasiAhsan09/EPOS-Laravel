


<div class="p-5 pt-0">
    <div class="row align-items-center">
        <div class="col-lg-2">
            <h4>Ledger Report </h4>
        </div>
        <div class="col-lg-10">
            From : {{session("general_ledger_from")}}
            To : {{session("general_ledger_to")}}
        </div>
    </div>
    {{-- <div class="p-5"> --}}
        <table>
            <thead style="background: rgb(34, 34, 34); color : rgb(250, 250, 250)">
                <th width="50px">Date</th>
                <th >Description</th>
                <th width="100px">Credit</th>
                <th width="100px">Debit</th>
                <th width="100px">Balance</th> <!-- Add balance header -->
            </thead>
        </table>
        @foreach ($data as $item)
        <div class="custom-box">
        <h5>{{$item->parent->title ? '('.$item->parent->title ?? "".') - ' : '' }}  
            {{ $item->title ?? "" }} ({{ ucfirst($item->type) ?? "" }}) 
            {{$item->reference_type ? '- ('.ucfirst($item->reference_type).')' : ''}}
        </h5>
        <table class="table table-bordered table-responsive-sm" style="border: solid 2px">
           
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
                    <td width="80px">{{ date("m-d-Y",strtotime($transaction->transaction_date)) }}</td>
                    <td >{{ $transaction->note }}</td>
                    <td width="100px">{{ number_format($transaction->credit, 2) }}</td>
                    <td width="100px">{{ number_format($transaction->debit, 2) }}</td>
                    <td width="100px">{{ number_format($balance, 2) }}</td> <!-- Display running balance -->
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
        </div>
    @endforeach
    {{-- </div> --}}
</div>

<style>
        table{
        width: 100% ;
    }
    
    table, th, td {
  border: 1px solid gray;
  border-collapse: collapse;
  font-size: 12px
}

    .dates{
        float: right
    }

    .custom-box > h2, .custom-box > h3, .custom-box > h4, .custom-box > h5{
    text-align: center;
    text-transform: capitalize;
    background: rgb(197, 197, 197);
    color: black;
    margin:0;
    margin-top: 5px  
}
</style>