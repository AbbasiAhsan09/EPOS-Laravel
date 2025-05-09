<?php

namespace App\Http\Controllers;

use App\Models\AppFormFields;
use App\Models\AppFormFieldsData;
use App\Models\AppForms;
use App\Models\Configuration;
use App\Models\Parties;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
          
            $orders = PurchaseOrder::with('invoices','dynamicFeildsData')->byUser()->orderBy('id','desc')->paginate(10);
            // dd($orders);
            $dynamicFields = AppForms::where("name",'purchase_order')
            ->with("fields")->whereHas("fields", function($query){
                return $query->where('show_in_table', 1)->filterByStore();
            })->first();

        return view('purchase.orders.orders_list',compact('orders','dynamicFields'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // dd('hi');
        $vendors = Parties::where('group_id' , 2)->byUser()->get();
        $config = Configuration::filterByStore()->first();
        $dynamicFields = AppForms::where("name",'purchase_order')
        ->with("fields")->whereHas("fields", function($query){
            $query->filterByStore();
        })->first();


        return view('purchase.orders.create_order',compact('vendors','config','dynamicFields'));
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
                $order->doc_num = date('d',time())."/PO"."/".date('m/y',time()).'/'.(PurchaseOrder::max("id") ?? 0)+ 1;
                $order->quotation_num = $request->q_num;
                $order->party_id = $request->party_id;
                $order->remarks = $request->remarks;
                $order->shipping_cost = $request->other_charges;
                $order->created_by = Auth::user()->id;
                $order->sub_total = $request->gross_total;
                if($request->has("order_date") && $request->order_date){
                    $order->order_date = $request->order_date;
                    $order->created_at = strtotime($request->order_date);
                }
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

                // Dynamic Fields Storing
                   if(isset($request->dynamicFields) && count($request->dynamicFields)){
                    foreach ($request->dynamicFields as $key => $value) {
                        if(!empty($value)){
                        $form = AppForms::where("name",'purchase_order')->first();
                        foreach ($value as $key => $field_value) {
                        $form_field = AppFormFields::where('form_id',$form->id)->where('name',$key)->filterByStore()->first();
                            if($form_field){
                                AppFormFieldsData::create(['form_id' => $form->id, 'field_id' => $form_field->id, 
                                'value' => $field_value, 'related_to' => $order->id,
                                'store_id' => Auth::user()->store_id ?? null]);
                            }
                        }
                        }
                    }
                }
             //Dynamic Fields Storing
   
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
        $config = Configuration::filterByStore()->first();
        $order = PurchaseOrder::filterByStore()->where('id',$id)->first();
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
        $order = PurchaseOrder::where('id',$id)->with('details.items','dynamicFeildsData')->filterByStore()->first();
        if(!$order){
            Alert::toast('Invalid Request','error');
            return redirect()->back();
        }
        $vendors = Parties::where('group_id' , 2)->byUser()->get();
        $dynamicFields = AppForms::where("name",'purchase_order')
        ->with("fields")->whereHas("fields", function($query){
            $query->filterByStore();
        })->first();

        return view('purchase.orders.create_order',compact('order','vendors','dynamicFields'));
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
                $order =  PurchaseOrder::where('id',$id)->filterByStore()->first();
                if(!$order){
                    Alert::toast('Invalid Request','error');
                    return redirect()->back();
                }
                $order->doc_num = date('d',time())."/PO"."/".date('m/y',time()).'/'.$order->id;
                $order->quotation_num = $request->q_num;
                $order->party_id = $request->party_id;
                $order->remarks = $request->remarks;
                $order->shipping_cost = $request->other_charges;
                $order->created_by = Auth::user()->id;
                $order->sub_total = $request->gross_total;
                if($request->has("order_date") && $request->order_date){
                    $order->order_date = $request->order_date;
                    $order->created_at = strtotime($request->order_date);
                }
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

                  // Dynamic Fields Storing
                  if(isset($request->dynamicFields) && count($request->dynamicFields)){
                    foreach ($request->dynamicFields as $key => $value) {
                        if(!empty($value)){
                        $form = AppForms::where("name",'purchase_order')->first();
                        foreach ($value as $key => $field_value) {
                        $form_field = AppFormFields::where('form_id',$form->id)->where('name',$key)->filterByStore()->first();
                            if($form_field){
                               $appFormFieldData = AppFormFieldsData::where(['form_id' => $form->id, 'field_id' => $form_field->id, 
                                'related_to' => $order->id,
                                'store_id' => Auth::user()->store_id])->first();
                                if($appFormFieldData){
                                    $appFormFieldData->update(['value' => $field_value]);
                                }else{
                                    AppFormFieldsData::create(['form_id' => $form->id, 'field_id' => $form_field->id, 
                                    'related_to' => $order->id,
                                    'store_id' => Auth::user()->store_id,'value' => $field_value]);
                                }
                            }
                        }
                        }
                    }
                }
             //Dynamic Fields Storing

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
    public function destroy($id)
    {
        try {
            
            $order = PurchaseOrder::where('id',$id)->filterByStore()->first();
            if($order){
                $order->delete();
                Alert::toast("Deleted Purchase Order!",'success');
                
            }else{
            Alert::toast("Invalid Request!",'error');
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function print_invoice($id)  {
        try {
            $config = Configuration::filterByStore()->first();
            $order = PurchaseOrder::where('id',$id)->filterByStore()->with(["party","details.items"])
            ->first();
            // dd($invoice);
            if(!$order){
                Alert::toast("Invalid Request",'error');
                return redirect()->back();
            }
            return view('purchase.invoices.print', compact('order', 'config'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
