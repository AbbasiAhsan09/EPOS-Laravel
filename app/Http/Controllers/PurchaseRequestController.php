<?php

namespace App\Http\Controllers;

use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class PurchaseRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $requests = PurchaseRequest::paginate(15);
        return view('purchase.request.list',compact('requests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('purchase.request.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // for ($i=0; $i < count($request->item_id) ; $i++) { 
        //     dump((isset($request->uom[$i]) && $request->uom[$i] > 1));
        // }
        // dd($request);
        try {
            $validate = $request->validate([
                'order_tyoe' => 'required',
                'item_id' => 'required'
            ]);


            if($validate)
            {
                $pr = new PurchaseRequest();
                $pr->requested_by = Auth::user()->id;
                $pr->type = $request->order_tyoe;
                $pr->required_on = $request->required_on;
                $pr->remarks = $request->remarks;
                $pr->total_amount = $request->gross_total;
                $pr->save();

                if($pr && count($request->item_id)){
                    for ($i=0; $i < count($request->item_id) ; $i++) { 
                        $item = new PurchaseRequestDetail();
                        $item->item_id =$request->item_id[$i];
                        $item->taxes =$request->tax[$i];
                        $item->request_id = $pr->id;
                        $item->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                        $item->qty = $request->qty[$i];
                        $item->rate = $request->rate[$i];
                        $item->total = $request->qty[$i] * $request->rate[$i];
                        $item->save();
                    }
                }

                Alert::toast('Purchase Requisition Added!','success');
                return redirect('/purchase/request');
            }else{
                Alert::toast('Something Went Wrong','error');
                return redirect('/purchase/request');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseRequest $purchaseRequest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $purchaseRequest = PurchaseRequest::where('id',$id)->with('details.items')->first();
        if($purchaseRequest){
            // dump(($purchaseRequest));
            return view('purchase.request.create',compact('purchaseRequest'));
        }else{
            Alert::toast('Something went wrong!','error');
            return redirect()->route("request.index");
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        try {
            $validate = $request->validate([
                'order_tyoe' => 'required',
                'item_id' => 'required'
            ]);


            if($validate)
            {
                $pr =  PurchaseRequest::find($id);
                $pr->requested_by = Auth::user()->id;
                $pr->type = $request->order_tyoe;
                $pr->required_on = $request->required_on;
                $pr->remarks = $request->remarks;
                $pr->total_amount = $request->gross_total;
                $pr->save();

                PurchaseRequestDetail::where('request_id', $pr->id)->where('status',1)->delete();
                if($pr && count($request->item_id)){
                    for ($i=0; $i < count($request->item_id) ; $i++) { 
                        $item = new PurchaseRequestDetail();
                        $item->item_id =$request->item_id[$i];
                        $item->taxes =$request->tax[$i];
                        $item->request_id = $pr->id;
                        $item->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                        $item->qty = $request->qty[$i];
                        $item->rate = $request->rate[$i];
                        $item->total = $request->qty[$i] * $request->rate[$i];
                        $item->save();
                    }
                }

                Alert::toast('Purchase Requisition Updated!','info');
                return redirect('/purchase/request');
            }else{
                Alert::toast('Something Went Wrong','error');
                return redirect('/purchase/request');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseRequest $purchaseRequest)
    {
        //
    }


    public function main()
    {
        return view('purchase.main');
    }
}
