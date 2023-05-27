<?php

namespace App\Http\Controllers;

use App\Http\Trait\InventoryTrait;
use App\Http\Trait\TransactionsTrait;
use App\Models\Inventory;
use App\Models\Parties;
use App\Models\Products;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetails;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class PurchaseInvoiceController extends Controller
{
    use InventoryTrait, TransactionsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $invoices = PurchaseInvoice::with('created_by_user','party','order')->paginate(15);
        return view('purchase.invoices.p_inv',compact('invoices'));
    }

    public function create_inv(int $id)
    {
        try {
        $checkInv = PurchaseInvoice::where('po_id' , $id)->first();

        if($checkInv){
            Alert::toast('Invoice(s) Exist for The P.O!','info');
        }else{

           
            
        }

        $order = PurchaseOrder::where('id',$id)->with('details.items')->first();
        $vendors = Parties::where('group_id' , 2)->get();
        return view('purchase.invoices.p_create_inv',compact('order','vendors'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
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
                
            $invoice = new PurchaseInvoice();
            $invoice->doc_num = date('d',time()).'/POI'.'/'. date('m/y',time()).'/'. (PurchaseInvoice::latest()->first()->id ?? 0) + 1;
            $invoice->party_id = $request->party_id;
            $invoice->po_id = PurchaseOrder::where('doc_num',$request->q_num)->first()->id;
            $invoice->total = $request->gross_total;
            if($request->has('discount') && (substr($request->discount,0,1) == '%')){
                $invoice->discount_type = 'PERCENT';
                $discount = (($request->gross_total / 100 ) *  ((int)ltrim($request->discount,'%')));
                $invoice->discount = $discount;
                
            }else if($request->has('discount') && $request->discount > 0){
                $invoice->discount_type = 'FLAT';
                $invoice->discount = $request->discount;
                $discount =  $request->discount;
            }

            $invoice->others = $request->other_charges;
            $invoice->tax = 0;
            $invoice->shipping = 0;
            $invoice->net_amount = (($request->gross_total + $request->other_charges) - ($discount));
            $invoice->created_by = Auth::user()->id;
            $invoice->remarks = $request->remarks;
            $invoice->doc_date = $request->doc_date;
            $invoice->recieved = $request->recieved;
            $invoice->due_date = $request->due_date;
            $invoice->save();

            $this->createPurchaseTransactionHistory($invoice->id,$invoice->party_id,$invoice->recieved,date('Y-m-d'),'paid');


            if($invoice && count($request->item_id)){
                
                    for ($i=0; $i < count($request->item_id) ; $i++) { 
                        $detail = new PurchaseInvoiceDetails();
                        $detail->inv_id = $invoice->id;
                        $detail->item_id = $request->item_id[$i];
                        $detail->rate = $request->rate[$i];
                        $detail->mrp = $request->mrp[$i];
                        $detail->qty = $request->qty[$i];
                        $detail->tax = $request->tax[$i];
                        $detail->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                        $detail->total = ((($request->qty[$i] * $request->rate[$i]) / 100 )* $request->tax[$i]) + ($request->qty[$i] * $request->rate[$i]);
                        $detail->save();

                        
                        // Inventory Manage
                        if($detail){
                            $this->updateProductPrice($detail->item_id, $detail->mrp ,$detail->rate, $detail->is_base_unit);
                            $totalCost = 0;
                            $totalNewCost = 0;
                            $item = Products::find($detail->item_id);
                            
                            $newQty = ($detail->is_base_unit ? ($detail->qty) : ((isset($item->uoms->base_unit_value ) ? $item->uoms->base_unit_value : 1 ) * $detail->qty));
                            $newRate = ($detail->is_base_unit ? ($detail->rate) : ($detail->rate / (isset($item->uoms->base_unit_value) ? $item->uoms->base_unit_value : 1 )));

                            $inventory = Inventory::where('item_id', $detail->item_id)->where('is_opnening_stock', 0)->first();

                            if(!$inventory){
                                $inventory = new Inventory();
                                $inventory->item_id = $detail->item_id;
                                $inventory->is_opnening_stock = 0;
                                $inventory->stock_qty = $newQty;
                                $inventory->wght_cost = $newRate;
                            }else{
                                $totalCost = $inventory->stock_qty * $inventory->wght_cost;
                                $totalNewCost = $newQty * $newRate;
                                $inventory->wght_cost = ($totalCost + $totalNewCost) / ($inventory->stock_qty + $newQty);
                                $inventory->stock_qty = ($inventory->stock_qty + $newQty);
                            }

                            $inventory->save();

                          
                        }
                    }
                
            }

            Alert::toast('PO Invoice Created!','success');
                return redirect("/purchase/invoice");
        }
            
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PurchaseInvoice  $purchaseInvoice
     * @return \Illuminate\Http\Response
     */
    public function show(PurchaseInvoice $purchaseInvoice)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PurchaseInvoice  $purchaseInvoice
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $invoice = PurchaseInvoice::find($id);
        $vendors = Parties::where('group_id' , 2)->get();
            
            return view('purchase.invoices.p_edit_inv',compact('invoice','vendors'));
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PurchaseInvoice  $purchaseInvoice
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
                $invoice =  PurchaseInvoice::find($id);
                $old_amount = $invoice->recieved;
                $invoice->doc_num = date('d',time()).'/POI'.'/'. date('m/y',time()).'/'. $invoice->id;
                $invoice->party_id = $request->party_id;
                $invoice->po_id = PurchaseOrder::where('doc_num',$request->q_num)->first()->id;
                $invoice->total = $request->gross_total;
                if($request->has('discount') && (substr($request->discount,0,1) == '%')){
                    $invoice->discount_type = 'PERCENT';
                    $discount = (($request->gross_total / 100 ) *  ((int)ltrim($request->discount,'%')));
                    $invoice->discount = $discount;
                    
                }else if($request->has('discount') && $request->discount > 0){
                    $invoice->discount_type = 'FLAT';
                    $invoice->discount = $request->discount;
                    $discount =  $request->discount;
                }
                $invoice->others = $request->other_charges;
                $invoice->tax = 0;
                $invoice->shipping = 0;
                $invoice->net_amount = (($request->gross_total + $request->other_charges) - ($discount));
                $invoice->created_by = Auth::user()->id;
                $invoice->remarks = $request->remarks;
                $invoice->doc_date = $request->doc_date;
                $invoice->recieved = $request->recieved;
                $invoice->due_date = $request->due_date;
                $invoice->save();
    
                if($invoice && count($request->item_id)){
                        $deleteItems = PurchaseInvoiceDetails::where('inv_id' , $invoice->id)->whereNotIn('item_id' , $request->item_id);
                        if(count($deleteItems->get())){
                            $transaction_description = (count($deleteItems->get()) ? 'Return Items in order'.$deleteItems->get()->pluck('item_details.name') : '');
                        }
                        $this->updatePurchaseTransaction($invoice->id,($invoice->party_id ?? 0),$old_amount,$invoice->recieved,isset($transaction_description) ? $transaction_description :'');
                      
                        foreach ($deleteItems->get() as $key => $deletedItem) {
                            // dd($deleteItems->get());
                            $inventory = Inventory::where('item_id',$deletedItem->item_id)->where('is_opnening_stock',0)->first();
                            // (['is_opnening_stock' => 0 ,'item_id' => $deletedItem->item_id]);
                            // dd($inventory);
                            $dltItemRef = Products::where('id',$deletedItem->item_id)->with('uoms')->first();
                            // dd($dltItemRef);
                            $dltQty = $deletedItem->is_base_unit ? $deletedItem->qty : ($deletedItem->qty * (isset($dltItemRef->uoms->base_unit_value) ? $dltItemRef->uoms->base_unit_value : 1)); 
                            $inventory->stock_qty = $inventory->stock_qty - $dltQty;
                            $inventory->save();
                        }
                        $deleteItems->delete();
                        
                        for ($i=0; $i < count($request->item_id) ; $i++) { 
                            
                            $item = Products::where('id',$request->item_id[$i])->with('uoms')->first();
                            $reqQty = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? $request->qty[$i] : ($request->qty[$i] * ($item->uoms->base_unit_value ?? 1)));
                            $detail =  PurchaseInvoiceDetails::where('inv_id' , $invoice->id)->where('item_id' , $request->item_id[$i])->first();
                            
                            if(!$detail){
                            
                            $detail = new PurchaseInvoiceDetails();
                            $inventory = Inventory::FirstOrCreate(['is_opnening_stock' => 0 ,'item_id' => $request->item_id[$i]]);
                            $inventory->stock_qty = $inventory->stock_qty + $reqQty;
                            $inventory->updated_at = time();                               
                            $inventory->save(); 

                            }else{
                                $oldQty = ($detail->is_base_unit ? $detail->qty : ($detail->qty *  (isset($item->uoms->base_unit_value) ? $item->uoms->base_unit_value : 1)));
                                $diffQty =  $reqQty - $oldQty;
                                $inventory =Inventory::FirstOrCreate(['is_opnening_stock' => 0 ,'item_id' => $detail->item_id]);
                                $inventory->stock_qty = $inventory->stock_qty + $diffQty;
                                $inventory->updated_at = time();                               
                                $inventory->save();
                            }

                            $detail->inv_id = $invoice->id;
                            $detail->item_id = $request->item_id[$i];
                            $detail->rate = $request->rate[$i];
                            $detail->qty = $request->qty[$i];
                            $detail->tax = $request->tax[$i];
                            $detail->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                            $detail->total = ((($request->qty[$i] * $request->rate[$i]) / 100 )* $request->tax[$i]) + ($request->qty[$i] * $request->rate[$i]);
                            $detail->save();
                        }
                }

                Alert::toast('Invoice Updated!','info');
                return redirect("/purchase/invoice");
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PurchaseInvoice  $purchaseInvoice
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        // dd('hi');
      try {
        $invoice = PurchaseInvoice::find($id);
        if($invoice){
            $details = PurchaseInvoiceDetails::where('inv_id', $invoice->id);
            $this->deleteItemOnPurchaseInvoice($details->get());
            $details->delete();
            $invoice->delete();
            Alert::toast('Purchase Invoice Deleted!', 'info');
            return redirect()->back();
        }
      } catch (\Throwable $th) {
        throw $th;
      }
    }
}
