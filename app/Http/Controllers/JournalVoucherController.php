<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\JournalVoucher;
use App\Models\JournalVoucherDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JournalVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            
            $vouchers = JournalVoucher::filterByStore()->with('account');
            

            if($request->has("search") && !empty($request->input("search"))){
                $vouchers = $vouchers->where("doc_no","like",'%'.$request->input("search").'%');
            }

            if(($request->has("from") && !empty($request->input("from"))) && ($request->has("to") && !empty($request->input("to")))){
                $vouchers = $vouchers->whereBetween('date',[$request->input("from"),$request->input("to")]);
            }

            if($request->has("account_id") && !empty($request->input("account_id"))){
                $vouchers = $vouchers->where("account_id",$request->input("account_id"));
            }

            if($request->has("type") && $request->input("type") == "pdf"){
                
                $data = [
                    'vouchers' => $vouchers->orderBy("date",'ASC')->get(),
                    'report_title' => 'Journal Voucher Report',
                    'from' => isset($request->from) ? $request->from : null,
                    'to' => isset($request->to) ? $request->to : null,
                ];
                $pdf = Pdf::loadView('journal-vouchers.pdf.report', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }

            $vouchers = $vouchers->orderBy("date",'DESC')->paginate(20);

            $accounts =  Account::orderBy('type','ASC')->orderBy('title', 'ASC')
            ->byUser()->filterByStore()->get();
            $accounts = $accounts->groupBy("type");

            return view('journal-vouchers.list',compact('vouchers',"accounts"));

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(int $id = null)
    {
        try {
            

            $accounts =  Account::orderBy('type','ASC')->orderBy('title', 'ASC')
            ->byUser()->filterByStore()->get();
            $accounts = $accounts->groupBy("type");

            $voucher = null;

            if($id){
                $voucher = JournalVoucher::where("id",$id)->filterByStore()->with("entries")->first();
            }

            $last_id = JournalVoucher::max("id");

            return view("journal-vouchers.form",compact("accounts","voucher",'last_id'));

        } catch (\Throwable $th) {
            //throw $th;
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
                'jv_account_id' => 'required',
            ]);

            $jv_data = [
                'date' => $request->date,
                'account_id' => $request->jv_account_id,
                'reference_no' => $request->reference_no ?? null,
                'note' => $request->note ?? null,
                'doc_no' => 'JV/'. (JournalVoucher::max('id') ?? 0) + 1,
                'store_id' => Auth::user()->store_id,
                'user_id' => Auth::user()->id
            ];
            
            if($request->account_id && !count($request->account_id)){
                
                toast("Atleas 1 entry is required",'error');
                return redirect()->back();
            }

            DB::beginTransaction();

            $jv = JournalVoucher::create($jv_data);



            $total_credit = 0;
            $total_debit = 0;
            for ($i=0; $i < count($request->account_id); $i++) { 
                $data = [
                    'journal_voucher_id' => $jv->id,
                    'store_id' => $jv->store_id,
                    'reference_no' => $request->reference[$i] ?? null,
                    'account_id' => $request->account_id[$i],
                    'description' => $request->description[$i],
                    'credit' => isset($request->credit[$i]) && !empty($request->credit[$i])? $request->credit[$i] : 0,
                    'debit' => isset($request->debit[$i]) && !empty($request->debit[$i])? $request->debit[$i] : 0,
                    'mode' => $request->mode[$i] ?? 'cash',
                ];


                $entry = JournalVoucherDetail::create($data);
                $total_credit += ($entry->credit ?? 0);
                $total_debit += ($entry->debit ?? 0);

                AccountController::record_journal_entry([
                    'account_id' => $entry->account_id,
                    'transaction_date' => $jv->date,
                    'note' => $jv->doc_no .' | '.$entry->description,
                    'credit' => $entry->credit ?? 0,
                    'debit' => $entry->debit ?? 0,
                    'reference_type' => 'jv',
                    'reference_id' => $jv->id,
                    'source_account' => $jv->account_id,
                ]);
            }

            $jv->update([
                'total_credit' => $total_credit,
                'total_debit' => $total_debit 
            ]);

            Db::commit();

            toast('Journal Voucher Created','success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\JournalVoucher  $journalVoucher
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        try {
            
            $voucher = JournalVoucher::filterByStore()->where("id",$id)->with('account','entries.account')->first();


            if(!$voucher){
                toast("Invalid JV ID",'error');
                return redirect()->back();
            }

            $data = [
                'voucher' => $voucher,
                'report_title' => $voucher->doc_no.' - Report',
            ];
            $pdf = Pdf::loadView('journal-vouchers.pdf.detail', $data)->setPaper('a4', 'portrait');

            return $pdf->stream();


        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\JournalVoucher  $journalVoucher
     * @return \Illuminate\Http\Response
     */
    public function edit(JournalVoucher $journalVoucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JournalVoucher  $journalVoucher
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        try {

            $request->validate([
                'date' => 'required',
                'jv_account_id' => 'required',
            ]);

            $jv = JournalVoucher::where("id", $id)->filterByStore()->first();
            
            if(!$jv){
                toast("Invalid voucher id",'error');
                return redirect()->back();
            }

            $jv_data = [
                'date' => $request->date,
                'account_id' => $request->jv_account_id,
                'reference_no' => $request->reference_no ?? null,
                'note' => $request->note ?? null,
            ];
            
            if($request->account_id && !count($request->account_id)){
                
                toast("Atleas 1 entry is required",'error');
                return redirect()->back();
            }

            DB::beginTransaction();

            $jv->update($jv_data);
            
            $total_credit = 0;
            $total_debit = 0;

            // delete previous entries
            JournalVoucherDetail::where(['journal_voucher_id' => $jv->id])->filterByStore()->delete();

            // Reverse Previous Entries
            AccountController::reverse_transaction([
                'reference_type' => 'jv',
                'reference_id' => $jv->id,
                'date' => $jv->date,
                'description' => 'Due to update',
                'transaction_count' => 0,
                'order_by' => 'DESC',
                'order_column' => 'id'
            ]);

            for ($i=0; $i < count($request->account_id); $i++) { 
                $data = [
                    'journal_voucher_id' => $jv->id,
                    'store_id' => $jv->store_id,
                    'reference_no' => $request->reference[$i] ?? null,
                    'account_id' => $request->account_id[$i],
                    'description' => $request->description[$i],
                    'credit' => isset($request->credit[$i]) && !empty($request->credit[$i])? $request->credit[$i] : 0,
                    'debit' => isset($request->debit[$i]) && !empty($request->debit[$i])? $request->debit[$i] : 0,
                    'mode' => $request->mode[$i] ?? 'cash',
                ];


                $entry = JournalVoucherDetail::create($data);
                $total_credit += ($entry->credit ?? 0);
                $total_debit += ($entry->debit ?? 0);

                AccountController::record_journal_entry([
                    'account_id' => $entry->account_id,
                    'transaction_date' => $jv->date,
                    'note' => $jv->doc_no .' | '.$entry->description,
                    'credit' => $entry->credit ?? 0,
                    'debit' => $entry->debit ?? 0,
                    'reference_type' => 'jv',
                    'reference_id' => $jv->id,
                    'source_account' => $jv->account_id,
                ]);
            }

            $jv->update([
                'total_credit' => $total_credit,
                'total_debit' => $total_debit 
            ]);

            Db::commit();

            toast('Journal Voucher Updated','success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\JournalVoucher  $journalVoucher
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {

            $voucher = JournalVoucher::where("id",$id)->filterByStore()->first();

            if(!$voucher){

                toast("Invalid JV ID",'error');
                return redirect()->back();
            }


            DB::beginTransaction();

            JournalVoucherDetail::where("journal_voucher_id",$voucher->id)->delete();
            // Reverse Previous Entries
            AccountController::reverse_transaction([
                'reference_type' => 'jv',
                'reference_id' => $voucher->id,
                'date' => $voucher->date,
                'description' => 'Due to delete',
                'transaction_count' => 0,
                'order_by' => 'DESC',
                'order_column' => 'id'
            ]);

            $voucher->delete();
            DB::commit();
            toast("Journal Voucher Deleted",'success');
            return redirect()->back();

            
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
}
