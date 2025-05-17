<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Voucher;
use App\Models\VoucherEntry;
use App\Models\VoucherType;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            
            $vouchers = Voucher::filterByStore()->with("account","account_from","user");

            $voucher_type = null;
            if($request->has('voucher_type_id')){
                $vouchers = $vouchers->where("voucher_type_id",$request->voucher_type_id);
                $voucher_type = VoucherType::where("id",$request->voucher_type_id)->filterByStore()->first();
            }

            if($request->has("search") && !empty($request->input("search"))){
                $vouchers = $vouchers->where("doc_no","like",'%'.$request->input("search").'%');
            }

            if(($request->has("from") && !empty($request->input("from"))) && ($request->has("to") && !empty($request->input("to")))){
                $vouchers = $vouchers->whereBetween('date',[$request->input("from"),$request->input("to")]);
            }

            if($request->has("account_id") && !empty($request->input("account_id"))){
                $vouchers = $vouchers->where("account_id",$request->input("account_id"));
            }

            if($request->has("from_account_id") && !empty($request->input("from_account_id"))){
                $vouchers = $vouchers->where("account_from_id",$request->input("from_account_id"));
            }

            if($request->has("type") && $request->input("type") == "pdf"){
                // dd($voucher_type);
                $data = [
                    'vouchers' => $vouchers->get(),
                    "from" => $request->from,
                    "to" => $request->to,
                    'report_title' => $voucher_type->name.' Report',
                ];
                $pdf = Pdf::loadView('vouchers.pdf', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }
            
            $vouchers= $vouchers->paginate(20);

             // from accounts lists
                $accounts = Account::orderBy('type','ASC')->orderBy('title', 'ASC');

                $account_types = explode(',',trim($voucher_type->account_types ?? ""));
                if($account_types && count(($account_types))){
                    $accounts = $accounts->whereIn("type",$account_types);
                }
                
                $account_reference_types = explode(',',trim($voucher_type->account_reference_types ?? ""));
                // if($account_reference_types && count(($account_reference_types))){
                //     $accounts = $accounts->whereIn("reference_type",$account_reference_types);
                // }

                $accounts = $accounts->byUser()->filterByStore()->get();

                $accounts = $accounts->groupBy("type");

                $all_accounts = Account::filterByStore()->get();
                $all_accounts = $all_accounts->groupBy("type");

                $all_voucher_types = VoucherType::filterByStore()->orderBy("name","asc")->get();


           return view("vouchers.list",compact("vouchers","voucher_type","accounts","all_accounts","all_voucher_types"));

        } catch (\Throwable $th) {
            throw $th;
        }
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

        // $account_types = explode(',',trim($voucher_type->account_types));
        // if($account_types && count(($account_types))){
        //     $from_accounts = $from_accounts->whereIn("type",$account_types);
        // }
        
        // $account_reference_types = explode(',',trim($voucher_type->account_reference_types));
        // if($account_reference_types && count(($account_reference_types))){
        //     $from_accounts = $from_accounts->whereIn("reference_type",$account_reference_types);
        // }

        $from_accounts = $from_accounts->byUser()->filterByStore()->get();

        $from_accounts = $from_accounts->groupBy("type");

        $voucher = null;
        if($voucher_id > 0){
            $voucher = Voucher::where("id",$voucher_id)->filterByStore()->with('entries','account','account_from','user')->first();
        }

        return view("vouchers.form",compact("accounts","voucher_type","from_accounts","voucher"));


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
            $request->validate([
                'date' => 'required',
                'voucher_type_id' => 'required',
                'account_id' => "required",
                'account_from_id' => "required",
            ]);

            if(!$request->has("amount") || (isset($request->amount) && count($request->amount) < 1)){
                toast("Please add at least 1 entry","error");
                return redirect()->back();
            }
            $voucher_type = VoucherType::where("id",$request->voucher_type_id)->filterByStore()->first();
            
            if(!$voucher_type){
                toast("Invalid voucher type",'error');
                return redirect()->back();
            }

            DB::beginTransaction();

            $prefix = $this->getFirstLetters($voucher_type->name); 
            $last_voucher = Voucher::orderBy('id', 'desc')->first();
            $last_voucher_id = $last_voucher ? $last_voucher->id : 1;
            $doc_no = $prefix.'/'.$last_voucher_id;
            $voucher_input_data= [
                'doc_no' => $doc_no,
                'store_id' => Auth::user()->store_id,
                'user_id' => Auth::user()->id,
                'date' => $request->date,
                'note' => trim($request->note),
                'voucher_type_id' => $voucher_type->id,
                'mode' => $request->mode ?? null,
                'reference_no' => $request->reference_no,
                'account_id' => $request->account_id,
                'account_from_id' => $request->account_from_id
            ];            
        
            $voucher = Voucher::create($voucher_input_data);
            $total = 0;
            for ($i=0; $i < count($request->amount) ; $i++) { 
                if(((int) $request->amount[$i]) < 0.1){
                   break;
                }
                $voucher_entry_input_data = [
                    'voucher_id' => $voucher->id,
                    'reference' => $request->reference[$i] ?? null,
                    'description' => $request->description[$i]  ?? null,
                    'amount' => $request->amount[$i] ?? 0,
                    'store_id' => $voucher->store_id,
                ];
                

                $entry = VoucherEntry::create($voucher_entry_input_data);
                $total+=($entry->amount ?? 0); 
            }

            $voucher->update(['total' => $total]);

            // $account = Account::where("id",$voucher->account_id)->filterByStore()->first();
            $account_from = Account::where("id",$voucher->account_from_id)->filterByStore()->first();
            $account = Account::where("id",$voucher->account_id)->filterByStore()->first();
            $is_account_debit_increase = true;
            
            // if(in_array($account_from->type,['assets','expenses'])){
            //     $is_account_debit_increase = true;
            //     if(in_array($account->type,['assets','expenses']) && $voucher_type->type === 'reciept'){
            //         $is_account_debit_increase = false;
            //     }else if(in_array($account->type,['assets','expenses'])){
            //         $is_account_debit_increase = false;
            //     }else{
            //         $is_account_debit_increase = true;
            //     }
            // }else{
            //     $is_account_debit_increase = false;
            // }

            if($voucher_type->is_bank_recieve){
                $is_account_debit_increase = true;
            }else{
                $is_account_debit_increase = false;
            }


            // dd($voucher->total, $is_account_debit_increase, $account, $account_from);
            // dd($account, $account_from, $is_account_debit_increase);

            AccountController::record_journal_entry([
                'account_id' => $voucher->account_id,
                'transaction_date' => $voucher->date,
                'note' => 'Transaction made on created '.$voucher_type->name.' '.$voucher->doc_no.' by '. Auth::user()->email,
                'credit' => !$voucher_type->is_bank_recieve ? $voucher->total : 0,
                'debit' =>  $voucher_type->is_bank_recieve ? $voucher->total : 0,
                'reference_type' => 'voucher',
                'reference_id' => $voucher->id,
                'source_account' => $account_from->id
            ]);

            DB::commit();

            toast($voucher_type->name." created",'success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function show(int $voucher_id)
    {
        try {
            $voucher = Voucher::filterByStore()->where("id",$voucher_id)->with('account','voucher_type','entries','account_from')->first();


            if(!$voucher){
                toast("Invalid Voucher ID",'error');
                return redirect()->back();
            }

            $data = [
                'voucher' => $voucher,
                'report_title' => $voucher->voucher_type->name . '  ('.$voucher->doc_no.')',
            ];
            $pdf = Pdf::loadView('vouchers.pdf.detail', $data)->setPaper('a4', 'portrait');

            return $pdf->stream();

        } catch (\Throwable $th) {
            throw $th;
        }
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
    public function update(int $voucher_id, Request $request)
    {
        try {
            // dd($request->all());
            $request->validate([
                'date' => 'required',
                'voucher_type_id' => 'required',
                'account_id' => "required",
                'account_from_id' => "required",
            ]);

            if(!$request->has("amount") || (isset($request->amount) && count($request->amount) < 1)){
                toast("Please add at least 1 entry","error");
                return redirect()->back();
            }
            $voucher_type = VoucherType::where("id",$request->voucher_type_id)->filterByStore()->first();
            
            if(!$voucher_type){
                toast("Invalid voucher type",'error');
                return redirect()->back();
            }

            $voucher = Voucher::where("id",$voucher_id)->filterByStore()->first();
            
            if(!$voucher){
                toast("Invalid Voucher ID",'error');
                return redirect()->back();
            }

            DB::beginTransaction();

           
            $voucher_input_data= [
                'date' => $request->date,
                'note' => trim($request->note),
                'voucher_type_id' => $voucher_type->id,
                'mode' => $request->mode ?? null,
                'reference_no' => $request->reference_no,
                'account_id' => $request->account_id,
                'account_from_id' => $request->account_from_id
            ];            
        
            $voucher->update($voucher_input_data);
            $total = 0;

            // cleanup all old entries
            VoucherEntry::where("voucher_id",$voucher->id)->delete();

            for ($i=0; $i < count($request->amount) ; $i++) { 
                if(((int) $request->amount[$i]) < 0.1){
                   break;
                }
                $voucher_entry_input_data = [
                    'voucher_id' => $voucher->id,
                    'reference' => $request->reference[$i] ?? null,
                    'description' => $request->description[$i]  ?? null,
                    'amount' => $request->amount[$i] ?? 0,
                    'store_id' => $voucher->store_id,
                ];
                

                $entry = VoucherEntry::create($voucher_entry_input_data);
                $total+=($entry->amount ?? 0); 
            }

            $voucher->update(['total' => $total]);

            // $account = Account::where("id",$voucher->account_id)->filterByStore()->first();
            $account_from = Account::where("id",$voucher->account_from_id)->filterByStore()->first();
            $account = Account::where("id",$voucher->account_id)->filterByStore()->first();
            // $is_account_debit_increase = true;
            
            // if(in_array($account_from->type,['assets','expenses'])){
            //     $is_account_debit_increase = true;
            //     if(in_array($account->type,['assets','expenses']) && $voucher_type->type === 'reciept'){
            //         $is_account_debit_increase = false;
            //     }else if(in_array($account->type,['assets','expenses'])){
            //         $is_account_debit_increase = false;
            //     }else{
            //         $is_account_debit_increase = true;
            //     }
            // }else{
            //     $is_account_debit_increase = false;
            // }


            AccountController::reverse_transaction([
                'reference_type' => 'voucher',
                'reference_id' => $voucher->id,
                'date' => null,
                'description' => 'Transaction reversed on update '.$voucher->doc_no.' by '. Auth::user()->email,
                'transaction_count' => 2,
                'order_by' => 'DESC',
                'order_column' => 'id',
            ]);


            AccountController::record_journal_entry([
                'account_id' => $voucher->account_id,
                'transaction_date' => $voucher->date,
                'note' => 'Transaction made on update'.$voucher_type->name.' '.$voucher->doc_no.' by '. Auth::user()->email,
                'credit' => !$voucher_type->is_bank_recieve ? $voucher->total : 0,
                'debit' =>  $voucher_type->is_bank_recieve ? $voucher->total : 0,
                'reference_type' => 'voucher',
                'reference_id' => $voucher->id,
                'source_account' => $account_from->id
            ]);

            DB::commit();

            toast($voucher_type->name." updated",'success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Voucher  $voucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $voucher_id)
    {
        try {
            
            $voucher = Voucher::where("id",$voucher_id)->filterByStore()->first();
            
            if(!$voucher){
                toast("Trying to delete invalid voucher",'error');
                return redirect()->back();
            }

            DB::beginTransaction();

            VoucherEntry::where("voucher_id",$voucher->id)->delete();
           
            AccountController::reverse_transaction([
                'reference_type' => 'voucher',
                'reference_id' => $voucher->id,
                'date' => null,
                'description' => 'Transaction reversed on delete '.$voucher->doc_no.' by '. Auth::user()->email,
                'transaction_count' => 2,
                'order_by' => 'DESC',
                'order_column' => 'id',
            ]);

            $voucher->delete();
            DB::commit();

            toast("Voucher deleted successfully",'success');

            return redirect()->back();

        } catch (\Throwable $th) {
            
            DB::rollBack();
            throw $th;
        }
    }


    public function generate_voucher_types() {
        try {
            
            
            $receipt_voucher_types = [
                [
                    'name' => 'Receipt Voucher',
                    'slug' => "receipt-voucher",
                    "account_id" => Account::where("account_number",1000)->filterByStore()->first()->id,
                    "description" => "System Generate Voucher Type for Receiving Payment",
                    'is_bank_recieve' => true
                ]
            ];

            $payment_voucher_types = [
                [
                    'name' => 'Payment Voucher',
                    'slug' => "payment-voucher",
                    "account_id" => Account::where("account_number",1000)->filterByStore()->first()->id,
                    "description" => "System Generate Voucher Type for Receiving Payment",
                    'is_bank_recieve' => false
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
