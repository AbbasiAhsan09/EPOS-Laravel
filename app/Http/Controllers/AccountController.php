<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use App\Models\AccountTransaction;
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

        $accounts =  Account::orderBy('title', 'ASC')->byUser()->filterByStore()->get();

        return view('accounts.journal',compact('accounts'));
    }

    public function journal_post(Request $request)  {
        try {
            // dd($request);
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
                ];
                
                $created = AccountTransaction::create($item);
                if($created){
                    
                    $transaction = AccountTransaction::where("id",$created->id)->with('account')
                    ->first()->toArray();
                    
                    $account = $transaction["account"];
                    
                    if($account["reference_type"] === "vendor" && $transaction["debit"] > 0){
                        
                        $vendor_req =new Request([
                            'amount' => $transaction["debit"],
                            'date' => $transaction["transaction_date"]
                        ]);
                        $vendorLedgerController = new VendorLedgerController();
                        $vendorLedgerController->update($vendor_req, $account["reference_id"]);
                        dd($vendor_req);
                        dd($account,$transaction);
                    }
                    dump($transaction);
                    dump($account);
                    dd();

                }


            }

            toast('Transaction added successfully','success');
            return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
