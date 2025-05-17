@extends("reports.layout")
@section("report_content")
<table class="table table-responsive-sm table-striped table-bordered" border="3">
    <thead>
        <th>Doc #</th>
        <th>Date</th>
        <th>Bank</th>
        <th>Account</th>
        <th>Total</th>
    </thead>
    <tbody>
        @foreach ($vouchers as $voucher)
            <tr>
                <td>{{$voucher->doc_no ?? ""}}</td>
                <td>{{date('d/m/Y',strtotime($voucher->date))}}</td>
                <td>{{$voucher->account->title ?? ""}} - {{$voucher->account->type ?? ""}}</td>
                <td>{{$voucher->account_from->title ?? ""}} - {{$voucher->account_from->type ?? ""}}</td>
                <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($voucher->total,2)}}</td>
            
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="4">Grand Total</th>
            <th>{{ConfigHelper::getStoreConfig()["symbol"].number_format($vouchers->sum("total"),2)}}</th>
        </tr>
    </tfoot>
</table>

<style>
    table{
        width: 100% ;
    }
    table, th, td {
  border: 1px solid gray;
  border-collapse: collapse;
}
    .dates{
        float: right
    }
</style>

@endsection