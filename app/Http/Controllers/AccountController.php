<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\AccountTransaction;
use App\Models\Parties;
use App\Models\PurchaseInvoice;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Account::orderBy('title', 'ASC')->byUser()->filterByStore()->get();
        return view('accounts.index',compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreAccountRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreAccountRequest $request)
    {
        try {

            $input = [
                'title' => $request->title,
                'type' => $request->type,
                'opening_balance' => $request->opening_balance ?? null,
                'description' => $request->description ?? null,
                'color_code' => $request->color_code ?? null
            ];
    
            $input['store_id'] = Auth::user()->store_id ?? null;
    
            $account = Account::create($input);
    
            if(!$account){
                toast('Failed to create a new account. please try again or contact support','error');
                
            }
            
            // Opening balance
            AccountTransaction::create([
                'account_id' => $account->id,
                'store_id' => $account->store_id,
                'reference_type' => 'opening_balance',
                'reference_id' => $account->id,
                'debit' => $account->opening_balance !== null ? $account->opening_balance : 0, 
                'credit' => 0,
                'transaction_date' => date("Y-m-d",time()),
                'recorded_by' => Auth::user()->id,
                'note' => 'Account opening balance' 
            ]);

            toast('New account created successfully','success');

            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function show(Account $account)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function edit(Account $account)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateAccountRequest  $request
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateAccountRequest $request, int $id)
    {
        try {
            $input = [
                'title' => $request->title,
                'type' => $request->type,
                'opening_balance' => $request->opening_balance ?? null,
                'description' => $request->description ?? null,
                'color_code' => $request->color_code ?? null
            ];

            $account = Account::where("id",$id)->byUser()->filterByStore()->first();

            if(!$account){
                toast('Invalid Account ID','error');
            }else{
                $account->update($input);
                $opening_transaction = AccountTransaction::where([
                    'reference_type' => 'opening_balance',
                    'reference_id' => $account->id,
                    'account_id' => $account->id,
                ])->first();
                
                if($opening_transaction){
                    $opening_transaction->update(
                        [
                            'debit' => $account->opening_balance !== null  ? $account->opening_balance : 0, 
                            'credit' => 0,
                        ]
                    );
                }else{
                    AccountTransaction::create([
                        'account_id' => $account->id,
                        'store_id' => $account->store_id,
                        'reference_type' => 'opening_balance',
                        'reference_id' => $account->id,
                        'debit' => $account->opening_balance !== null  ? $account->opening_balance : 0, 
                        'credit' => 0,
                        'transaction_date' => date("Y-m-d",time()),
                        'recorded_by' => Auth::user()->id,
                        'note' => 'Account opening balance' 
                    ]);
                }
                
            }




            toast('Account updated successfully','info');

            return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Account  $account
     * @return \Illuminate\Http\Response
     */
    public function destroy(Account $account)
    {
        //
    }


    function journal() {

        $accounts =  Account::orderBy('title', 'ASC')->where('title','!=','Cash Sales')
        ->byUser()->filterByStore()->get();

        return view('accounts.journal',compact('accounts'));
    }

    public function journal_entries(Request $request) {

        session()->forget("j_entry_from");
        session()->forget("j_entry_to");
        session()->forget("j_entry_transaction_type");
        session()->forget("j_entry_account_id");

    $entries = AccountTransaction::where("store_id", Auth::user()->store_id)
    ->with('account')
    ->orderBy('id', 'DESC');

        // Filter by date range
        if ($request->has("from") && $request->from && $request->has('to') && $request->to) {
            $entries = $entries->whereBetween('transaction_date', [$request->from, $request->to]);
            session()->put('j_entry_from',$request->from);
            session()->put('j_entry_to',$request->to);
        }

        // Filter by account ID
        if ($request->has('account_id') && $request->account_id) {
            $entries = $entries->where("account_id", $request->account_id);
            session()->put('j_entry_account_id',$request->account_id);

        }

        // Filter by transaction type
        if ($request->has("transaction_type") && $request->transaction_type) {
            if ($request->transaction_type === 'credit') {
                $entries = $entries->where("credit", '>=', 1);
            } elseif ($request->transaction_type === 'debit') {
                $entries = $entries->where("debit", '>=', 1);
            }
            session()->put('j_entry_transaction_type',$request->transaction_type);

        }

        // Execute the query and paginate the results
        $entries = $entries->paginate(20);


        $accounts =  Account::orderBy('title', 'ASC')
        ->byUser()->filterByStore()->get();

        return view('accounts.journal-entries', compact('entries','accounts'));
    }

    public function journal_post(Request $request)  {
        try {
            // dd($request->all());
            $validate = $request->validate([
                'account_id' => 'required',
                'transaction_date' => 'required',
            ]); 

            if(!$validate){
                toast('Please fill the appropriate fields','error');
                return redirect()->back();
            }
            // dd($request->all());

            for ($i=0; $i < count($request->account_id); $i++) { 
                $item = [
                    'account_id' => $request->account_id[$i],
                    'transaction_date' => $request->transaction_date[$i],
                    'note' => $request->note[$i],
                    'credit' => isset($request->credit[$i]) ? $request->credit[$i] : 0,
                    'debit' => isset($request->debit[$i]) ? $request->debit[$i] : 0,
                    'store_id' => Auth::user()->store_id,
                    'source_account' => $request->source_account[$i],
                    'recorded_by' => Auth::user()->id,
                ];
                
                if(!$item["credit"]  && !$item["debit"]){
                    continue;
                }

                $first_entry = AccountTransaction::create($item);
                if($first_entry){
                    
                    $second_entry = AccountTransaction::create([
                        'account_id' => $first_entry->source_account,
                        'transaction_date' => $first_entry->transaction_date,
                        'note' => $first_entry->note,
                        'store_id' => $first_entry->store_id,
                        'recorded_by' => $first_entry->recorded_by,
                        'credit' => $first_entry->debit > 0 ? $first_entry->debit: 0, 
                        'debit' => $first_entry->credit > 0 ? $first_entry->credit: 0, 
                    ]);
                    
                    // $account = $transaction["account"];
                    
                    // if($account["reference_type"] === "vendor" && $transaction["debit"] > 0){
                        
                    //     $vendor_req =[
                    //         'amount' => $transaction["debit"],
                    //         'date' => $transaction["transaction_date"]
                    //     ];
                    //     $vendorLedgerController = new VendorLedgerController();
                    //     $vendorLedgerController->update_invoice_bulk_payments($vendor_req, $account["reference_id"]);
                    // }

                    // if($account["reference_type"] === "customer" && $transaction["credit"] > 0){
                        
                    //     $customer_req =[
                    //         'amount' => $transaction["credit"],
                    //         'date' => $transaction["transaction_date"]
                    //     ];
                    //     $customerLedgerController = new CustomerLedgerController();
                    //     $customerLedgerController->update_invoice_bulk_payments($customer_req, $account["reference_id"]);
                    // }

                }


            }

            toast('Transactions added successfully','success');
            return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function account_details(int $account_id, int $store_id){
        try {
            $receivable_balance = 0;
            $payable_balance = 0;
            $account = Account::where(["id" => $account_id, 'store_id' => $store_id])->first()->toArray();
            $debited = AccountTransaction::where("account_id" , $account['id'])->sum('debit');
            $credited = AccountTransaction::where("account_id" , $account['id'])->sum('credit');
            $account_reference = [
                'type' => $account["reference_type"],
                'id' => $account["reference_id"]
            ];

            if(count($account_reference) && !empty($account_reference["id"]) && !empty($account_reference["type"])){
                // If vendor
                if($account_reference["type"] === 'vendor'){
                    $party = Parties::find($account_reference["id"]);
                    $invoice_total = PurchaseInvoice::where("party_id", $account_reference["id"])->sum("net_amount");
                    $payable_balance = (($invoice_total + ($party->opening_balance ?? 0)) - $debited); 
                }

                // if customer
                if($account_reference["type"] === 'customer'){
                  $party = Parties::find($account_reference["id"]);
                  $invoice_total = Sales::where("customer_id", $account_reference["id"])->sum("net_total");
                  $receivable_balance = ($invoice_total + ($party->opening_balance ?? 0)) - $credited; 
                }
            }

            return [
                "account_details" => $account,
                "receivable_balance" => $receivable_balance,
                "payable_balance" => $payable_balance
            ];
            
           

            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function transaction_destroy(int $transaction_id) {
        try {
            $transaction = AccountTransaction::where('id', $transaction_id)
                ->where('store_id', Auth::user()->store_id)
                ->first();
    
            if ($transaction) {
                $transaction->delete();
                toast('Transaction deleted successfully!', 'info');
            } else {
                toast('Unauthorized action!', 'error');
            }
    
            return redirect()->back();
        } catch (\Exception $e) {
    
            // Show user-friendly error message
            toast('An error occurred while deleting the transaction.', 'error');
            return redirect()->back()->withErrors('An error occurred, please try again.');
        }
    }


    public function generate_sales_ledger_report(Request $request) {
        try {
            $ledger_accounts = ["Cash","sales","Purchase"];

            $ledger = AccountTransaction::select('account_id')
            ->with("account")
            ->selectRaw('SUM(debit) AS total_debit')
            ->selectRaw('SUM(credit) AS total_credit')
            ->where('store_id', Auth::user()->store_id)
            ->groupBy('account_id')
            ->get();

            return $ledger;

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
