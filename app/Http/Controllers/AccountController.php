<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\AccountTransaction;
use App\Models\Parties;
use App\Models\PurchaseInvoice;
use App\Models\Sales;
use Error;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

          // Handle opening balance transaction
        if ($account->opening_balance !== null) {
            // Determine whether to debit or credit the opening balance
            $is_debit = in_array($account->type, ['income', 'equity', 'liabilities']);
            $is_credit = in_array($account->type, ['expenses', 'assets']);
            
            $opening_balance_equity = Account::firstOrCreate(
                [
                    'pre_defined' => 1,
                    'type' => 'equity',
                    'title' => 'Opening Balance Equity',
                    'store_id' => Auth::user()->store_id
                ],
                [
                    'reference_type' => null,
                    'reference_id' => null,
                    'opening_balance' => 0,
                ]
            );
           
            // If the account is an asset or expense, credit the Opening Balance Equity account
            if ($is_credit) {
                

                AccountTransaction::create([
                    'account_id' => $opening_balance_equity->id, // Replace with the ID of your Opening Balance Equity account
                    'store_id' => $account->store_id,
                    'reference_type' => 'opening_balance',
                    'reference_id' => $account->id,
                    'debit' => $account->opening_balance, // Credit to Opening Balance Equity
                    'credit' => 0,
                    'transaction_date' => now(),
                    'recorded_by' => Auth::user()->id,
                    'note' => 'Debit for new account opening balance'
                ]);

                 // Create the opening balance transaction
            AccountTransaction::create([
                'account_id' => $account->id,
                'store_id' => $account->store_id,
                'reference_type' => 'opening_balance',
                'reference_id' => $account->id,
                'debit' => 0, // Debit for expenses or assets
                'credit' => $account->opening_balance, // Credit for income, equity, or liabilities
                'transaction_date' => now(), // Use Carbon for date handling
                'recorded_by' => Auth::user()->id,
                'note' => 'Account opening balance'
            ]);
            }

            // If the account is an asset or expense, credit the Opening Balance Equity account
            if ($is_debit) {
                


                 // Create the opening balance transaction
            AccountTransaction::create([
                'account_id' => $account->id,
                'store_id' => $account->store_id,
                'reference_type' => 'opening_balance',
                'reference_id' => $account->id,
                'credit' => 0, // Debit for expenses or assets
                'debit' => $account->opening_balance, // Credit for income, equity, or liabilities
                'transaction_date' => now(), // Use Carbon for date handling
                'recorded_by' => Auth::user()->id,
                'note' => 'Account opening balance'
            ]);


            
            AccountTransaction::create([
                'account_id' => $opening_balance_equity->id, // Replace with the ID of your Opening Balance Equity account
                'store_id' => $account->store_id,
                'reference_type' => 'opening_balance',
                'reference_id' => $account->id,
                'credit' => $account->opening_balance, // Credit to Opening Balance Equity
                'debit' => 0,
                'transaction_date' => now(),
                'recorded_by' => Auth::user()->id,
                'note' => 'Credit for new account opening balance'
            ]);


            }

            
        }

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
            }

            DB::beginTransaction();

            $account->update($input);

            $transactions = AccountTransaction::where(["reference_type"=>"opening_balance", "reference_id" => $account->id ])
            ->filterByStore()->delete();




            if ($account->opening_balance !== null) {
                // Determine whether to debit or credit the opening balance
                $is_debit = in_array($account->type, ['income', 'equity', 'liabilities']);
                $is_credit = in_array($account->type, ['expenses', 'assets']);
                
                $opening_balance_equity = Account::firstOrCreate(
                    [
                        'pre_defined' => 1,
                        'type' => 'equity',
                        'title' => 'Opening Balance Equity',
                        'store_id' => Auth::user()->store_id
                    ],
                    [
                        'reference_type' => null,
                        'reference_id' => null,
                        'opening_balance' => 0,
                    ]
                );
               
                // If the account is an asset or expense, credit the Opening Balance Equity account
                if ($is_credit) {
                    
    
                    AccountTransaction::create([
                        'account_id' => $opening_balance_equity->id, // Replace with the ID of your Opening Balance Equity account
                        'store_id' => $account->store_id,
                        'reference_type' => 'opening_balance',
                        'reference_id' => $account->id,
                        'debit' => $account->opening_balance, // Credit to Opening Balance Equity
                        'credit' => 0,
                        'transaction_date' => now(),
                        'recorded_by' => Auth::user()->id,
                        'note' => 'Debit for new account opening balance'
                    ]);
    
                     // Create the opening balance transaction
                AccountTransaction::create([
                    'account_id' => $account->id,
                    'store_id' => $account->store_id,
                    'reference_type' => 'opening_balance',
                    'reference_id' => $account->id,
                    'debit' => 0, // Debit for expenses or assets
                    'credit' => $account->opening_balance, // Credit for income, equity, or liabilities
                    'transaction_date' => now(), // Use Carbon for date handling
                    'recorded_by' => Auth::user()->id,
                    'note' => 'Account opening balance'
                ]);
                }
    
                // If the account is an asset or expense, credit the Opening Balance Equity account
                if ($is_debit) {
                    
    
    
                     // Create the opening balance transaction
                AccountTransaction::create([
                    'account_id' => $account->id,
                    'store_id' => $account->store_id,
                    'reference_type' => 'opening_balance',
                    'reference_id' => $account->id,
                    'credit' => 0, // Debit for expenses or assets
                    'debit' => $account->opening_balance, // Credit for income, equity, or liabilities
                    'transaction_date' => now(), // Use Carbon for date handling
                    'recorded_by' => Auth::user()->id,
                    'note' => 'Account opening balance'
                ]);
    
    
                
                AccountTransaction::create([
                    'account_id' => $opening_balance_equity->id, // Replace with the ID of your Opening Balance Equity account
                    'store_id' => $account->store_id,
                    'reference_type' => 'opening_balance',
                    'reference_id' => $account->id,
                    'credit' => $account->opening_balance, // Credit to Opening Balance Equity
                    'debit' => 0,
                    'transaction_date' => now(),
                    'recorded_by' => Auth::user()->id,
                    'note' => 'Credit for new account opening balance'
                ]);
    
    
                }
    
                
            }

            DB::commit();

            toast('Account updated successfully','info');

            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
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
    ->orderBy('id', 'ASC');

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

    public function journal_post(Request $request) {
        try {
            // Validate each entry in the array
            $request->validate([
                'account_id.*' => 'required',
                'transaction_date.*' => 'required',
            ]);

            for ($i = 0; $i < count($request->account_id); $i++) { 
               $item = [];
                $item[$i] = [
                    'account_id' => $request->account_id[$i],
                    'transaction_date' => $request->transaction_date[$i],
                    'note' => $request->note[$i],
                    'credit' => isset($request->credit[$i])? (int)$request->credit[$i] : 0,
                    'debit' =>  isset($request->debit[$i])? (int)$request->debit[$i] : 0,
                    'store_id' => Auth::user()->store_id,
                    'source_account' => $request->source_account[$i] ?? null,
                    'recorded_by' => Auth::user()->id,
                ];


                if (!empty($item[$i]["source_account"])) {
                   
                    if ($item[$i]["debit"] > 0) {
                        // Create debit entry first
                        $debit_entry1 = AccountTransaction::create($item[$i]);
                        
                        // Create corresponding credit entry
                        AccountTransaction::create([
                            'account_id' => $debit_entry1->source_account,
                            'transaction_date' => $debit_entry1->transaction_date,
                            'note' => $debit_entry1->note,
                            'store_id' => $debit_entry1->store_id,
                            'recorded_by' => $debit_entry1->recorded_by,
                            'credit' => $debit_entry1->debit > 0 ? $debit_entry1->debit : 0, 
                            'debit' => $debit_entry1->credit > 0 ? $debit_entry1->credit : 0, 
                        ]);
                    } 
                    if($item[$i]["credit"] > 0){
                        // Handle the case where debit is not present
                        AccountTransaction::create([
                            'account_id' => $item[$i]['source_account'],
                            'transaction_date' => $item[$i]['transaction_date'],
                            'note' => $item[$i]['note'],
                            'store_id' => $item[$i]['store_id'],
                            'recorded_by' => $item[$i]["recorded_by"],
                            'credit' => $item[$i]["debit"] > 0 ? $item[$i]["debit"] : 0, 
                            'debit' => $item[$i]["credit"] > 0 ? $item[$i]["credit"] : 0, 
                        ]);
    
                        // Create the credit entry as well
                        AccountTransaction::create($item[$i]);
                    }

                } else {
                    // Create entry without source account
                    AccountTransaction::create($item[$i]);
                }

            }
    
            
            toast('Transactions added successfully', 'success');
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

            $test = DB::select('SELECT 
a.id,
    a.title,
    a.type,
    SUM(at.debit) AS total_debit,
    SUM(at.credit) AS total_credit,
    (SUM(at.debit) - SUM(at.credit)) AS balance
FROM 
    accounts AS a
LEFT JOIN 
    account_transactions AS at ON a.id = at.account_id
GROUP BY 
    a.id, a.title, a.type
ORDER BY 
    a.type, a.title;');

    return($test);
    
            $ledger_accounts = ["Cash","sales","Purchase"];

            $ledger = AccountTransaction::select('account_id')
            ->with("account")
            ->selectRaw('SUM(debit) AS total_debit')
            ->selectRaw('SUM(credit) AS total_credit')
            ->where('store_id', Auth::user()->store_id)
            ->groupBy('account_id')
            ->get();

            return view("accounts.reports.general-ledger", compact('ledger'));

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
