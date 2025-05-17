@extends('layouts.app')
@section('content')
    @php
        $config = ConfigHelper::getStoreConfig();
        $total_debit = 0;
        $total_credit = 0;
        $netProfitMonthWise = [];
        $grossProfileMonthWise = [];
    @endphp
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <div class="col-lg-3">
                <h4>Profit & Loss</h4>
            </div>

            <div class="col-lg-6">
                <form>
                    <div class="row  align-items-end">
                    <div class="col-lg-4">
                        <label for="">Year</label>
                    <div class="input-group input-group-outline">

                        <select name="year" class="form-control" >
                            <option value="">Current Year</option>
                            @foreach ($yearList as $year)
                                <option value="{{ $year }}" {{ request()->query("year") == $year ? "selected" : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-lg-4">
                    <label for="">Format</label>
                    <div class="input-group input-group-outline">
                        <select name="type" class="form-control" >
                            <option value="">WEB</option>
                            <option value="pdf" {{ request()->query("type") == 'pdf' ? 'selected' : ''  }}>PDF</option>
                        </select>
                    </div>
                </div>
                <div class="col-lg-3">
                    <button class="btn btn-primary m-0 w-100 btm-sm">Generate</button>
                </div>
                </div>
                </form>
            </div>

            {{-- <a href="/reports/accounting/trial-balance?report-type=pdf" class="btn btn-primary">PDF Report</a> --}}

        </div>

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
