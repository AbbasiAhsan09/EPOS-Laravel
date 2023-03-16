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
        return view('purchase.request.list');
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
        try {
            $validate = $request->validate([
                'type' => 'required',
                'item_id' => 'required'
            ]);


            if($validate)
            {
                $pr = new PurchaseRequest();
                $pr->requested_by = Auth::user()->id;
                $pr->type = $request->type;
                $pr->remarksa = $request->remarks;
                $pr->save();

                if($pr && count($request->item_id)){
                    for ($i=0; $i < count($request->item_id) ; $i++) { 
                        $item = new PurchaseRequestDetail();
                        $item->item_id =$request->item_id[$i];
                        $item->doc_num = $pr->id;
                        $item->request_id = $pr->id;
                        $item->qty = $request->qty[$i];
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
    public function edit(PurchaseRequest $purchaseRequest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseRequest  $purchaseRequest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        //
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
