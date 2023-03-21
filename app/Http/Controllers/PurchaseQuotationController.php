<?php

namespace App\Http\Controllers;

use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\PurchaseQuotation;
use Illuminate\Http\Request;

class PurchaseQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotations = PurchaseQuotation::paginate(10);
        return view('purchase.quotation.quotation_list',compact('quotations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $group_v = PartyGroups::where('group_name', 'LIKE' , "vendor%")->first();
        $group_c = PartyGroups::where('group_name', 'LIKE' , "custo%")->first();
        $vendors = Parties::where('group_id',$group_v->id ?? '')->get();
        $customers = Parties::where('group_id',$group_c->id ?? '')->get();
        return view("purchase.quotation.create_quotation", compact('vendors','customers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
       
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PurchaseQuotation  $purchaseQuotation
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseQuotation $purchaseQuotation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseQuotation  $purchaseQuotation
     * @return \Illuminate\Http\Response
     */
    public function edit(PurchaseQuotation $purchaseQuotation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseQuotation  $purchaseQuotation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PurchaseQuotation $purchaseQuotation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseQuotation  $purchaseQuotation
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseQuotation $purchaseQuotation)
    {
        //
    }
}
