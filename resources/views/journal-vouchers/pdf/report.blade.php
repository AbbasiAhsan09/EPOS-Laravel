@extends('reports.layout')
@section('report_content')
<table class="table table-responsive-sm table-striped table-bordered" border="3">
    <thead>
        <th>Doc #</th>
        <th>Date</th>
        <th>Account</th>
        <th>Total Credit</th>
        <th>Total Debit</th>
        <th>Created On.</th>
        <th>User</th>
   
    </thead>
    <tbody>
        @foreach ($vouchers as $voucher)
            <tr>
                <td>{{$voucher->doc_no ?? ""}}</td>
                <td>{{date('d/m/Y',strtotime($voucher->date))}}</td>
                <td>{{$voucher->account->title ?? ""}}</td>
                <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($voucher->total_credit,2)}}</td>
                <td>{{ConfigHelper::getStoreConfig()["symbol"].number_format($voucher->total_debit,2)}}</td>
                <td>{{date("d/m/Y",strtotime($voucher->created_at))}}</td>
                <td>{{$voucher->user->name ?? "Deleted"}}</td>
                
            </tr>

        @endforeach
    </tbody>
</table>


@endsection