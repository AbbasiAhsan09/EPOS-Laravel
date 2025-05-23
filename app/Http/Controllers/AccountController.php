<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\AccountTransaction;
use App\Models\Parties;
use App\Models\PurchaseInvoice;
use App\Models\Sales;
use Barryvdh\DomPDF\Facade\Pdf;
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
    public function index(Request $request)
    {
        DB::enableQueryLog();
        // dd($request->all());
        $items = Account::orderBy('title', 'ASC')
            ->where('coa', false)
            ->byUser()
            ->with('parent.parent')
            ->filterByStore();
       
        if($request->has('head_accounts')){
            $items = $items->where('head_account', true);
        }else{
            $items = $items->where('head_account', false);
        }

        if($request->query("search")){
            $items = $items->where("title",'LIKE','%'.$request->query("search").'%');
        }

        // Assign the paginated result back to $items and keep query strings intact
        $items = $items->paginate(20)->withQueryString();
        // dd($items);
        $coas = Account::orderBy('title', 'ASC')->where('coa', true)->byUser()->filterByStore()->get();
        return view('accounts.index',compact('items','coas'));
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
            // dd($request->all());

            $input = [
                'title' => $request->title,
                'parent_id' => $request->has('parent_id') ? $request->parent_id : null, 
                'opening_balance' => $request->opening_balance !== null && !empty($request->opening_balance) ?  $request->opening_balance : 0,
                'description' => $request->description ?? null,
                'color_code' => $request->color_code ?? null
            ];

            if($request->has('parent_id')){
                $input['parent_id'] = $request->has('parent_id') ? $request->parent_id : null;
            }else{
                $input["parent_id"] = $request->has("coa_id") ? $request->input("coa_id") : null; 
            }
            
            $coa = Account::where(['coa' => true, 'id' => $request->coa_id, 'store_id' => Auth::user()->store_id])->first();
            
            if(!$coa){
                toast('Bad Request Please Contact Support','error');
                return redirect()->back();
            }

            $input["type"] = $coa->type;
            $input["head_account"] = $request->has('parent_id') ? false : true; 
    
            $input['store_id'] = Auth::user()->store_id ?? null;
    
            $account = Account::create($input);

            
    
            if(!$account){
                toast('Failed to create a new account. please try again or contact support','error');
                
            }

          // Handle opening balance transaction
        if ($account->opening_balance !== null) {
            // Determine whether to debit or credit the opening balance
             $is_debit =  in_array($account->type, ['expenses', 'assets','drawings']);
             $is_credit =in_array($account->type, ['income', 'equity', 'liabilities']);
            
            $opening_balance_equity_head = $this->get_head_account(['account_number' => 3000]);
            $opening_balance_equity = Account::firstOrCreate(
                [
                    'pre_defined' => 1,
                    'type' => 'equity',
                    'account_number' => 3500,
                    'store_id' => Auth::user()->store_id,
                    'parent_id' => $opening_balance_equity_head->id ?? null,

                ],
                [
                    'reference_type' => null,
                    'tilte' => 'Opening Balance Equity',
                    'reference_id' => null,
                    'opening_balance' => 0,
                ]
            );
           
            $this->record_journal_entry([
                'account_id' => $account->id, // Replace with the ID of your Opening Balance Equity account
                'reference_type' => 'opening_balance',
                'reference_id' => $account->id,
                'debit' => $is_debit  ? $account->opening_balance : 0, // Credit to Opening Balance Equity
                'credit' => $is_credit ? $account->opening_balance : 0,
                'transaction_date' => '2000-01-01',
                'recorded_by' => Auth::user()->id,
                'note' => 'Debit for new account opening balance',
                'source_account' => $opening_balance_equity->id
            ]);

            
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
            // dd($request->all());
            $input = [
                'title' => $request->title,
                'opening_balance' => $request->opening_balance !== null ? $request->opening_balance : 0,
                'description' => $request->description ?? null,
                'color_code' => $request->color_code ?? null
            ];

            if($request->has('parent_id')){
                $input['parent_id'] = $request->has('parent_id') ? $request->parent_id : null;
            }else{
                $input["parent_id"] = $request->has("coa_id") ? $request->input("coa_id") : null; 
            }

            $coa = Account::where(['coa' => true, 'id' => $request->coa_id, 'store_id' => Auth::user()->store_id])->first();
            
            if(!$coa){
                toast('Bad Request Please Contact Support','error');
                return redirect()->back();
            }


            $input["type"] = $coa->type;
            $input["head_account"] = $request->has('parent_id') ? false : true; 

            $account = Account::where("id",$id)->byUser()->filterByStore()->first();

            if(!$account){
                toast('Invalid Account ID','error');
            }

            DB::beginTransaction();

            $account->update($input);

            $this->reverse_transaction([
                "reference_type"=>"opening_balance", 
                "reference_id" => $account->id,
                "date" => '',
                "description" => 'Reversed Opening balance transaction because this account ('.$account->id.') is updated by '. Auth::user()->name,
                "transaction_count" => 2,
                "order_by" => "DESC",
                "order_column" => "id"
            ]);


             // Handle opening balance transaction
        if ($account->opening_balance !== null) {
            // Determine whether to debit or credit the opening balance
             $is_debit =  in_array($account->type, ['expenses', 'assets']);
             $is_credit =in_array($account->type, ['income', 'equity', 'liabilities']);
            
            $opening_balance_equity_head = $this->get_head_account(['account_number' => 3000]);
            $opening_balance_equity = Account::firstOrCreate(
                [
                    'pre_defined' => 1,
                    'type' => 'equity',
                    'account_number' => 3500,
                    'store_id' => Auth::user()->store_id,
                    'parent_id' => $opening_balance_equity_head->id ?? null,
                    
                ],
                [
                    'title' => 'Opening Balance Equity',
                    'reference_type' => null,
                    'reference_id' => null,
                    'opening_balance' => 0,
                ]
            );
           
            $this->record_journal_entry([
                'account_id' => $account->id, // Replace with the ID of your Opening Balance Equity account
                'reference_type' => 'opening_balance',
                'reference_id' => $account->id,
                'debit' => $is_debit  ? $account->opening_balance : 0, // Credit to Opening Balance Equity
                'credit' => $is_credit ? $account->opening_balance : 0,
                'transaction_date' => '2000-01-01',
                'recorded_by' => Auth::user()->id,
                'note' => ' New account opening balance Updated',
                'source_account' => $opening_balance_equity->id
            ]);

            
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
    public function destroy($id)
    {
        try {
          
            $account = Account::where("id",$id)->filterByStore()->first();

            
            
            if(!$account){
                toast('Account not found', 'error');
                return redirect()->back();
            }
            
            $sub_account = Account::where("parent_id",$account->id)->count();
            
            if($sub_account){
                toast("This account has active sub accounts associated with this account","error");
                return redirect()->back();
            }
            
            $transactions = AccountTransaction::where("account_id",$account->id)
            ->where(function($query){
                $query->where("debit",'>',0)->orWhere("credit",">",0);
            })->count();

            if($transactions){
                toast("This account has active transactions please delete the Vouchers and Orders to delete this account","error");
                return redirect()->back();
            }

            $account->delete();

            toast("Account Deleted",'success');

            return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    function journal() {

        $accounts =  Account::orderBy('type','ASC')->orderBy('title', 'ASC')->where('title','!=','Cash Sales')
        ->byUser()->filterByStore()->where('coa',false)->get();
        $accounts = $accounts->groupBy("type");
        // dd($accounts);
        return view('accounts.journal',compact('accounts'));
    }

    public function journal_entries(Request $request) {

        session()->forget("j_entry_from");
        session()->forget("j_entry_to");
        session()->forget("j_entry_transaction_type");
        session()->forget("j_entry_account_id");

    $entries = AccountTransaction::where("store_id", Auth::user()->store_id)
    ->with('account.parent')
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


        $accounts =  Account::orderBy('type','ASC')->orderBy('title', 'ASC')
        ->byUser()->filterByStore()->get();

        $accounts = $accounts->groupBy("type");
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
               
                $item = [
                    'account_id' => $request->account_id[$i],
                    'transaction_date' => $request->transaction_date[$i],
                    'note' => $request->note[$i],
                    'credit' => isset($request->credit[$i])? (int)$request->credit[$i] : 0,
                    'debit' =>  isset($request->debit[$i])? (int)$request->debit[$i] : 0,
                    'source_account' => $request->source_account[$i] ?? null,
                ];

                $this->record_journal_entry($item);                

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


  

    public function post_journal_entry() {
        try {
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    static function reverse_transaction($reference = ['reference_type' => '', 'reference_id' => 0, 'date' => '','description' => '', 'transaction_count' => 0, 'order_by' => null, 'order_column' => 'id'], $delete = true){
        try {
            
            if(!isset($reference["date"]) || !$reference["date"] || empty($reference["date"])){
                $reference["date"] = date('Y-m-d',time());
            }

           
            if(isset($reference["reference_id"]) && !empty($reference["reference_id"]) && $reference["reference_id"] > 0 && isset($reference["reference_type"]) && !empty($reference["reference_type"])){
                $transactions = AccountTransaction::where([
                    'reference_id' => $reference['reference_id'],
                    'reference_type' =>$reference["reference_type"]
                    ])->where("store_id", Auth::user()->store_id);


                if((isset($reference["order_by"]) && !empty(isset($reference["order_by"]))) && (isset($reference["order_column"]) && !empty(isset($reference["order_column"])))){
                   $transactions = $transactions->orderBy($reference["order_column"],$reference["order_by"] ?? "ASC");
                }

                if(abs($reference["transaction_count"]) > 0){
                    $transactions = $transactions->take(abs($reference["transaction_count"]))->get();
                }else{
                    $transactions = $transactions->get();
                }

                // dd($transactions);
                DB::beginTransaction();
                if($transactions && count($transactions)){
                    foreach ($transactions as  $transaction) {

                        if($delete){
                            $transaction->delete();
                            continue;
                        }

                        if(abs($transaction->credit)> 0){
                            AccountTransaction::create([
                                'account_id' => $transaction->account_id,
                                'transaction_date' => $reference["date"],
                                'note' => '(Reversed Transaction Ref ID '.$transaction->id.')'.(isset($reference["description"]) && !empty($reference["description"]) ? $reference["description"] : $transaction->note) ,
                                'debit' => $transaction->credit,
                                'credit' => 0,
                                'reference_type' => $transaction->reference_type,
                                'reference_id' => $transaction->reference_id,
                                'recorded_by' => Auth::user()->id,
                                'store_id' => Auth::user()->store_id,
                            ]);
                        }else{
                            if(abs($transaction->debit) > 0){
                                AccountTransaction::create([
                                    'account_id' => $transaction->account_id,
                                    'transaction_date' => $reference["date"],
                                    'note' => '(Reversed Transaction Ref ID '.$transaction->id.')'.(isset($reference["description"]) && !empty($reference["description"]) ? $reference["description"] : $transaction->note) ,
                                    'debit' => 0,
                                    'credit' => $transaction->debit,
                                    'reference_type' => $transaction->reference_type,
                                    'reference_id' => $transaction->reference_id,
                                    'recorded_by' => Auth::user()->id,
                                    'store_id' => Auth::user()->store_id,
                                ]);
                            }
                        }
                    }
                }
                DB::commit();
            }

         


        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    static function record_journal_entry(
        $entry = [
        'account_id' => 0,
        'transaction_date' => null,
        'note' => null,
        'credit' => 0,
        'debit' =>  0,
        'reference_type' => null,
        'reference_id' => null,
        'source_account' => null,
    ]){
        try {
            
            
            if (empty($entry["account_id"]) || (abs($entry["debit"]) == 0 && abs($entry["credit"]) == 0)) {
                return false;
            }

            if(!isset($entry["transaction_date"]) || !($entry["transaction_date"]) ){
                $entry["transaction_date"] = date('Y-m-d',time());
            }

            $entry["store_id"] = Auth::user()->store_id;
            $entry["recorded_by"] = Auth::user()->id;
            
            DB::beginTransaction();
            if (!empty($entry["source_account"])) {
                   
                if (abs($entry["debit"]) > 0) {
                    // Create debit entry first
                    $debit_entry1 = AccountTransaction::create($entry);
                    
                    // Create corresponding credit entry
                    AccountTransaction::create([
                        'account_id' => $debit_entry1->source_account,
                        'transaction_date' => $debit_entry1->transaction_date,
                        'reference_id' => $entry["reference_id"] ?? $debit_entry1->id,
                        'reference_type' => $entry["reference_type"] ?? "journal_entry",
                        'note' => $debit_entry1->note,
                        'store_id' => $debit_entry1->store_id,
                        'recorded_by' => $debit_entry1->recorded_by,
                        'credit' => $debit_entry1->debit !== null && !empty($debit_entry1->debit) ? $debit_entry1->debit : 0, 
                        'debit' => $debit_entry1->credit !== null && !empty($debit_entry1->credit)  ? $debit_entry1->credit : 0, 
                        'source_account' => $debit_entry1->account_id,
                     ]);

                    $debit_entry1->update([
                        'reference_id' => $entry["reference_id"] ?? $debit_entry1->id,
                        'reference_type' => $entry["reference_type"] ?? "journal_entry",
                    ]);
                } 
                if(abs($entry["credit"]) > 0){
                  
                    // Handle the case where debit is not present
                   $debit_entry_2 = AccountTransaction::create([
                        'account_id' => $entry['source_account'],
                        'transaction_date' => $entry['transaction_date'],
                        'note' => $entry['note'],
                        'store_id' => $entry['store_id'],
                        'recorded_by' => $entry["recorded_by"],
                        'credit' =>  $entry["debit"] !== null && !empty($entry["debit"]) ? $entry["debit"] : 0, 
                        'debit' => $entry["credit"] !== null && !empty($entry["credit"])  ? $entry["credit"] : 0, 
                    ]);
                
                    // dd($debit_entry_2);

                    // Create the credit entry as well
                    $entry["reference_type"] = $entry["reference_type"] ?? "journal_entry";
                    $entry["reference_id"] = $entry["reference_id"] ?? $debit_entry_2->id;

                    $creditEntry = AccountTransaction::create($entry);

                    $debit_entry_2->update([
                        'reference_id' => $entry["reference_id"] ?? $debit_entry_2->id,
                        'reference_type' => $entry["reference_type"] ?? "journal_entry",
                        'source_account' => $creditEntry->account_id
                    ]);
                }

            } else {
                // Create entry without source account
                AccountTransaction::create($entry);
            }

            DB::commit();

            return true;

        } catch (\Throwable $th) {
            
            DB::rollBack();
            throw $th;
        }
    }


    // Chart of Accounts

    static function first_or_create_coa($item = ["title" => '', 'type' => '']) {
        try {

            if(!$item["title"] || !$item["type"]){
                return false;
            }

            $coa  = Account::firstOrCreate(
                [
                    'store_id' => Auth::user()->store_id,
                    'title' => $item["title"],
                    'type' => $item["type"],
                    'pre_defined' => 1,
                    'coa' => true
                ],
                [
                    'opening_balance' => 0,
                    'current_balance' => 0,
                    'This COA is system generated'
                ]
                );
            
            return $coa;
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function generate_coa(){
        try {
            
            $assets_chat_of_accounts = [
                'Current Assets',
                'Fixed Assets',
                'Other Assets',
            ];

            $liabilities_chat_of_accounts = [
                'Current Liabilities',
                'Long-Term Liabilities'
            ];


            $equity_chat_of_accounts = [
                "Owner's Equity",
                'Retained Earnings'
            ];

            $revenue_chat_of_accounts = [
                'Revenue'
            ];

            $expenses_chat_of_accounts = [
                'Operating Expenses',
                'Other Expenses'
            ];

            $drawings_chat_of_accounts = [
                "Owner's Drawings/Withdrawals"
            ];
            

            // generate asset accounts 
            foreach ($assets_chat_of_accounts as $key => $asset_coa) {
                AccountController::first_or_create_coa(['title' => $asset_coa, 'type' => 'assets']);
            }

            // generate liability accounts 
            foreach ($liabilities_chat_of_accounts as $key => $liability_coa) {
                AccountController::first_or_create_coa(['title' => $liability_coa, 'type' => 'liabilities']);
            }

            // generate equity accounts 
            foreach ($equity_chat_of_accounts as $key => $equity_coa) {
                AccountController::first_or_create_coa(['title' => $equity_coa, 'type' => 'equity']);
            }

             // generate income accounts 
             foreach ($revenue_chat_of_accounts as $key => $income_coa) {
                AccountController::first_or_create_coa(['title' => $income_coa, 'type' => 'income']);
            }

             // generate income accounts 
             foreach ($expenses_chat_of_accounts as $key => $expenses_coa) {
                AccountController::first_or_create_coa(['title' => $expenses_coa, 'type' => 'expenses']);
            }

            // generate drawings accounts 
            foreach ($drawings_chat_of_accounts as $key => $drawings_coa) {
                AccountController::first_or_create_coa(['title' => $drawings_coa, 'type' => 'drawings']);
            }

            $this->generate_heads();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function generate_heads(){
        try {
            
            $heads = [
                'assets' => [
                    'Current Assets'=> [
                        'Cash',
                        'Petty Cash',
                        'Accounts Receivable',
                        'Inventory',
                        'Prepaid Expenses',
                        'Short-Term Investments',
                    ],
            
                    'Fixed Assets'=> [
                        'Property, Plant, and Equipment',
                        'Accumulated Depreciation',
                    ],
               
                    'Other Assets'=> [
                        'Long-Term Investments',
                        'Intangible Assets (Goodwill, Patents)',
                    ]
                ],
            'liabilities' => [
                'Current Liabilities'=> [
                    'Accounts Payable',
                    'Short-Term Loans',
                    'Accrued Expenses',
                    'Taxes Payable',
                    'Unearned Revenue'
                ],
                'Long-Term Liabilities'=> [
                    'Long-Term Loans',
                    'Mortgage Payable',
                ]
            ],
            'drawings' => [
                "Owner's Drawings/Withdrawals" => [
                    "Owner's Withdrawal",
                ],
            ],
            'equity' => [
                "Owner's Equity"=> [
                    "Owner's Capital",
                    "Opening Balance Equity"
                ],
                'Retained Earnings'=> [
                    'Retained Earnings'
                ]
            ],
            'income' => [
                'Revenue'=> [
                    'Sales Revenue',
                    'Service Revenue',
                    'Other Income',
                ]
            ],
            'expenses' => [
                'Operating Expenses'=> [
                    'Cost of Goods Sold (COGS)',
                    'Rent Expense',
                    'Salaries and Wages',
                    'Utilities',
                    'Advertising and Marketing',
                    'Office Supplies',
                    'Depreciation Expense'
                ],
                'Other Expenses'=> [
                    'Interest Expense',
                    'Taxes Expense',
                    'Miscellaneous Expenses',
                ]
            ]
        ];

        // Strictly Defined numbers
        $head_account_numbers = [
            "Cash" => "1000",
            "Petty Cash" => "1010",
            "Accounts Receivable" => "1020",
            "Inventory" => "1030",
            "Prepaid Expenses" => "1040",
            "Short-Term Investments" => "1050",
            "Property, Plant, and Equipment" => "1500",
            "Accumulated Depreciation" => "1510",
            "Long-Term Investments" => "1700",
            "Intangible Assets" => "1710",
            "Accounts Payable" => "2000",
            "Short-Term Loans" => "2010",
            "Accrued Expenses" => "2020",
            "Taxes Payable" => "2030",
            "Unearned Revenue" => "2040",
            "Long-Term Loans" => "2500",
            "Mortgage Payable" => "2510",
            "Owner's Capital" => "3000",
            "Opening Balance Equity" => "3500",
            "Owner's Withdrawal" => "3010",
            "Retained Earnings" => "3100",
            "Sales Revenue" => "4000",
            "Service Revenue" => "4010",
            "Other Income" => "4020",
            "Cost of Goods Sold (COGS)" => "5000",
            "Rent Expense" => "5010",
            "Salaries and Wages" => "5020",
            "Utilities" => "5030",
            "Advertising and Marketing" => "5040",
            "Office Supplies" => "5050",
            "Depreciation Expense" => "5060",
            "Interest Expense" => "6000",
            "Taxes Expense" => "6010",
            "Miscellaneous Expenses" => "6020"
        ];
        

        foreach ($heads as $key => $heads) {
            $type = $key;
            foreach ($heads as $coa_title => $titles) {
                $coa = Account::where([
                    'coa' => true, 
                    'store_id' => Auth::user()->store_id, 
                    'title' => trim($coa_title),
                    'pre_defined' => true,
                    'type' => trim($type)
                ])->first();
                
                if($coa){
                    foreach ($titles as  $title) {
                         Account::firstOrCreate([
                            'title' => $title,
                            'account_number' => $head_account_numbers[$title] ?? null,
                            'store_id' => Auth::user()->store_id, 
                            'parent_id' => $coa->id,
                            'pre_defined' => true,
                            'type' => $type,
                            'head_account' => true,
                            'description' => 'This is system generated account'
                        ],
                        [
                            'opening_balance' => 0,
                            'current_balance' => 0
                        ]);
                    }
                }
            } 
            # code...
        }
            

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function get_heads_by_coa(int $coa_id, int $store_id){
        try {

            if(!$coa_id || !$store_id){
                return [];
            }
            
            return Account::orderBy('title', 'ASC')->where(['coa' => false, 'store_id' => $store_id, 'parent_id' => $coa_id,
            'head_account' => true])
            ->get() ?? [];

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function trial_balance_report(Request $request){
        try {
            
            $pdf = $request->has("pdf");
            $data = [];
            $heads = Account::where('head_account',true)->with('parent')->orderBy('type','ASC')->orderBy('title', 'ASC')
            // ->with("children.transactions",'parent','transactions')
            ->filterByStore()->get();

            $account_types = [];
            // set account type array 
            foreach ($heads as $key => $head) {
                $head_credit_debit = $this->get_credit_debit_sum(["account_id" => $head->id]);
                $item = [];
                $item["account_id"] = $head->id;
                $item["parent_account"] = $head->parent->title ?? '';
                $item["title"] = $head->title;
                $item["type"] = $head->type;
                $item["parent"] = true;
                $item["sum"]["total_credit"] = $head_credit_debit[0]['total_credit'] ;
                $item["sum"]["total_debit"] = $head_credit_debit[0]['total_debit'] ;
                $item["total_credit"] = $head_credit_debit[0]['total_credit'];
                $item["total_debit"] = $head_credit_debit[0]['total_debit'];
                $item["children"] = [];

                
                $accounts = Account::where("head_account",false)->where('parent_id',$head->id)
                ->filterByStore()->get();

                if($accounts && count($accounts)){
                    foreach ($accounts as $key => $account) {
                        $account_credit_debit = $this->get_credit_debit_sum(["account_id" => $account->id]);
                        $sub_item = [];
                        $sub_item["title"] = $account->title;
                        $sub_item["account_id"] = $account->id;
                        $sub_item["type"] = $account->type;
                        $sub_item["sum"]["total_credit"] = $account_credit_debit[0]['total_credit'];
                        $sub_item["sum"]["total_debit"] = $account_credit_debit[0]['total_debit'];
                        $item["total_credit"] = $item["total_credit"] +  $account_credit_debit[0]['total_credit'];
                        $item["total_debit"] = $item["total_debit"] +  $account_credit_debit[0]['total_debit'];
                        array_push($item["children"],$sub_item); 
                    }
                }

                if($item["total_debit"] > 0 || $item["total_credit"] > 0){

                    array_push($data,$item);
                }
                
        
            }
            // return $data;
            
            $data = collect($data)->groupBy('parent_account');
            
            if($pdf){
                $data = [
                    "data" => $data->toArray(),
                    'report_title' => 'Financial Report'
                ];
                $pdf = Pdf::loadView('accounts.reports.pdf.trial-balance', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }
            
            return view('accounts.reports.trial-balance', compact('data'));

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    static function get_credit_debit_sum($where = []){
        try {
            // dd($where);
            $credit_debit = AccountTransaction::
            selectRaw('SUM(debit) AS total_debit')
            ->selectRaw('SUM(credit) AS total_credit')
            ->where('store_id', Auth::user()->store_id)
            ->where($where)
            ->get()->toArray();

            return $credit_debit;
            // dd($credit_debit);

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function general_ledger_report(Request $request){

        $range = [date('Y-m-d',time()),date('Y-m-d',time())]; 
        $pdf = $request->has("type") && $request->type === "pdf" ;
        
        if($request->has("from") && !empty($request->from)){
            $range[0] = $request->from;
            
        }

        if($request->has("to") && !empty($request->to)){
            $range[1] = $request->to;
        }
        

        $data = Account::filterByStore()
        ->whereHas('transactions', function ($query) use ($range) {
            // Filter the transactions based on the date range
            $query->whereBetween('transaction_date', $range);
        })
        ->orderBy("parent_id",'ASC')
        ->with("parent") // Ensures only accounts with transactions are fetched
        ->with(['transactions' => function ($query)use($range) {
            session()->put('general_ledger_from',$range[0]);
            session()->put('general_ledger_to',$range[1]);
            $query->orderBy('transaction_date', 'ASC')->whereBetween('transaction_date',$range); // Orders the transactions by transaction_date DESC
        }]);

        if($request->has("account_id") && !empty($request->account_id) && $request->account_id > 0){
            $data = $data->where("id",$request->account_id);
            session()->put('general_account_id',abs($request->account_id));

        }else{
            session()->put('general_account_id',null);

        }

        $data = $data->get();

        if($pdf){
            $data = ["data" => $data];
            $pdf = Pdf::loadView('accounts.reports.pdf.general-ledger', $data)->setPaper('a4', 'landscape');
            return $pdf->stream();
        }

        $accounts = Account::filterByStore()
        ->where("coa", false)->whereHas('transactions')->orderBy("title",'ASC')->get();

        return view("accounts.reports.general-ledger", compact('data','accounts'));
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


    static function get_head_account($where = []){
        try {

            return Account::where($where)->filterByStore()->first();
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    static function get_coa_account($where = []){
        try {
            return Account::where($where)->where('head_account',false)->where("pre_defined",true)
            ->where('coa',true)->filterByStore()->first();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
