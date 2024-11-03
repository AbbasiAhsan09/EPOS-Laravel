<?php

namespace App\Http\Controllers;

use App\Helpers\ConfigHelper;
use App\Imports\PartiesImporter;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\PurchaseInvoice;
use App\Models\Sales;
use Illuminate\Http\Request;
use Excel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Stmt\TryCatch;

class PartiesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

        $party_groups = PartyGroups::all();
        $parties = Parties::with("groups")
        ->when($request->has('party_group'), function($q) use ($request) {
            $q->where("group_id", $request->party_group);
        })
        ->when(
            function ($query) use ($request) {
                return $request->filled('party_name') || $request->filled('party_phone') || $request->filled('party_email');
            },
            function ($q) use ($request) {
                $q->where(function ($query) use ($request) {
                    if ($request->filled('party_name')) {
                        $query->where("party_name", 'like', '%' . $request->party_name . '%');
                    }
                    if ($request->filled('party_phone')) {
                        $query->orWhere("phone", 'like', '%' . $request->party_phone . '%');
                    }
                    if ($request->filled('party_email')) {
                        $query->orWhere("email", 'like', '%' . $request->party_email . '%');
                    }
                });
            }
        )
        ->orderBy('group_id', 'DESC')
        ->byUser()
        ->paginate(20)
        ->withQueryString();
    
        $group_id = $request->has('party_group') ? $request->party_group : false;
        
        return view('parties.index',compact('party_groups','parties','group_id'));
        
        } catch (\Throwable $th) {
            throw $th;
        }
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
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            // dd($request);
            $party = new Parties();
            $party->party_name = $request->party_name;
            $party->email = $request->email;
            $party->phone = $request->phone;
            $party->business_name = $request->business_name;
            if($request->has('country') && $request->country){
                $party->country = $request->country;
            }
            if($request->has('city') && $request->city){
                $party->city = $request->city;
            }
            if($request->has('state') && $request->state){
                $party->state = $request->state;
            }


            if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                if($request->has('opening_balance')){
                    $party->opening_balance = $request->opening_balance ?? 0;
                }
            }


            $party->website = $request->website;
            $party->group_id = $request->group_id;
            $party->location = $request->location;
            $party->save();

            
            
            
            if($party && ConfigHelper::getStoreConfig()["use_accounting_module"]){
                $this->create_party_account($party->id);
            }
            
            toast('Party Added!','success');
            return redirect()->back();


        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function create_party_account(int $party_id) {
        try {

            $party = Parties::find($party_id);

            if(!$party){
                return false;
            }

            $group_validation = $this->is_customer_group($party->group_id);
            $is_vendor = $group_validation['is_vendor'];
            $is_customer = $group_validation['is_customer'];


            $party_head = AccountController::get_head_account(["account_number" => ($is_customer ? 1020 : 2000)]);

            DB::beginTransaction();

            $account = Account::firstOrCreate(
                [
                    'type' => $is_customer ? 'assets' : ($is_vendor ? 'liabilities' : ''),
                    'reference_id' => $party->id,
                    'store_id' => Auth::user()->store_id,
                    'reference_type' => $is_customer ? 'customer' : ($is_vendor ? 'vendor' : ''),
                    'parent_id' => $party_head->id ?? null,
                ],
                [
                    
                    'title' => $party->party_name,
                    'opening_balance' => $party->opening_balance !== null ? $party->opening_balance : 0,
                ]
            );
             $opening_balance_head = AccountController::get_head_account(["account_number" => 3000]);

             $opening_balance_equity = Account::firstOrCreate(
                [
                    'pre_defined' => 1,
                    'type' => 'equity',
                    'title' => 'Opening Balance Equity',
                    'store_id' => Auth::user()->store_id,
                    'parent_id' => $opening_balance_head->id ?? null,
                ],
                [
                    'reference_type' => null,
                    'reference_id' => null,
                    'opening_balance' => 0,
                ]
            );

            if($account && $opening_balance_equity){
                    AccountController::record_journal_entry([
                        'account_id' => $account->id,
                        'note' => 'Initial opening account for '.($is_customer ? 'customer' : 'vendor').' ID: '.$account->reference_id,
                        'debit' => $is_customer ? $account->opening_balance : 0,
                        'credit' => $is_vendor ? $account->opening_balance : 0,
                        'reference_type' => 'opening_balance_'.$account->reference_type,
                        'reference_id' => $account->reference_id,
                        'source_account' => $opening_balance_equity->id
                    ]);
            }

            DB::commit();

            return true;
            
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }




    static function is_customer_group(int $group_id) {
        try {
            $party_group = PartyGroups::find($group_id);

            $validation = [
                'is_customer' => false,
                'is_vendor' => false
            ];

            if($party_group && ($party_group->group_name === 'Customer' || $party_group->group_name === 'customer' || $party_group->group_name === 'customers')){
               
                $validation["is_customer"] = true;
            } 

            if($party_group && ($party_group->group_name === 'Vendor' || $party_group->group_name === 'vendor' || $party_group->group_name === 'vendors')){
               
                $validation["is_vendor"] = true;
            } 

            return $validation;
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Parties  $parties
     * @return \Illuminate\Http\Response
     */
    public function show(Parties $parties)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Parties  $parties
     * @return \Illuminate\Http\Response
     */
    public function edit(Parties $parties)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Parties  $parties
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        try {
            $party =  Parties::where('id',$id)->byUser()->first();
            
            if(!$party){
                toast('Party does not exist', 'error');
                return redirect()->back();
            }
            DB::beginTransaction();

            // dd($party->toArray(),$request->all());
                $old_opening_balance = $party->opening_balance;
                $old_group_id = $party->group_id;
                $group_validation = $this->is_customer_group($party->group_id);
                $is_customer = $group_validation["is_customer"];
                $is_vendor = $group_validation["is_vendor"];

            if($party && $request->has('group_id') && ((int)$request->group_id !== (int)$party->group_id)){
               

                if($is_customer){
                    $sales = Sales::where("customer_id",$id)->count();
                    if($sales){
                        toast('You cannot update this party group because this party has ('.$sales.') active sale orders. Contact support to update this party','error');
                        return redirect()->back();
                    }
                }

                if($is_vendor){
                    $purchases = PurchaseInvoice::where("party_id",$id)->count();
                    
                    if($purchases){
                        toast('You cannot update this party group because this party has ('.$purchases.') active purchase invoices. Contact support to update this party','error');
                        return redirect()->back();
                    }
                }
            }
            
            
            $party->party_name = $request->party_name;
            $party->email = $request->email;
            $party->phone = $request->phone;
            $party->business_name = $request->business_name;
            if($request->has('country') && $request->country){
                $party->country = $request->country;
            }
            if($request->has('city') && $request->city){
                $party->city = $request->city;
            }
            if($request->has('state') && $request->state){
                $party->state = $request->state;
            }

            
            if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                if($request->has('opening_balance')){
                    $party->opening_balance = $request->opening_balance ?? 0;
                }
            }


            $party->website = $request->website;
            $party->group_id = $request->group_id;
            $party->location = $request->location;
            $party->save();


            $group_validation = $this->is_customer_group($party->group_id);
            $is_vendor = $group_validation['is_vendor'];
            $is_customer = $group_validation['is_customer'];




            if(ConfigHelper::getStoreConfig()["use_accounting_module"] && ($is_vendor || $is_customer)){

                
            $was_group_validation = $this->is_customer_group($old_group_id);
            $was_vendor = $was_group_validation['is_vendor'];
            $was_customer = $was_group_validation['is_customer'];

            $head_account = AccountController::get_head_account(["account_number" => ($is_customer ? 1020 : 2000)]);

                $account = Account::where(function ($query) use($was_customer) {
                    $query->where(['reference_type' => ($was_customer ? 'customer' : "vendor")]);
                })
                ->where('reference_id', $party->id)
                ->where('store_id', Auth::user()->store_id)
                ->first();

                if($account){

                    $have_any_transaction = AccountTransaction::
                    where("account_id" , $account->id)
                    ->where(function($qry){
                        $qry->where("debit", '>', 0)
                        ->orWhere("credit", '>', 0);
                    })
                    ->first();
                    
                    if($have_any_transaction && ($was_customer !== $is_customer)){
                        DB::rollBack();
                        toast('You cannot updated this party group type because this party has active transactions','error');
                        return redirect()->back();
                    }

                // Reverse transactions
                AccountController::reverse_transaction([
                    'reference_type' => 'opening_balance_'.$account->reference_type, 
                    'reference_id' => $party->id,
                    'description' => 'This transaction has been reversed because this party was updated  on '. date('Y-m-d',time()) .' by '. Auth::user()->name,
                    'transaction_count' => 2,
                    'order_by' => 'DESC',
                    'order_column' => 'id'
                ]);

                    $account->update([
                    'title' => $party->party_name,
                    'opening_balance' => $party->opening_balance !== null ? $party->opening_balance : 0, 
                    'reference_type' => $is_customer ? 'customer' : ($is_vendor ? 'vendor' : ''),
                    'type' => $is_customer ? 'assets' : 'liabilities',
                    'parent_id' => $head_account->id,
                ]);
                }else{
                    // Create new account for income or liabilities category
                   $account = Account::firstOrCreate(
                        [
                            'reference_type' => $is_customer ? 'customer' : ($is_vendor ? 'vendor' : ''),
                            'reference_id' => $party->id,
                            'store_id' => Auth::user()->store_id
                        ],
                        [
                            'title' => $party->party_name,
                            'type' => $is_customer ? 'assets' : 'liabilities',
                            'opening_balance' => $party->opening_balance !== null ? $party->opening_balance : 0,
                        ]
                    );
                }

                $this->create_party_account($party->id);

       

            }

            DB::commit();
            
            toast('Party Updated!','info');
            return redirect()->back();


        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Parties  $parties
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {
            $party = Parties::where("id",$id)->filterByStore()->first();

            if(!$party){
                toast('No party found','error');
                return redirect()->back();
            }

            $group_validation = $this->is_customer_group($party->group_id);
            $is_customer = $group_validation["is_customer"];
            $is_vendor = $group_validation["is_vendor"];

            if($is_customer){
                $sales = Sales::where("customer_id",$id)->count();
                if($sales){
                    toast('You cannot update this party group because this party has ('.$sales.') active sale orders. Contact support to update this party','error');
                    return redirect()->back();
                }
            }

            if($is_vendor){
                $purchases = PurchaseInvoice::where("party_id",$id)->count();
                
                if($purchases){
                    toast('You cannot update this party group because this party has ('.$purchases.') active purchase invoices. Contact support to update this party','error');
                    return redirect()->back();
                }
            }

            DB::beginTransaction();

            if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                $account = Account::where(function($query){
                    $query->where("reference_type","vendor")->orWhere("reference_type","customer");
                })
                ->where("reference_id",$party->id)
                ->filterByStore()->first();

                if($account){
                    $transaction = AccountTransaction::select('account_id')
                ->with("account")
                ->selectRaw('SUM(debit) AS total_debit')
                ->selectRaw('SUM(credit) AS total_credit')
                ->where('store_id', Auth::user()->store_id)
                ->where("account_id",$account->id)
                ->groupBy('account_id')
                ->first();

                if($transaction){
                    if((abs($transaction->total_debit) !== 0) ||( abs($transaction->total_credit) !== 0)){
                        toast('This party has account with credit' . ($transaction->total_credit) . ' and debit' . ($transaction->total_debit) . ' You cannot delete this.','error');
                        return redirect()->back();
                    }else{
                        AccountTransaction::where("account_id",$account->id)->delete();
                        $account->delete();
                    }
                }
                }

            }

            $party->delete();

            DB::commit();

            toast('Party deleted successfully','success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }

    function importCSV(Request $request)  {
        try {
            // dd($request);
             Excel::import(new PartiesImporter, $request->file("file"));
            return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    function get_party_balance(int $party_id) {
        try {
            
            $party = Parties::find($party_id);

            if(!$party){
                return 0;
            }

            $group_validation = $this->is_customer_group($party->group_id);
            $is_customer = $group_validation["is_customer"];
            $is_vendor = $group_validation["is_vendor"];

            $opening_balance = $party->opening_balance ?? 0;
            $balance = 0;
            if($is_customer){
                $c_balance = Sales::where("customer_id",$party->id)
                ->selectRaw("(SUM(net_total) - SUM(recieved)) as balance ")
                ->get();
                dd($c_balance);
            }


        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
