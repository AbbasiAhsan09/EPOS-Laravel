<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Labour;
use App\Models\LabourWorkHistory;
use App\Models\LabourWorkHistoryItems;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LabourWorkHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        session()->forget('labour_from');
        session()->forget('labour_to');
        session()->forget('labour_id');
        session()->forget('labour_status');

        $items = LabourWorkHistory::filterByStore()->with("labour")
        ->orderBy("end_date","ASC")->orderBy("end_date","DESC")->orderBy("start_date","DESC");

        if($request->has("from") && $request->has("to") && $request->input("from") && $request->input("to")){
            $range = [$request->from, $request->to];
                session()->put("labour_from",$range[0]);
                session()->put("labour_to",$range[1]);

            if($request->has("status") && $request->input("status")){
                session()->put("labour_status",$request->status);
                if($request->status == "open"){
                    $items = $items->whereBetween("start_date", $range)->where("end_date", null);
                }

                if($request->status == "close"){
                    $items = $items->whereBetween("end_date", $range)->whereNot("end_date", null);
                }
            }else{
                $items = $items->whereBetween("start_date", $range);
            }
        }

        if($request->has("labour_id") && $request->input("labour_id") ){
            session()->put("labour_id",$request->labour_id);
            $items = $items->where("labour_id", $request->input("labour_id"));
        }


        if($request->has("type") && $request->input("type") == 'pdf'){
            $data = ['items' => $items->get()];
            $pdf = Pdf::loadView('labour.work-history.report.pdf-report', $data)->setPaper('a4', 'portrait');
            return $pdf->stream();
        }

        $items = $items->paginate(20);

        $labours = Labour::filterByStore()->orderBy("name","ASC")->get();

        return view("labour.work-history.index",compact('items','labours'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $labours = Labour::filterByStore()->orderBy('name','ASC')->get();
        $history  = null;
        return view("labour.work-history.form",compact('labours','history')); 
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
            'labour_id' => "required",
            "start_date" => "required",
        ]);
        $prefix = "LB";
        $labour_work_history_data = [];
        $labour_work_history_data["labour_id"] = $request->labour_id;
        $labour_work_history_data["start_date"] = $request->start_date;
        $labour_work_history_data["doc_no"] = $prefix . '/' . (isset(LabourWorkHistory::latest()->first()->id) ? (LabourWorkHistory::max("id") + 1) : 1);
        $labour_work_history_data["is_ended"] = $request->has("is_ended") && $request->is_ended ? $request->is_ended: false;
        $labour_work_history_data["is_paid"] = $request->has("is_paid") && $request->is_paid ? $request->is_paid: false;
        $labour_work_history_data["end_date"] = $request->has("end_date") && $request->end_date ? $request->end_date: null;
        $labour_work_history_data["paid_date"] = $request->has("paid_date") && $request->paid_date ? $request->paid_date: null;
        $labour_work_history_data["other_charges"] = $request->other_charges ?? 0;
        $labour_work_history_data["bonus"] = $request->bonus ?? 0;
        $labour_work_history_data["total"] = $request->total ?? 0;
        $labour_work_history_data["net_total"] = $request->net_total ?? 0;
        $labour_work_history_data["store_id"] = Auth::user()->store_id;
        $labour_work_history_data["notes"] = $request->notes ?? null;


        DB::beginTransaction();
        if(!$request->has("rate") || !count($request->rate)){
            toast("List items cannot be empty",'error');
            return redirect()->back();
        }
        
        $history = LabourWorkHistory::create($labour_work_history_data);
        
        if($history){
            $total = 0;
            for ($i=0; $i < count($request->rate); $i++) { 
                $item_data = ["labour_work_history_id" => $history->id];
                $item_data["date"] = $request->date[$i];
                $item_data["description"] = $request->description[$i];
                $item_data["rate"] = $request->rate[$i];
                $item_data["qty"] = $request->qty[$i];
                $rate = $request->rate[$i];
                $qty = $request->qty[$i];
                $item_data["total"] = $rate * $qty;
                $total += $rate * $qty;
                LabourWorkHistoryItems::create($item_data);
            }
            $net_total = $total + $history->other_charges + $history->bonus;
            $history->update(['total' => $total, "net_total" => $net_total]);

            $payable_head = Account::filterByStore()->where('account_number',2000)->first();
            $labour_account = Account::filterByStore()
            ->where([
                "reference_id" => $history->labour_id,
                "reference_type" => 'labour'
            ])->first();

            if( $labour_account){    
                if($payable_head && !$labour_work_history_data["is_paid"]){
                    AccountController::record_journal_entry([
                        'account_id'  => $labour_account->id,
                        'transaction_date' => !empty($labour_work_history_data["paid_date"]) ? $labour_work_history_data["paid_date"] : date("Y-m-d",time()),
                        'note' => 'Labour bill #' . $history->doc_no . ' Account : ' . $labour_account->title,
                        'credit' => 0,
                        'debit' => $history->net_total,
                        'reference_type' => 'labour_bill',
                        'reference_id' => $history->id,
                        'source_account' => $payable_head->id,
                    ]);
                }
                if($labour_work_history_data["is_paid"]){
                    $cash_account = Account::filterByStore()->where('account_number',1000)->first();
                    if($cash_account){
                        AccountController::record_journal_entry([
                            'account_id'  => $labour_account->id,
                            'transaction_date' => !empty($labour_work_history_data["paid_date"]) ? $labour_work_history_data["paid_date"] : date("Y-m-d",time()),
                            'note' => 'Labour bill #' . $history->doc_no,
                            'credit' => 0,
                            'debit' => $history->net_total,
                            'reference_type' => 'labour_bill',
                            'reference_id' => $history->id,
                            'source_account' => $cash_account->id,
                        ]);
                    }
                }

            }


        }

        toast("Labour bill created successfully",'success');
        DB::commit();

        return redirect()->back();

       } catch (\Throwable $th) {
        DB::rollBack();
        throw $th;
       }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LabourWorkHistory  $labourWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function show(LabourWorkHistory $labourWorkHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\LabourWorkHistory  $labourWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $labours = Labour::filterByStore()->orderBy('name','ASC')->get();
        $history  = LabourWorkHistory::where("id",$id)->with("items")->filterByStore()->first() ?? null;
        return view("labour.work-history.form",compact('labours','history')); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\LabourWorkHistory  $labourWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request )
    {
        try {
           
            $request->validate([
               'labour_id' => "required",
               "start_date" => "required",
           ]);
           $history = LabourWorkHistory::where("id",$id)->filterByStore()->first();
        //    dd($history);
           if(!$history){
            toast("Invalid Request",'error');
            return redirect()->back();
           }
           $labour_work_history_data = [];
           $labour_work_history_data["labour_id"] = $request->labour_id;
           $labour_work_history_data["start_date"] = $request->start_date;
           $labour_work_history_data["is_ended"] = $request->has("is_ended") && $request->is_ended ? $request->is_ended: false;
           $labour_work_history_data["is_paid"] = $request->has("is_paid") && $request->is_paid ? $request->is_paid: false;
           $labour_work_history_data["end_date"] = $request->has("end_date") && $request->end_date ? $request->end_date: null;
           $labour_work_history_data["paid_date"] = $request->has("paid_date") && $request->paid_date ? $request->paid_date: null;
           $labour_work_history_data["other_charges"] = $request->other_charges ?? 0;
           $labour_work_history_data["bonus"] = $request->bonus ?? 0;
           $labour_work_history_data["total"] = $request->total ?? 0;
           $labour_work_history_data["notes"] = $request->notes ?? null;
           $labour_work_history_data["net_total"] = $request->net_total ?? 0;
           $labour_work_history_data["store_id"] = Auth::user()->store_id;
   
           DB::beginTransaction();
           if(!$request->has("rate") || !count($request->rate)){
               toast("List items cannot be empty",'error');
               return redirect()->back();
           }
           
           $history->update($labour_work_history_data);
        //    dd($request->all());
           if($history){
            LabourWorkHistoryItems::where("labour_work_history_id",$history->id)->delete();
               $total = 0;

               for ($i=0; $i < count($request->rate); $i++) { 
                   $item_data = ["labour_work_history_id" => $history->id];
                   $item_data["date"] = $request->date[$i];
                   $item_data["description"] = $request->description[$i];
                   $item_data["rate"] = $request->rate[$i];
                   $item_data["qty"] = $request->qty[$i];
                   $rate = $request->rate[$i];
                   $qty = $request->qty[$i];
                   $item_data["total"] = $rate * $qty;
                   $total += $rate * $qty;
                   LabourWorkHistoryItems::create($item_data);
               }
               $net_total = $total + $history->other_charges + $history->bonus;
               $history->update(['total' => $total, "net_total" => $net_total]);
               
               $payable_head = Account::filterByStore()->where('account_number',2000)->first();
               $labour_account = Account::filterByStore()
               ->where([
                   "reference_id" => $history->labour_id,
                   "reference_type" => 'labour'
               ])->first();

               AccountController::reverse_transaction([
                'reference_type' => 'labour_bill',
                'reference_id' => $history->id,
                'date' => null,
                'description' => 'Revrsed transaction for labour bill# '.$history->doc_no,
                'transaction_count' => 2,
                'order_column' => 'id',
                'order_by' => 'DESC',
            ]);

            if( $labour_account){    
                if($payable_head && !$labour_work_history_data["is_paid"]){
                    AccountController::record_journal_entry([
                        'account_id'  => $labour_account->id,
                        'transaction_date' => !empty($labour_work_history_data["paid_date"]) ? $labour_work_history_data["paid_date"] : date("Y-m-d",time()),
                        'note' => 'Labour bill #' . $history->doc_no. ' Account : ' . $labour_account->title,
                        'credit' => $history->net_total,
                        'debit' => 0,
                        'reference_type' => 'labour_bill',
                        'reference_id' => $history->id,
                        'source_account' => $payable_head->id,
                    ]);
                }
                if($labour_work_history_data["is_paid"]){
                    $cash_account = Account::filterByStore()->where('account_number',1000)->first();
                    if($cash_account){
                        AccountController::record_journal_entry([
                            'account_id'  => $cash_account->id,
                            'transaction_date' => !empty($labour_work_history_data["paid_date"]) ? $labour_work_history_data["paid_date"] : date("Y-m-d",time()),
                            'note' => 'Labour bill #' . $history->doc_no,
                            'credit' => $history->net_total,
                            'debit' => 0,
                            'reference_type' => 'labour_bill',
                            'reference_id' => $history->id,
                            'source_account' => $labour_account->id,
                        ]);
                    }
                }

            }
           }
   
           toast("Labour bill updated successfully",'success');
           DB::commit();
   
           return redirect()->back();
   
          } catch (\Throwable $th) {
           DB::rollBack();
           throw $th;
          }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\LabourWorkHistory  $labourWorkHistory
     * @return \Illuminate\Http\Response
     */
    public function destroy(LabourWorkHistory $labourWorkHistory)
    {
        //
    }
}
