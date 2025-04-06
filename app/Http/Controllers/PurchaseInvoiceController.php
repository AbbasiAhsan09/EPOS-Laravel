<?php

namespace App\Http\Controllers;

use App\Helpers\ConfigHelper;
use App\Http\Trait\InventoryTrait;
use App\Http\Trait\TransactionsTrait;
use App\Models\Account;
use App\Models\AccountTransaction;
use App\Models\AppFormFields;
use App\Models\AppFormFieldsData;
use App\Models\AppForms;
use App\Models\Configuration;
use App\Models\Inventory;
use App\Models\Parties;
use App\Models\Products;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetails;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $invoices = PurchaseInvoice::with('created_by_user','party','order','dynamicFeildsData')->byUser()->paginate(15);
        $dynamicFields = AppForms::where("name",'purchase_invoice')
        ->with("fields")->whereHas("fields", function($query){
            return $query->where('show_in_table', 1)->filterByStore();
        })->first();
        return view('purchase.invoices.p_inv',compact('invoices','dynamicFields'));
    }

    public function create_inv(int $id)
    {
        try {
            $config = Configuration::filterByStore()->first();
            
        $checkInv = PurchaseInvoice::where('po_id' , $id)->first();

        if($checkInv){
            Alert::toast('Invoice(s) Exist for The P.O!','info');
        }else{

           
            
        }

        $order = PurchaseOrder::where('id',$id)->with('details.items')->first();
        $vendors = Parties::filterByStore()->with("groups")->get()->groupBy(function ($vendor) {
            return optional($vendor->groups)->group_name;
        });

        $dynamicFields = AppForms::where("name",'purchase_invoice')
        ->with("fields")->whereHas("fields", function($query){
            return $query->where('show_in_table', 1)->filterByStore();
        })->first();

        $last_id = PurchaseInvoice::max("id"); 

        return view('purchase.invoices.p_create_inv',compact('order','vendors','config','dynamicFields','last_id'));
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
                // 'payment_method' => 'required',
                'item_id' => 'required',
                'uom' => 'required',
                'tax' => 'required',
                'rate' => 'required'
            ]);

            if($validate){
                
            DB::beginTransaction();
            $invoice = new PurchaseInvoice();
            $invoice->doc_num ='PI'.'/'. (PurchaseInvoice::max("id") ?? 0) + 1;
            $invoice->party_id = $request->party_id;
            $invoice->po_id = PurchaseOrder::where('doc_num',$request->q_num)->first()->id ?? null;
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

            // dd((($request->gross_total + $request->other_charges) - ($discount)));
            $invoice->others = $request->other_charges;
            $invoice->tax = 0;
            $invoice->shipping = 0;
            $invoice->net_amount = (($request->gross_total + $request->other_charges) - ($discount));
            $invoice->created_by = Auth::user()->id;
            $invoice->remarks = $request->remarks;
            
            if( $request->doc_date && !empty($request->doc_date) && Auth::check() && (Auth::user()->userroles->role_name == 'Admin' || Auth::user()->userroles->role_name == 'SuperAdmin')){
              $invoice->created_at =  strtotime($request->doc_date);
              $invoice->doc_date = $request->doc_date;
            }
            if($request->recieved){
                $invoice->recieved = $request->recieved;
            }
            if($request->due_date){
                $invoice->due_date = $request->due_date;
              }
            $invoice->save();

            // Dynamic Fields Storing
            if(isset($request->dynamicFields) && count($request->dynamicFields)){
                foreach ($request->dynamicFields as $key => $value) {
                    if(!empty($value)){
                    $form = AppForms::where("name",'purchase_invoice')->first();
                    foreach ($value as $key => $field_value) {
                    $form_field = AppFormFields::where('form_id',$form->id)->where('name',$key)->filterByStore()->first();
                        if($form_field){
                            AppFormFieldsData::create(['form_id' => $form->id, 'field_id' => $form_field->id, 
                            'value' => $field_value, 'related_to' => $invoice->id,
                            'store_id' => Auth::user()->store_id ?? null]);
                        }
                    }
                    }
                }
            }
         //Dynamic Fields Storing

            $this->createPurchaseTransactionHistory($invoice->id,$invoice->party_id,($invoice->recieved ?? 0),date('Y-m-d'),'paid');


            if($invoice && count($request->item_id)){
                
                    for ($i=0; $i < count($request->item_id) ; $i++) { 
                        $item = Products::where("id",$request->item_id[$i])->filterByStore()->first();
                        $detail = new PurchaseInvoiceDetails();
                        $detail->inv_id = $invoice->id;
                        $detail->item_id = $request->item_id[$i];
                        $detail->rate = $request->rate[$i];
                        $detail->mrp = isset($request->mrp[$i]) ? $request->mrp[$i] : $item->tp ?? 0 ;
                        $detail->qty = $request->qty[$i];
                        $detail->tax = $request->tax[$i];
                        $detail->bags = isset($request->bags[$i]) ? $request->bags[$i] : 0;
                        $detail->bag_size = isset($request->bag_size[$i]) ? $request->bag_size[$i] : 0;
                        $detail->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                        $detail->base_unit_value = (isset($request->uom[$i]) && $request->uom[$i] > 1 ? $request->uom[$i] : 1);
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
                                $inventory->wght_cost = 0;
                                $inventory->stock_qty = ($inventory->stock_qty + $newQty);
                            }

                            $inventory->save();

                          
                        }
                    }
                
            }

            if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                $inventory_head= AccountController::get_head_account(['account_number' => 1030]);
                

                if($invoice->party_id){
                    $party = Parties::find($invoice->party_id);
                    if($party){
                       $group_validation = PartiesController::is_customer_group($party->group_id);
                       $is_customer = $group_validation["is_customer"];
                       $party_account = Account::firstOrCreate(
                            [ 
                                'store_id' => Auth::user()->store_id, // and store_id,
                                'reference_type' => $is_customer ? "customer" : 'vendor',
                                'reference_id' => $party->id,
                            ],
                            [
                                'title' => $party->party_name,
                                'type' => $is_customer ? 'assets' : 'liabilities',
                                'description' => 'This account is created by system on creating Purchase Invoice '.$invoice->doc_num, // Added description key
                                'opening_balance' => 0,
                            ]
                        );

                        if($party_account){
                            AccountController::record_journal_entry(
                                [
                                    'account_id' => $party_account->id,
                                    'reference_type' => 'purchase_invoice',
                                    'reference_id' => $invoice->id,
                                    'credit' => $invoice->net_amount,
                                    'debit' => 0,
                                    'transaction_date' => $request->has('doc_date') ?  $request->doc_date : date('Y-m-d',time()),
                                    'note' => 'This transaction is made by '.Auth::user()->name.' for Purchase Invoice '. $invoice->doc_num .'',
                                    'source_account' => $inventory_head->id,
                                ]
                                );
                        }
                    }
                }
            }
            DB::commit();
            Alert::toast('PO Invoice Created!','success');
                return redirect("/purchase/invoice");
        }
            
        } catch (\Throwable $th) {
            DB::rollBack();
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
        $invoice = PurchaseInvoice::where('id',$id)->with('dynamicFeildsData')->filterByStore()->first();
            if($invoice){
                $vendors = Parties::filterByStore()->with("groups")->get()->groupBy(function ($vendor) {
                    return optional($vendor->groups)->group_name;
                });
                $config = Configuration::filterByStore()->first();
                    
                $dynamicFields = AppForms::where("name",'purchase_invoice')
                ->with("fields")->whereHas("fields", function($query){
                    return $query->where('show_in_table', 1)->filterByStore();
                })->first();

                    return view('purchase.invoices.p_edit_inv',compact('invoice','vendors','config','dynamicFields'));
            }
            Alert::toast('Invalid Request','error');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
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
                DB::beginTransaction();
                $invoice =  PurchaseInvoice::where('id',$id)->filterByStore()->first();
                if(!$invoice){
                    Alert::toast('Invalid Request','error');
                    return redirect()->back();
                }
                $old_amount = $invoice->recieved;
                $invoice->doc_num = date('d',time()).'/POI'.'/'. date('m/y',time()).'/'. $invoice->id;
                $invoice->party_id = $request->party_id;
                $invoice->po_id = PurchaseOrder::where('doc_num',$request->q_num)->first()->id ?? null;
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
                // dd($request->doc_date);
                $invoice->others = $request->other_charges;
                $invoice->tax = 0;
                $invoice->shipping = 0;
                $invoice->net_amount = (($request->gross_total + $request->other_charges) - ($discount));
                $invoice->created_by = Auth::user()->id;
                $invoice->remarks = $request->remarks;
                if( $request->doc_date && !empty($request->doc_date) && Auth::check() && (Auth::user()->userroles->role_name == 'Admin' || Auth::user()->userroles->role_name == 'SuperAdmin')){
                    $invoice->created_at =  strtotime($request->doc_date);
                    $invoice->doc_date = $request->doc_date;
                  }
                  if($request->recieved){
                      $invoice->recieved = $request->recieved;
                  }
                  if($request->due_date){
                    $invoice->due_date = $request->due_date;
                  }
                $invoice->save();

                   // Dynamic Fields Storing
                   if(isset($request->dynamicFields) && count($request->dynamicFields)){
                    foreach ($request->dynamicFields as $key => $value) {
                        if(!empty($value)){
                        $form = AppForms::where("name",'purchase_invoice')->first();
                        foreach ($value as $key => $field_value) {
                        $form_field = AppFormFields::where('form_id',$form->id)->where('name',$key)->filterByStore()->first();
                            if($form_field){
                               $appFormFieldData = AppFormFieldsData::where(['form_id' => $form->id, 'field_id' => $form_field->id, 
                                'related_to' => $invoice->id,
                                'store_id' => Auth::user()->store_id])->first();
                                if($appFormFieldData){
                                    $appFormFieldData->update(['value' => $field_value]);
                                }else{
                                    AppFormFieldsData::create(['form_id' => $form->id, 'field_id' => $form_field->id, 
                                    'related_to' => $invoice->id,
                                    'store_id' => Auth::user()->store_id,'value' => $field_value]);
                                }
                            }
                        }
                        }
                    }
                }
             //Dynamic Fields Storing
    
                if($invoice && count($request->item_id)){
                        $deleteItems = PurchaseInvoiceDetails::where('inv_id' , $invoice->id)->whereNotIn('item_id' , $request->item_id);
                        if(count($deleteItems->get())){
                            $transaction_description = (count($deleteItems->get()) ? 'Return Items in order'.$deleteItems->get()->pluck('item_details.name') : '');
                        }
                        $this->updatePurchaseTransaction($invoice->id,($invoice->party_id ?? 0),($old_amount ?? 0),($invoice->recieved ?? 0),isset($transaction_description) ? $transaction_description :'');
                      
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
                            $detail->mrp = isset($request->mrp[$i]) ? $request->mrp[$i] : $item->tp ?? 0 ;
                            $detail->qty = $request->qty[$i];
                            $detail->tax = $request->tax[$i];
                            $detail->bags = isset($request->bags[$i]) ? $request->bags[$i] : 0;
                            $detail->bag_size = isset($request->bag_size[$i]) ? $request->bag_size[$i] : 0;
                            $detail->base_unit_value = (isset($request->uom[$i]) && $request->uom[$i] > 1 ? $request->uom[$i] : 1);
                            $detail->is_base_unit = ((isset($request->uom[$i]) && $request->uom[$i] > 1) ? true : false);
                            $detail->total = ((($request->qty[$i] * $request->rate[$i]) / 100 )* $request->tax[$i]) + ($request->qty[$i] * $request->rate[$i]);
                            $detail->save();

                            $this->updateProductPrice($detail->item_id, $detail->mrp ,$detail->rate, $detail->is_base_unit);

                        }
                }


                if(ConfigHelper::getStoreConfig()["use_accounting_module"]){

                    // Reverse transactions
                    AccountController::reverse_transaction([
                        'reference_type' => 'purchase_invoice',
                        'reference_id' => $invoice->id,
                        'date' => (isset($request->doc_date) && $request->doc_date) ? $request->doc_date : null,
                        'description' => 'This transaction is reversed transaction because Purchase Invoice'.$invoice->doc_num.'   is update by '. Auth::user()->name.'',
                        'transaction_count' => 2,
                        'order_by' => 'DESC',
                        'order_column' => 'id'
                    ]);

                    $inventory_head= AccountController::get_head_account(['account_number' => 1030]);
                    
                    if($invoice->party_id){
                        $party = Parties::find($invoice->party_id);
                        $group_validation  = PartiesController::is_customer_group($party->group_id);
                        $is_customer = $group_validation["is_customer"];
                        if($party){

                           $party_account = Account::firstOrCreate(
                                [ 
                                    'store_id' => Auth::user()->store_id, // and store_id,
                                    'reference_type' => $is_customer ? 'customer' : 'vendor',
                                    'reference_id' => $party->id,
                                    'type' => $is_customer ? 'assets' : 'liabilities',
                                ],
                                [
                                    'title' => $party->party_name,
                                    'description' => 'This account is created by system on creating Purchase Invoice '.$invoice->doc_num, // Added description key
                                    'opening_balance' => 0,
                                ]
                            );
                            
    
                            if($party_account){
                       
                                AccountController::record_journal_entry([
                                    'store_id' => Auth::user()->store_id,
                                    'account_id' => $party_account->id,
                                    'reference_type' => 'purchase_invoice',
                                    'reference_id' => $invoice->id,
                                    'credit' => $invoice->net_amount,
                                    'debit' =>0, 
                                    'transaction_date' => $request->has('doc_date') ?  $request->doc_date : date('Y-m-d',time()),
                                    'note' => 'This transaction is made by '.Auth::user()->name.' for Purchase Invoice '. $invoice->doc_num .' (Updated)',
                                    'source_account' => $inventory_head->id,
                                ]);
    
                            }
                        }
                    }
                }

                DB::commit();

                Alert::toast('Invoice Updated!','info');
                return redirect("/purchase/invoice");
            }
        } catch (\Throwable $th) {
            DB::rollBack();
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
      try {
        $invoice = PurchaseInvoice::where('id',$id)->filterByStore()->first();
        DB::beginTransaction();
        if($invoice){
            $details = PurchaseInvoiceDetails::where('inv_id', $invoice->id);
            $this->deleteItemOnPurchaseInvoice($details->get());
            $details->delete();

            if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
            AccountController::reverse_transaction([
                'reference_type' => 'purchase_invoice',
                'reference_id' => $invoice->id,
                'order_by' => 'DESC',
                'order_column' => 'id',
                'transaction_count' => 2,
                'description' => 'This transaction is reversed because Purchase Invoice'.$invoice->doc_num.'   is deleted by '. Auth::user()->name.'',
            ]);
           }

            $invoice->delete();
            DB::commit();
            Alert::toast('Purchase Invoice Deleted!', 'info');
            return redirect()->back();
        }
        DB::rollBack();
        Alert::toast('Invalid Request','error');
        return redirect()->back();
      } catch (\Throwable $th) {
        DB::rollBack();
        throw $th;
      }
    }


    function print_invoice($id)  {
        try {
            $order = PurchaseInvoice::where('id',$id)->filterByStore()->first();
            // dd($invoice);
            if(!$order){
                Alert::toast("Invalid Request",'error');
                return redirect()->back();
            }
            return view('purchase.invoices.print-invoice', compact('order'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function get_invoice_details(Request $request) {
        try {
            
            $request->validate([
                'store_id' => 'required',
                'tran_no' => 'required'
            ]);

            $tran_no = $request->input('tran_no');
            $store_id = $request->input('store_id');

            $order = PurchaseInvoice::where("store_id", $store_id)->where("doc_num",$tran_no)
            ->with("details","party")
            ->first();

            if($order){
                return response()->json($order);
            }

            return false;

        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
