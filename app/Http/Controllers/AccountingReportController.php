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
                $query->where('credit', '>', 0)
                      ->orWhere('debit', '>', 0);
            })->filterByStore();

            if($request->has('accounts') && count($request->accounts) > 0){
                $accounts = $accounts->whereIn("id",$request->accounts);
            }

            $accounts = $accounts->with(['transactions' => function ($query) use ($startDate, $endDate) {
                $query
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->where(function($subQry){
                    $subQry->where("credit", '>',0)->orWhere("debit", '>',0);
                })
                ->with("sale.order_details.item_details")
                ->with("purchase.details.items")
                ->with("sale_return.order_details.item_details")
                ->with("purchase_return")
                ->orderByRaw("CASE WHEN (reference_type = 'opening_balance_customer' OR  reference_type =  'opening_balance_vendor') THEN 0 ELSE 1 END")
                ->orderBy('transaction_date');
            }])->get();
            // dd($accounts);

            // Initialize ledger data array
            $ledgerData = [];

            foreach ($accounts as $account) {
                // Calculate starting balance (all transactions before the start date)
                $startingBalance = AccountTransaction::where('account_id', $account->id)->filterByStore()
                    ->where(function($subQry){
                        $subQry->where("credit", '>',0)->orWhere("debit", '>',0);
                    })
                    ->where('transaction_date', '<', $startDate)
                    ->sum(DB::raw('debit - credit'));

                // Initialize running balance with starting balance
                $runningBalance = $startingBalance;

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
                $query->where('credit', '>', 0)
                      ->orWhere('debit', '>', 0);
            })->filterByStore()->get();
            $accounts = $all_accounts->groupBy("type");
            // dd($accounts);
            return view("reports.accounts.general-ledger",compact("ledgerData","accounts"));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
