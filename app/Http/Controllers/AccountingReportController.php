<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Parties;
use App\Models\PartyGroups;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

            if($request->has('accounts') && count($request->accounts) > 0){
                $accounts = $accounts->whereIn("id",$request->accounts);
            }

            $accounts = $accounts->with(['transactions' => function ($query) use ($startDate, $endDate) {
                $query
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->where(function($subQry){
                    $subQry->where("credit", '!=',0)->orWhere("debit", '!=',0);
                })
                ->with("sale.order_details.item_details")
                ->with("purchase.details.items")
                ->with("source_account_detail")
                ->with("sale_return.order_details.item_details")
                ->with("purchase_return")
                ->orderByRaw("CASE WHEN (reference_type = 'opening_balance_customer' OR  reference_type =  'opening_balance_vendor'  OR  reference_type =  'opening_balance') THEN 0 ELSE 1 END")
                ->orderBy('transaction_date')
                ->orderBy('credit',"DESC")
                ;
            }])->get();
            // dd($accounts);

            // Initialize ledger data array
            $ledgerData = [];

            foreach ($accounts as $account) {
                // Calculate starting balance (all transactions before the start date)
                $startingBalance = AccountTransaction::where('account_id', $account->id)->filterByStore()
                    ->where(function($subQry){
                        $subQry->where("credit", '!=',0)->orWhere("debit", '!=',0);
                    })
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));

                // Initialize running balance with starting balance
                $runningBalance = $startingBalance;

                $totalDebit = AccountTransaction::where("account_id",$account->id)->sum("debit");
                $totalCredit = AccountTransaction::where("account_id",$account->id)->sum("credit");

          
                // Collect all transactions for the account within the specified period
                $transactionsData = [];
                foreach ($account->transactions as $transaction) {
                    // Calculate running balance for each transaction
                    $runningBalance += $transaction->debit - $transaction->credit;

                    // Prepare transaction data with running balance
                    $transactionsData[] = [
                        'transaction_date' => date("d/m/Y",strtotime($transaction->transaction_date)),
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

            if($request->has("type") && $request->type === 'pdf'){
                $data = ["ledgerData" => $ledgerData,
                        'report_title' => 'Ledger Report',
                        'from' => $startDate,
                        'to' => $endDate
                    ];
                $pdf = Pdf::loadView('reports.accounts.pdfs.general-ledger', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }

            $all_accounts = Account::filterByStore()->whereHas('transactions', function ($query) {
                $query->where('credit', '!=', 0)
                      ->orWhere('debit', '!=', 0);
            })->filterByStore()->get();
            $accounts = $all_accounts->groupBy("type");
            // dd($accounts);
            return view("reports.accounts.general-ledger",compact("ledgerData","accounts"));
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function account_balance_report(Request $request)  {
        try {

            // $query = "SET PERSIST sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));";
            // DB::statement($query);

            $grandQuery = Account::filterByStore()
            ->whereHas('transactions', function ($query) {
                $query->whereNotNull('credit')
                      ->orWhereNotNull('debit');
            })
            ->with(['transactions' => function ($query) {
                $query->select('account_id', 
                    DB::raw('SUM(credit) as total_credit'), 
                    DB::raw('SUM(debit) as total_debit'))
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
            ->orderBy("type",'ASC')
            ->orderBy("title",'ASC');

            if($request->query("type")&& !empty($request->query("type"))){
                $grandQuery = $grandQuery->where("type",$request->query("type"));
            }

            if($request->query("search") && !empty($request->query("search"))){
                $grandQuery = $grandQuery->where("title","like","%".$request->query("search")."%");
            }

            if ($request->query("zero-balance") && !empty($request->query("zero-balance"))) {
                if ($request->query("zero-balance") == "NO") {
                    
                }
            }

            $total_balance = $grandQuery->get()->sum(function ($account) {
                $transaction = $account->transactions->first();
                return  $transaction->total_debit - $transaction->total_credit;
            });
        
            if($request->query("report-type") == 'pdf'){
                $accounts = $grandQuery->get();
            }else{
                $accounts = $grandQuery->paginate(50)->withQueryString();
            }
        
            // Calculate remaining balance for each account
            $accounts->each(function ($account) {
                $transaction = $account->transactions->first();
                $account->remaining_balance =  $transaction->total_debit - $transaction->total_credit;
            });
            
         

            if($request->has("report-type") && $request->query('report-type') == 'pdf'){
                $data = [
                        'report_title' => 'Account Balances Report',
                        'accounts' => $accounts,
                        'total_balance' => $total_balance
                    ];
                $pdf = Pdf::loadView('reports.accounts.pdfs.account-balance', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }
            
            return view("reports.accounts.account-balance", compact("accounts","total_balance"));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
