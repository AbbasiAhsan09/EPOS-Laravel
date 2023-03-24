<?php

namespace App\Http\Controllers;

use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\PurchaseQuotation;
use App\Models\PurchaseQuotationDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class PurchaseQuotationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $quotations = PurchaseQuotation::where('type' , 'PURCHASE')->paginate(10);
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
       try {
        $validate = $request->validate([
            'order_tyoe' => 'required',
            'party_id' => 'required | integer',
            'payment_method' => 'required',
            'item_id' => 'required',
            'uom' => 'required',
            'tax' => 'required',
            'rate' => 'required'
        ]);

        if($validate){
            // dd($request->all());
            $quotation = new PurchaseQuotation();
            $quotation->doc_num = date('d',time())."/".($request->order_tyoe == 'normal' ? 'PR' : 'SA').'/'.date('m/y',time()).'/'.PurchaseQuotation::latest()->first()->id + 1;
            $quotation->req_num = $request->req_num;
            $quotation->party_id = $request->party_id;
            $quotation->type = ($request->order_tyoe == 'normal' ? 'PURCHASE' : 'SALES');
            $quotation->remarks = $request->remarks;
            $quotation->other_charges = $request->other_charges;
            $quotation->created_by = Auth::user()->id;
            $quotation->gross_total = $request->gross_total;
            if($request->has('discount') && (substr($request->discount,0,1) == '%')){
                $quotation->discount_type = 'PERCENT';
                $discount = (($request->gross_total / 100 ) *  ((int)ltrim($request->discount,'%')));
                $quotation->discount = $discount;
                
            }else if($request->has('discount') && $request->discount > 0){
                $quotation->discount_type = 'FLAT';
                $quotation->discount = $request->discount;
                $discount =  $request->discount;
            }
            $quotation->save();
// dd($request->all());
            if($quotation){
                for ($i=0; $i < count($request->item_id) ; $i++) { 
                    $detail = new PurchaseQuotationDetails();
                    $detail->quotation_id = $quotation->id;
                    $detail->item_id = $request->item_id[$i];
                    $detail->rate = $request->rate[$i];
                    $detail->qty = $request->qty[$i];
                    $detail->tax = $request->tax[$i];
                    $detail->status = 1;
                    $detail->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                    $detail->total = ((($request->qty[$i] * $request->rate[$i]) / 100 )* $request->tax[$i]) + ($request->qty[$i] * $request->rate[$i]);
                    $detail->save();
                }
            }
            
            Alert::toast('Quotation Added!','success');
            return redirect()->back();

        }else{

        }
            dd($request->all());
       } catch (\Throwable $th) {
        throw $th;
       }
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
    public function edit(int $id)
    {
        $quotation = PurchaseQuotation::where('id',$id)->with('details.items')->first();
        $group_v = PartyGroups::where('group_name', 'LIKE' , "vendor%")->first();
        $group_c = PartyGroups::where('group_name', 'LIKE' , "custo%")->first();
        $vendors = Parties::where('group_id',$group_v->id ?? '')->get();
        $customers = Parties::where('group_id',$group_c->id ?? '')->get();
        return view("purchase.quotation.create_quotation", compact('vendors','customers','quotation'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseQuotation  $purchaseQuotation
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        try {
            $validate = $request->validate([
                'order_tyoe' => 'required',
                'party_id' => 'required | integer',
                'payment_method' => 'required',
                'item_id' => 'required',
                'uom' => 'required',
                'tax' => 'required',
                'rate' => 'required'
            ]);
    
            if($validate){
                // dd($request->all());
                $quotation =  PurchaseQuotation::find($id);
                $quotation->doc_num = date('d',time())."/".($request->order_tyoe == 'normal' ? 'PR' : 'SA').'/'.date('m/y',time()).'/'.$id;
                $quotation->req_num = $request->req_num;
                $quotation->party_id = $request->party_id;
                $quotation->type = ($request->order_tyoe == 'normal' ? 'PURCHASE' : 'SALES');
                $quotation->remarks = $request->remarks;
                $quotation->other_charges = $request->other_charges;
                $quotation->gross_total = $request->gross_total;
                if($request->has('discount') && (substr($request->discount,0,1) == '%')){
                    $quotation->discount_type = 'PERCENT';
                    $discount = (($request->gross_total / 100 ) *  ((int)ltrim($request->discount,'%')));
                    $quotation->discount = $discount;
                    
                }else if($request->has('discount') && $request->discount > 0){
                    $quotation->discount_type = 'FLAT';
                    $quotation->discount = $request->discount;
                    $discount =  $request->discount;
                }
                $quotation->save();
    // dd($request->all());
                if($quotation){
                    $items = PurchaseQuotationDetails::where('quotation_id', $quotation->id)->whereNotIn('item_id' , $request->item_id);
                    $items->delete();
                    for ($i=0; $i < count($request->item_id) ; $i++) { 
                        $detail = PurchaseQuotationDetails::where('quotation_id' , $quotation->id)->where('item_id' , $request->item_id[$i])->first();
                       if(!$detail){
                        $detail = new PurchaseQuotationDetails();
                        $detail->quotation_id = $quotation->id;
                        $detail->item_id = $request->item_id[$i];
                        $detail->rate = $request->rate[$i];
                        $detail->qty = $request->qty[$i];
                        $detail->tax = $request->tax[$i];
                        $detail->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                        $detail->total = ((($request->qty[$i] * $request->rate[$i]) / 100 )* $request->tax[$i]) + ($request->qty[$i] * $request->rate[$i]);
                        $detail->save();
                       }else{
                        $detail->quotation_id = $quotation->id;
                        $detail->item_id = $request->item_id[$i];
                        $detail->rate = $request->rate[$i];
                        $detail->qty = $request->qty[$i];
                        $detail->tax = $request->tax[$i];
                        $detail->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                        $detail->total = ((($request->qty[$i] * $request->rate[$i]) / 100 )* $request->tax[$i]) + ($request->qty[$i] * $request->rate[$i]);
                        $detail->save();
                       }
                    }
                }
                
                Alert::toast('Quotation Updated!','info');
                return redirect()->back();
    
            }else{
    
            }
        } catch (\Throwable $th) {
            throw $th;
        }
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
