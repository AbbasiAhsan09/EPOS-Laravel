@extends('layouts.app')
@section('content')
@php
    $config = ConfigHelper::getStoreConfig();
    $total_debit = 0;
    $total_credit = 0;
@endphp
    <div class="container-fluid">
    <div class="d-flex justify-content-between">
        <div class="col-lg-3">
            <h4>Trial Balance Report</h4>
        </div>

        
            <a href="/reports/accounting/trial-balance?report-type=pdf" class="btn btn-primary">PDF Report</a>
        
    </div>

        <table class="table table-responsive-sm table-sm table-striped table-bordered ">
            <thead class="table-dark">
                <tr>
                    <th style="width: 350px">Account Title</th>
                    <th style="width: 250px">{{'Debit' }}</th>
                    <th style="width: 250px">{{ 'Credit' }}</th>
             
                </tr>
            </thead>
        </table>

        @foreach ($data as $key => $item)
        <table class="table table-responsive-sm table-sm table-striped table-bordered ">
           
            <tbody>
               @if ($item['heads'] && count($item['heads']))
               @foreach ($item['heads'] as $child)
              
               <tr>
                   <td style="width: 350px; word-wrap:break-word">{{ $child['title'] ?? "" }}</td>
                   <td style="width: 250px">{{ $config["symbol"] ." ". number_format($child['debit'],2) }}</td>
                   <td style="width: 250px">{{ $config["symbol"] ." ". number_format($child['credit'],2) }}</td>
            
               </tr>
               @endforeach
               @endif
            </tbody>
            <tfoot class="table-dark">
                <tr>
                    <th style="width: 350px">{{ $key }}</th>
                    <th style="width: 250px">{{ $config["symbol"] ." ". number_format($item["total_debit"],2) }}</th>
                    <th style="width: 250px">{{ $config["symbol"] ." ". number_format($item["total_credit"],2) }}</th>
                </tr>
            </tfoot>
        </table>
            @php
                $total_credit += $item["total_credit"];
                $total_debit += $item["total_debit"];
            @endphp
            @endforeach
            <table class="table table-responsive-sm table-sm table-striped table-bordered ">
                <tfoot class="table-dark">
                    <tr>
                        <th style="width: 350px">Grand Total</th>
                        <th style="width: 250px">{{ number_format($total_debit, 2) }}</th>
                        <th style="width: 250px">{{ number_format($total_credit, 2) }}</th>
                 
                    </tr>
                </tfoot>
            </table>
    </div>
@endsection
