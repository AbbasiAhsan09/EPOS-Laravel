<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Parties;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class PurchaseOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
            $orders = PurchaseOrder::with('invoices')->paginate(10);
        // dd($orders);
        return view('purchase.orders.orders_list',compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd('hi');
        $vendors = Parties::where('group_id' , 2)->get();
        $config = Configuration::first();

        return view('purchase.orders.create_order',compact('vendors','config'));
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
                $order = new PurchaseOrder();
                $order->doc_num = date('d',time())."/PO"."/".date('m/y',time()).'/'.(PurchaseOrder::latest()->first()->id ?? 0)+ 1;
                $order->quotation_num = $request->q_num;
                $order->party_id = $request->party_id;
                $order->remarks = $request->remarks;
                $order->shipping_cost = $request->other_charges;
                $order->created_by = Auth::user()->id;
                $order->sub_total = $request->gross_total;
                if($request->has('discount') && (substr($request->discount,0,1) == '%')){
                    $order->discount_type = 'PERCENT';
                    $discount = (($request->gross_total / 100 ) *  ((int)ltrim($request->discount,'%')));
                    $order->discount = $discount;
                    
                }else if($request->has('discount') && $request->discount > 0){
                    $order->discount_type = 'FLAT';
                    $order->discount = $request->discount;
                    $discount =  $request->discount;
                }
                $order->save();
    // dd($request->all());
                if($order){
                    for ($i=0; $i < count($request->item_id) ; $i++) { 
                        $detail = new PurchaseOrderDetails();
                        $detail->po_id = $order->id;
                        $detail->item_id = $request->item_id[$i];
                        $detail->rate = $request->rate[$i];
                        $detail->mrp = $request->mrp[$i];
                        $detail->qty = $request->qty[$i];
                        $detail->tax = $request->tax[$i];
                        $detail->status = 1;
                        $detail->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                        $detail->total = ((($request->qty[$i] * $request->rate[$i]) / 100 )* $request->tax[$i]) + ($request->qty[$i] * $request->rate[$i]);
                        $detail->save();
                    }
                }
                
                Alert::toast('PO Added!','success');
                return redirect("/purchase/order");
    
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
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $config = Configuration::first();
        $order = PurchaseOrder::find($id);
        return view('purchase.orders.create_order',compact('order','config'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $order = PurchaseOrder::where('id',$id)->with('details.items')->first();
        $vendors = Parties::where('group_id' , 2)->get();
        return view('purchase.orders.create_order',compact('order','vendors'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
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
                $order =  PurchaseOrder::find($id);
                $order->doc_num = date('d',time())."/PO"."/".date('m/y',time()).'/'.$order->id;
                $order->quotation_num = $request->q_num;
                $order->party_id = $request->party_id;
                $order->remarks = $request->remarks;
                $order->shipping_cost = $request->other_charges;
                $order->created_by = Auth::user()->id;
                $order->sub_total = $request->gross_total;
                if($request->has('discount') && (substr($request->discount,0,1) == '%')){
                    $order->discount_type = 'PERCENT';
                    $discount = (($request->gross_total / 100 ) *  ((int)ltrim($request->discount,'%')));
                    $order->discount = $discount;
                    
                }else if($request->has('discount') && $request->discount > 0){
                    $order->discount_type = 'FLAT';
                    $order->discount = $request->discount;
                    $discount =  $request->discount;
                }
                $order->save();
    // dd($request->all());
                if($order){
                    $deleteItems = PurchaseOrderDetails::where('po_id' , $order->id)->whereNotIn('item_id' , $request->item_id);
                    $deleteItems->delete();
                    for ($i=0; $i < count($request->item_id) ; $i++) { 
                        $detail = PurchaseOrderDetails::where('item_id' , $request->item_id[$i])->where('po_id' , $order->id)->first();
                        if(!$detail){
                            $detail = new PurchaseOrderDetails();
                        }
                        $detail->po_id = $order->id;
                        $detail->item_id = $request->item_id[$i];
                        $detail->mrp = $request->mrp[$i];
                        $detail->rate = $request->rate[$i];
                        $detail->qty = $request->qty[$i];
                        $detail->tax = $request->tax[$i];
                        $detail->status = 1;
                        $detail->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                        $detail->total = ((($request->qty[$i] * $request->rate[$i]) / 100 )* $request->tax[$i]) + ($request->qty[$i] * $request->rate[$i]);
                        $detail->save();
                    }
                }
                
                Alert::toast('PO Added!','success');
                return redirect("/purchase/order");
    
            }else{
    
            }
                dd($request->all());
           } catch (\Throwable $th) {
            throw $th;
           }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseOrder  $purchaseOrder
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        //
    }
}
