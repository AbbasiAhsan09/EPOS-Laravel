@extends('reports.layout')
@section("report_content")

 @php
        $config = ConfigHelper::getStoreConfig();
        $total_debit = 0;
        $total_credit = 0;
        $netProfitMonthWise = [];
        $grossProfileMonthWise = [];
    @endphp


    <div class="container-fluid">
               <table class="table table-responsive-sm table-sm table-striped table-bordered ">
            <thead>

                <th style="width: 350px">Account Title</th>
                @foreach ($months as $month)
                @php
                    $grossProfileMonthWise[$month["month"]] = 0;  
                    $netProfitMonthWise[$month["month"]] = 0;  
                @endphp
                    <th>{{ $month['title'] ?? '' }}</th>
                @endforeach

            </thead>
            <tbody>
                @foreach ($data as $item)
                    @if (true)
                        <tr>
                            <td>{{ $item['title'] }}</td>
                            @foreach ($months as $curr_month)
                                @php
                                    $transaction = collect($item['transactions'])->firstWhere(
                                        'month',
                                        $curr_month['month'],
                                    );
                                @endphp

                                <td>
                                    @if ($transaction)
                                        @php
                                            if($item["type"] === 'income' || $item['account_number'] == 5000){
                                                $grossProfileMonthWise[$transaction->month] += ($transaction->total_credit ?? 0) - ($transaction->total_debit ?? 0);
                                            }  
                                            $netProfitMonthWise[$transaction->month] += ($transaction->total_credit ?? 0) - ($transaction->total_debit ?? 0);  
                                        @endphp
                                        {{ number_format(($transaction->total_credit ?? 0) - ($transaction->total_debit ?? 0),2) }}
                                    @else
                                      -
                                    @endif
                                </td>

                            @endforeach
                        </tr>
                    @endif
                @endforeach

                <tfoot>
                    <tr>
                        <th>
                            Gross Profit
                        </th>
                        @foreach ($grossProfileMonthWise as $gross)
                            <th>{{ number_format($gross,2) }}</th>
                        @endforeach
                    </tr>
                     <tr>
                        <th>
                            Net Profit
                        </th>
                        @foreach ($netProfitMonthWise as $net)
                            <th>{{ number_format($net,2) }}</th>
                        @endforeach
                    </tr>
                </tfoot>
                {{--  Print Cogs --}}
            </tbody>
        </table>
    </div>
@endsection
