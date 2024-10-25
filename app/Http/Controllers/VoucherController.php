<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Voucher;
use App\Models\VoucherType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(int $voucher_type_id, int $voucher_id = null)
    {
       try {
        
        $voucher_type = VoucherType::where("id",$voucher_type_id)->filterByStore()->first();

        if(!$voucher_type){
            toast('Invalid voucher type','error');
            return redirect()->back();
        }

        $accounts =  Account::orderBy('type','ASC')->orderBy('title', 'ASC')
        ->byUser()->filterByStore()->get();
        $accounts = $accounts->groupBy("type");

        // from accounts lists
        $from_accounts = Account::orderBy('type','ASC')->orderBy('title', 'ASC');

        $account_types = explode(',',trim($voucher_type->account_types));
        if($account_types && count(($account_types))){
            $from_accounts = $from_accounts->whereIn("type",$account_types);
        }
        
        $account_reference_types = explode(',',trim($voucher_type->account_reference_types));
        if($account_reference_types && count(($account_reference_types))){
            $from_accounts = $from_accounts->whereIn("reference_type",$account_reference_types);
        }

        $from_accounts = $from_accounts->byUser()->filterByStore()->get();

        $from_accounts = $from_accounts->groupBy("type");

        return view("vouchers.form",compact("accounts","voucher_type","from_accounts"));


       } catch (\Throwable $th) {
        throw $th;
       }
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
            // dd($request->all());
            $request->validate([
                'date' => 'required',
                'voucher_type_id' => 'required',
                'total' => 'required',
                'account_id' => "required",
                'account_from_id' => "required",
            ]);

            if(!$request->has("amount") || (isset($request->amount) && count($request->amount) < 1)){
                toast("Please add at least 1 entry","error");
                return redirect()->back();
            }


            

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(Voucher $voucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function edit(Voucher $voucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Voucher $voucher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(Voucher $voucher)
    {
        //
    }


    public function generate_voucher_types() {
        try {
            
            
            $receipt_voucher_types = [
                [
                    'name' => 'Receipt Voucher',
                    'slug' => "receipt-voucher",
                    "account_id" => Account::where("account_number",1000)->filterByStore()->first()->id,
                    "description" => "System Generate Voucher Type for Receiving Payment",
                ]
            ];

            $payment_voucher_types = [
                [
                    'name' => 'Payment Voucher',
                    'slug' => "payment-voucher",
                    "account_id" => Account::where("account_number",1000)->filterByStore()->first()->id,
                    "description" => "System Generate Voucher Type for Receiving Payment",
                ]
            ];



            foreach ($payment_voucher_types as  $payment_voucher_type) {
                $payment_voucher_type["type"] = "payment";
                $payment_voucher_type["store_id"] = Auth::user()->store_id;
                VoucherType::firstOrCreate($payment_voucher_type,[
                    'account_types' => 'liabilities',
                    'account_reference_types' => 'vendor'
                ]);
            }

            foreach ($receipt_voucher_types as  $receipt_voucher_type) {
                $receipt_voucher_type["type"] = "receipt";
                $receipt_voucher_type["store_id"] = Auth::user()->store_id;
                VoucherType::firstOrCreate($receipt_voucher_type,[
                    'account_types' => 'assets',
                    'account_reference_types' => 'customer'
                ]);
            }
           

            return "Generated Voucher Types";

        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
