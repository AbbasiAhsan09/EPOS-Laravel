@extends('reports.layout')
@section("report_content")

@php
    $config = ConfigHelper::getStoreConfig();
    $total_debit = 0;
    $total_credit = 0;
@endphp
    <div class="container-fluid">
        <table class="table table-responsive-sm table-sm table-striped table-bordered ">
            <thead class="table-dark">
                <tr>
                    <th style="width: 300px">Account Title</th>
                    <th style="width: 200px">{{'Debit' }}</th>
                    <th style="width: 200px">{{ 'Credit' }}</th>
             
                </tr>
            </thead>
        </table>

        @foreach ($data as $key => $item)
            @if ($item["total_credit"] !== 0 || $item["total_debit"] !== 0)
            <table class="table table-responsive-sm table-sm table-striped table-bordered ">
           
                <tbody>
                   @if ($item['heads'] && count($item['heads']))
                   @foreach ($item['heads'] as $child)
                  
                   <tr>
                       <td style="width: 300px">{{ $child['title'] ?? "" }}</td>
                       <td style="width: 200px">{{ $config["symbol"] ." ". number_format($child['debit'],2) }}</td>
                       <td style="width: 200px">{{ $config["symbol"] ." ". number_format($child['credit'],2) }}</td>
                
                   </tr>
                   @endforeach
                   @endif
                </tbody>
                <tfoot class="table-dark">
                    <tr>
                        <th style="width: 300px">{{ $key }}</th>
                        <th style="width: 200px">{{ $config["symbol"] ." ". number_format($item["total_debit"],2) }}</th>
                        <th style="width: 200px">{{ $config["symbol"] ." ". number_format($item["total_credit"],2) }}</th>
                    </tr>
                </tfoot>
            </table>
            <hr>
            @endif
            @php
                $total_credit += $item["total_credit"];
                $total_debit += $item["total_debit"];
            @endphp
            @endforeach
            <table class="table table-responsive-sm table-sm table-striped table-bordered ">
                <tfoot class="table-dark">
                    <tr>
                        <th style="width: 300px">Grand Total</th>
                        <th style="width: 200px">{{ number_format($total_debit, 2) }}</th>
                        <th style="width: 200px">{{ number_format($total_credit, 2) }}</th>
                 
                    </tr>
                </tfoot>
            </table>
    </div>
@endsection
