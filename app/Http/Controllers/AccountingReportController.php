<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Parties;
use App\Models\PartyGroups;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingReportController extends Controller
{
    public function customer_payments(Request $request)
    {
        try {

            $data = AccountTransaction::whereHas("account", function ($accountQry) {
                $accountQry->where("reference_type", 'customer')->orWhere("account_number", 1000);
            })->with("account", "source_account_detail")
                ->where("credit", ">", 0)->get();

            $customers = Parties::where("group_id", PartyGroups::where("group_name", "like", "%customer%")->first()->id)->filterByStore()->get();

            // dd($data);
            return view("reports.accounts.customer-payments", compact("data", "customers"));
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function generate_general_ledger_report(Request $request)
    {
        try {
            // dd($request->all());
            $startDate = $request->has("from") && $request->input("from") ? Carbon::parse($request->input("from")) : Carbon::now()->subDay(11);
            $endDate = $request->has("to") && $request->input("to") ? Carbon::parse($request->input("to")) : Carbon::now();

            // Fetch all accounts with their transactions within the date range
            $accounts = Account::whereHas('transactions', function ($query) {
                $query->where('credit', '!=', 0)
                    ->orWhere('debit', '!=', 0);
            })->filterByStore();

            if ($request->has('accounts') && count($request->accounts) > 0) {
                $accounts = $accounts->whereIn("id", $request->accounts);
            }

            $accounts = $accounts->with(['transactions' => function ($query) use ($startDate, $endDate) {
                $query
                    ->whereBetween('transaction_date', [$startDate, $endDate])
                    ->where(function ($subQry) {
                        $subQry->where("credit", '!=', 0)->orWhere("debit", '!=', 0);
                    })
                    ->with("sale.order_details.item_details")
                    ->with("purchase.details.items")
                    ->with("source_account_detail")
                    ->with("sale_return.order_details.item_details")
                    ->with("voucher.voucher_type")
                    ->with("purchase_return")
                    ->orderByRaw("CASE WHEN (reference_type = 'opening_balance_customer' OR  reference_type =  'opening_balance_vendor'  OR  reference_type =  'opening_balance') THEN 0 ELSE 1 END")
                    ->orderBy('transaction_date')
                    ->orderBy('credit', "DESC")
                ;
            }])->get();
            // dd($accounts);

            // Initialize ledger data array
            $ledgerData = [];

            foreach ($accounts as $account) {
                // Calculate starting balance (all transactions before the start date)
                $startingBalance = AccountTransaction::where('account_id', $account->id)->filterByStore()
                    ->where(function ($subQry) {
                        $subQry->where("credit", '!=', 0)->orWhere("debit", '!=', 0);
                    })
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));

                // Initialize running balance with starting balance
                $runningBalance = $startingBalance;

                $totalDebit = AccountTransaction::where("account_id", $account->id)->sum("debit");
                $totalCredit = AccountTransaction::where("account_id", $account->id)->sum("credit");


                // Collect all transactions for the account within the specified period
                $transactionsData = [];
                foreach ($account->transactions as $transaction) {
                    // Calculate running balance for each transaction
                    $runningBalance += $transaction->debit - $transaction->credit;

                    // Prepare transaction data with running balance
                    $transactionsData[] = [
                        'transaction_date' => date("d/m/Y", strtotime($transaction->transaction_date)),
                        'description' => $transaction->note,
                        'debit' => $transaction->debit,
                        'credit' => $transaction->credit,
                        'running_balance' => $runningBalance,
                        'data' => $transaction
                    ];
                }

                // Store account ledger data
                $ledgerData[] = [
                    'account' => $account->title,
                    'account_type' => $account->type,
                    'description' => $account->note,
                    'starting_balance' => $startingBalance,
                    'transactions' => $transactionsData,
                    'ending_balance' => $runningBalance,
                    // 'data' => $account
                ];
            }

            // return ($ledgerData);

            if ($request->has("type") && $request->type === 'pdf') {
                $data = [
                    "ledgerData" => $ledgerData,
                    'report_title' => 'Ledger Report',
                    'from' => $startDate,
                    'to' => $endDate
                ];
                $pdf = Pdf::loadView('reports.accounts.pdfs.general-ledger', $data)->setPaper('a4', 'portrait');
                // dd($ledgerData);
                return $pdf->stream();
            }

            $all_accounts = Account::filterByStore()->whereHas('transactions', function ($query) {
                $query->where('credit', '!=', 0)
                    ->orWhere('debit', '!=', 0);
            })->filterByStore()->get();
            $accounts = $all_accounts->groupBy("type");
            return view("reports.accounts.general-ledger", compact("ledgerData", "accounts"));
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function account_balance_report(Request $request)
    {
        try {

            // $query = "SET PERSIST sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));";
            // DB::statement($query);

            $grandQuery = Account::filterByStore()
                ->whereHas('transactions', function ($query) {
                    $query->whereNotNull('credit')
                        ->orWhereNotNull('debit');
                })
                ->with(['transactions' => function ($query) {
                    $query->select(
                        'account_id',
                        DB::raw('SUM(credit) as total_credit'),
                        DB::raw('SUM(debit) as total_debit')
                    )
                        ->groupBy('account_id');
                }])
                ->orderByRaw("CASE 
                    WHEN title LIKE '%cash%' THEN 0
                    ELSE 1 
                  END")
                ->orderByRaw("CASE 
                    WHEN title LIKE '%bank%' THEN 0
                    ELSE 1 
            END")
                ->orderByRaw("CASE 
            WHEN reference_type IS NOT NULL THEN 0
            ELSE 1 
            END")
                ->orderBy("type", 'ASC')
                ->orderBy("title", 'ASC');

            if ($request->query("type") && !empty($request->query("type"))) {
                $grandQuery = $grandQuery->where("type", $request->query("type"));
            }

            if ($request->query("search") && !empty($request->query("search"))) {
                $grandQuery = $grandQuery->where("title", "like", "%" . $request->query("search") . "%");
            }

            if ($request->query("zero-balance") && !empty($request->query("zero-balance"))) {
                if ($request->query("zero-balance") == "NO") {
                }
            }

            $total_balance = $grandQuery->get()->sum(function ($account) {
                $transaction = $account->transactions->first();
                return  $transaction->total_debit - $transaction->total_credit;
            });

            if ($request->query("report-type") == 'pdf') {
                $accounts = $grandQuery->get();
            } else {
                $accounts = $grandQuery->paginate(50)->withQueryString();
            }

            // Calculate remaining balance for each account
            $accounts->each(function ($account) {
                $transaction = $account->transactions->first();
                $account->remaining_balance =  $transaction->total_debit - $transaction->total_credit;
            });



            if ($request->has("report-type") && $request->query('report-type') == 'pdf') {
                $data = [
                    'report_title' => 'Account Balances Report',
                    'accounts' => $accounts,
                    'total_balance' => $total_balance
                ];
                $pdf = Pdf::loadView('reports.accounts.pdfs.account-balance', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }

            return view("reports.accounts.account-balance", compact("accounts", "total_balance"));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function trial_balance_report(Request $request)
    {
        try {

            // dd("hi");
            DB::statement("SET SESSION sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

            $temp = DB::table('accounts as head')
                ->leftJoin('accounts as child', 'child.parent_id', '=', 'head.id')
                ->leftJoin('account_transactions as at', function ($join) {
                    $join->on('at.account_id', '=', 'head.id')
                        ->orOn('at.account_id', '=', 'child.id');
                })
                ->where('head.store_id', Auth()->user()->store_id)
                ->where('head.head_account', 1)
                ->where('at.deleted_at', null)
                ->groupBy('head.id', 'head.title') // best to group by both in MySQL strict mode
                ->select(
                    'head.account_number',
                    'head.id',
                    'head.title',
                    DB::raw('SUM(at.credit) as credit'),
                    DB::raw('SUM(at.debit) as debit'),
                    'head.parent_id'
                )
                ->get()->toArray();

            $coas = Account::where("coa", 1)->filterByStore()->get();

            $opening_stock_cost = DB::table('products')
                ->selectRaw('SUM(opening_stock * CASE WHEN opening_stock_unit_cost > 0 THEN opening_stock_unit_cost ELSE tp END) AS opening_stock_value')
                ->where('opening_stock', '!=', 0)
                ->where('deleted_at', null)
                ->where('store_id', '=', Auth()->user()->store_id)
                ->value('opening_stock_value');

            $results = array_map(function ($account) use ($opening_stock_cost) {
                if ($account->account_number == 1030) {

                    $accountArray = (array) $account;
                    $accountArray['debit'] = $account->debit + $opening_stock_cost;
                    return $accountArray;
                }
                return (array) $account;
            }, $temp);

            $data = [];

            foreach ($coas as $key => $coa) {
                if ($data && isset($data[$coa->id])) {
                } else {
                    $total_credit = 0;
                    $total_debit = 0;

                    $data_objects = array_filter($results, function ($obj) use ($coa) {
                        return $obj['parent_id'] === $coa->id;
                    });

                    if ($data_objects && count($data_objects)) {
                        foreach ($data_objects as $key => $object) {
                            // dump($object);
                            $total_credit += $object['credit'];
                            $total_debit += ($object['debit']);
                        }
                    }

                    $data[$coa->account_number . " " . $coa->title] = [
                        'coa' => $coa->title,
                        'heads' => $data_objects,
                        'total_credit' => $total_credit,
                        'total_debit' => $total_debit
                    ];
                }
            }


            if ($request->has("report-type") && $request->query('report-type') == 'pdf') {
                $data = ['data' => $data, 'report_title' => 'Trial Balance'];
                $pdf = Pdf::loadView('reports.accounts.pdfs.trial-balance', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }

            return view('reports.accounts.trial-balance', compact('data'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function month_wise_profit_loss(Request $request)
    {
        try {
            $months = [
                ['title' => 'Jan', 'month' => 1],
                ['title' => 'Feb', 'month' => 2],
                ['title' => 'Mar', 'month' => 3],
                ['title' => 'Apr', 'month' => 4],
                ['title' => 'May', 'month' => 5],
                ['title' => 'Jun', 'month' => 6],
                ['title' => 'Jul', 'month' => 7],
                ['title' => 'Aug', 'month' => 8],
                ['title' => 'Sep', 'month' => 9],
                ['title' => 'Oct', 'month' => 10],
                ['title' => 'Nov', 'month' => 11],
                ['title' => 'Dec', 'month' => 12],
            ];
            $startYear = 2000;
            $currentYear = date('Y');
            $yearList = [];

            for ($year = $currentYear; $year >= $startYear; $year--) {
                if($year !== $currentYear){
                    $yearList[] = $year;
                }
            }
        
            $year = $request->query('year') && !empty($request->query('year') ) ?$request->query('year') : date('Y');
            $storeId = auth()->user()->store_id;

            // Fetch head accounts
            $headAccounts = DB::table('accounts')
                ->where('head_account', 1)
                ->where('store_id', $storeId)->whereIn("type", ['expenses', 'income'])
                ->get();

            $data = [];

            foreach ($headAccounts as $head) {
                // Get IDs: head + its sub accounts
                $accountIds = DB::table('accounts')
                    ->where('store_id', $storeId)
                    ->where(function ($q) use ($head) {
                        $q->where('id', $head->id)
                            ->orWhere('parent_id', $head->id);
                    })
                    ->pluck('id')
                    ->toArray();

                // Get monthly totals for this head
                $transactions = DB::table('account_transactions')
                    ->whereIn('account_id', $accountIds)
                    ->whereNull('deleted_at')
                    ->selectRaw("
                        MONTH(transaction_date) as month,
                        SUM(debit) as total_debit,
                        SUM(credit) as total_credit
                    ")
                    ->where('store_id', $storeId)
                    ->whereNull("deleted_at")->where(DB::raw("YEAR(transaction_date)"), $year)
                    ->groupBy(DB::raw("MONTH(transaction_date)"))
                    ->orderBy('month')
                    ->get()->toArray();

                // Append to results
                $data[$head->title] = [
                    'account_number' => $head->account_number,
                    'transactions' => $transactions,
                    'title' => $head->title ?? "",
                    "type" => $head->type
                ];
            }

             if ($request->has("type") && $request->query('type') == 'pdf') {
                $data = ['data' => $data, 'months' => $months, 'report_title' => "Profit & Loss - ".$year];
                $pdf = Pdf::loadView('reports.accounts.pdfs.month-wise-pnl', $data)->setPaper('a4', 'landscape');
                return $pdf->stream();
            }

            return view('reports.accounts.month-wise-pnl', compact('data','months','yearList'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
