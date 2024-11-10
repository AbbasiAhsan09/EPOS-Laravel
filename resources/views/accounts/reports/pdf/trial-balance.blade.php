@extends('reports.layout')
@section('report_content')
@foreach ($data as $key => $item)
<div class="custom-box">
    {{-- <hr style="background: gray"> --}}
<h5 class="title">{{ucfirst($key)}}</h5>

{{-- Head Loop --}}
@foreach ($item as $head)
<table class="table table-bordered table-responsive-sm tria-balance-table" style="border: solid 2px">
    <thead>
        <th style="">Account Title</th>
        <th>Type</th>
        <th>Credit</th>
        <th>Debit</th>
        <th>Balance</th>
    </thead>
   
    <tbody>
        {{-- Children Loop --}}
        @foreach ($head["children"] as $child)
            <tr style="border: solid 2px">
                <td style="border: solid 2px">{{$child["title"]}}</td>
                <td style="border: solid 2px">{{ucfirst($child["type"])}}</td>
                <td style="border: solid 2px"> {{number_format($child["sum"]["total_credit"],2)}}</td>
                <td style="border: solid 2px"> {{number_format($child["sum"]["total_debit"],2)}}</td>
                <td>
                    @if (in_array($child["type"], ['expenses', 'assets','drawings']))
                        {{ConfigHelper::getStoreConfig()["symbol"]}} {{ ($child['sum']["total_debit"] - $child['sum']["total_credit"]) < 0 ? '('.abs($child['sum']["total_debit"] - $child['sum']["total_credit"]).')' : $child['sum']["total_debit"] - $child['sum']["total_credit"] }}
                    @else
                    {{ConfigHelper::getStoreConfig()["symbol"]}} {{ ($child['sum']["total_credit"] - $child['sum']["total_debit"]) < 0 ? '('.number_format(abs($child['sum']["total_credit"] - $child['sum']["total_debit"]),2).')' : number_format($child['sum']["total_credit"] - $child['sum']["total_debit"],2) }}
                    @endif
                </td>
                {{-- <th>{{($child["total_debit"] - $child["total_credit"]) < 0 ? '('.abs($child["total_debit"] - $child["total_credit"]).')' : $child["total_debit"] - $child["total_credit"]}}</th> --}}
            </tr>
        @endforeach
        {{-- Children Loop --}}
    </tbody>
    <tfoot style="background: rgb(100, 67, 67); color : white">
        <th>{{($head["title"])}} (Total)</th>
        <th>{{ucfirst($head["type"])}}</th>
        <th>{{ConfigHelper::getStoreConfig()["symbol"]}} {{($head["total_credit"])}}</th>
        <th>{{ConfigHelper::getStoreConfig()["symbol"]}} {{($head["total_debit"])}}</th>
        <th>
        @if (in_array($head["type"], ['expenses', 'assets','drawings']))
        {{ConfigHelper::getStoreConfig()["symbol"]}} {{ ($head["total_debit"] - $head["total_credit"]) < 0 ? '('.abs($head["total_debit"] - $head["total_credit"]).')' : $head["total_debit"] - $head["total_credit"] }}
        @else
        {{ConfigHelper::getStoreConfig()["symbol"]}} {{ ($head["total_credit"] - $head["total_debit"]) < 0 ? '('.abs($head["total_credit"] - $head["total_debit"]).')' : ($head["total_credit"] - $head["total_debit"]) }}
        @endif
        </th>
    </tfoot>
</table>
<hr style="background: gray">
    {{-- @dump($head) --}}
@endforeach
</div>

{{-- End head loop --}}
@endforeach
<style>
    table{
        width: 100% ;
    }
    
    table, th, td {
  border: 1px solid gray;
  border-collapse: collapse;
  font-size: 12px;
}

    .dates{
        float: right
    }

    .custom-box{
border : solid 1px gray;

background: #fbfbfb;
}
.custom-box > h2, .custom-box > h3, .custom-box > h4, .custom-box > h5{
    text-align: center;
    text-transform: capitalize;
    background: rgb(55, 55, 55);
    color: white;
    margin:0  
}
</style>
@endsection