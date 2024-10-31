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
use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\PurchaseInvoiceDetails;
use App\Models\Sales;
use App\Models\SalesDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    use InventoryTrait ,TransactionsTrait;


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        DB::enableQueryLog();
        session()->forget('all');
            session()->forget('canceled');
            session()->forget('end_date');
            session()->forget('start_date');
        $items = Sales::orderBy('id', 'DESC')->byUser()->filterByStore()
        ->when($request->has('type')  && $request->type == 'all' , function($query){
            session()->forget('canceled' );
            session()->forget('sales');
            session()->forget('end_date');
            session()->forget('start_date');
            session()->put('all', true);
            return $query->withTrashed();
        })
        ->when($request->has('type')  && $request->type == 'canceled' , function($query){
            session()->forget('all');
            session()->forget('sales');
            session()->forget('end_date');
            session()->forget('start_date');
            session()->put('canceled', true);
            return $query->onlyTrashed();
        })
        ->when($request->has('type')  && $request->type == 'sales' , function($query){
            session()->forget('all');
            session()->forget('end_date');
            session()->forget('start_date');
            session()->forget('canceled');
            session()->put('sales', true);  
        })
        ->when($request->has('start_date') && $request->has('end_date') , function($query) use($request) {
            session()->put('start_date', $request->start_date);
            session()->put('end_date', $request->end_date);
            return $query->whereBetween('created_at', [$request->start_date , $request->end_date]);
        })
        ->when($request->has('status') && !empty($request->status) ,function ($query) use($request) {
            $query->where('order_process_status',$request->status);
        })
        ->paginate(15)->withQueryString();
       
        return view('sales.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->configInventoryChecks();
       
        try {
            
          
            $validate = $request->validate([
                'item_id' => 'required',
                'rate' => 'required',
                'qty' => 'required',
                'tax' => 'required',
                'order_tyoe' => 'required',
                // 'payment_method' => 'required',
                // 'recieved' => 'required',
                'gross_total' => 'required',
                'discount' => 'required',
                'other_charges' => 'required',
                'note' => 'string | nullable'
            ]);

            if(!$validate){
                toast("Please provide appropriate fields",'error');
                return redirect()->back();
            }

            if ($validate) {

                $config = Configuration::filterByStore()->first();
                $store_prefix = 'SA';
                $order  = new Sales();
                $order->tran_no = date('d') . '/' . $store_prefix . '/' . date('y') . '/' . date('m') . '/' . (isset(Sales::latest()->first()->id) ? (Sales::max("id") + 1) : 1);
                $order->customer_id = ($request->party_id ? $request->party_id : 0);
                $order->gross_total = $request->gross_total;
                $order->gp_no = $request->gp_no;
                $order->condition = $request->condition;
                $order->truck_no = $request->truck_no;
                $order->broker = $request->broker;
                $order->other_charges = $request->other_charges;
                $order->recieved = ($request->has('recieved') ? $request->recieved : 0); 
                $order->payment_method = $request->has('payment_method') ? $request->payment_method : 'cash';
                $order->note = $request->note;
                if($request->has('bill_date')){
                    $order->bill_date = $request->bill_date;
                    $order->created_at = strtotime($request->bill_date);
                }else{
                    $order->bill_date = date('Y-m-d',time());
                }
                $discount = 0;
                $order->password = Str::random(10);
                if($config->order_processing){
                    $order->order_process_status = 'pending';
                }else{
                    $order->order_process_status = 'delivered'; 
                }
                if ($request->has('discount') && (substr($request->discount, 0, 1) == '%')) {
                    $order->discount_type = 'PERCENT';
                    $order->discount = ((int)ltrim($request->discount, '%'));
                    $discount = (($request->gross_total / 100) *  ((int)ltrim($request->discount, '%')));
                } else if ($request->has('discount') && $request->discount > 0) {
                    $order->discount_type = 'FLAT';
                    $order->discount = $request->discount;
                    $discount =  $request->discount;
                }
                $order->user_id = Auth::user()->id;
                $order->net_total = $request->gross_total - $discount + ($request->has('other_charges') && $request->other_charges > 1 ? $request->other_charges : 0);
                $order->save();

                // Dynamic Fields Storing
                if(isset($request->dynamicFields) && count($request->dynamicFields)){
                    foreach ($request->dynamicFields as $key => $value) {
                        if(!empty($value)){
                        $form = AppForms::where("name",'sales')->first();
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
             

                $this->createOrderTransactionHistory($order->id,$order->customer_id,$order->recieved,date('Y-m-d'),'recieved');

                if ($order && count($request->item_id)) {
                    for ($i = 0; $i < count($request->item_id); $i++) {
                        $details = new SalesDetails();
                        $details->sale_id = $order->id;
                        $details->item_id = $request->item_id[$i];
                        $details->is_base_unit = ($request->uom[$i] > 1 ? true : false);
                        $details->tax = $request->tax[$i];
                        $details->qty = $request->qty[$i];
                        $details->bags = isset($request->bags[$i]) ? $request->bags[$i] : null;
                        $details->bag_size = isset($request->bag_size[$i]) ? $request->bag_size[$i] : null;
                        $details->rate = $request->rate[$i];
                        if ($request->has('item_disc')) {
                            $details->total = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]) - ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->item_disc[$i]));
                        } else {
                            $details->total = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]));
                        }
                        $details->save();

                        if ($config->inventory_tracking) {
                           
                            if ($this->allowLowInventory) {
                                
                                $this->subtractInventoryWithOrder($details->item_id, $details->qty, $details->is_base_unit);
                            } else {
                                if ($this->checkAvaialableInventory($details->item_id, $details->is_base_unit) && $this->checkAvaialableInventory($details->item_id, $details->is_base_unit) >= $details->qty) {
                                                                       
                                    $this->subtractInventoryWithOrder($details->item_id, $details->qty, $details->is_base_unit);
                                } else {
                                    $details->delete();
                                }
                            }
                        }
                    }

                if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                    $revenue_coa = AccountController::get_coa_account(['title' => 'Revenue']);

                    $revenue_account = Account::firstOrCreate(
                        [
                            'title' => 'Sales Revenue', // Search by title
                            'pre_defined' => 1,      // and pre_defined
                            'store_id' => Auth::user()->store_id, // and store_id
                            'account_number' => 4000,
                            'parent_id' => $revenue_coa->id ?? null,
                            'head_account' => true
                        ],
                        [
                            'type' => 'income',
                            'description' => 'This account handles the Sales Revenue transactions', // Added description key
                            'opening_balance' => 0,
                        ]
                    );

                    if($request->has('order_tyoe') && $request->order_tyoe === 'pos'){
                        $current_asset_coa = AccountController::get_coa_account(['title' => 'Current Assets']);
                        $cash_account = Account::firstOrCreate(
                            [
                                'title' => 'Cash', // Search by title
                                'pre_defined' => 1,      // and pre_defined
                                'store_id' => Auth::user()->store_id, // and store_id
                                'account_number' => 1000,
                                'parent_id' => $current_asset_coa->id,
                                'head_account' => true// and store_id

                            ],
                            [
                                'type' => 'assets',
                                'description' => 'This account is created by system on cash sales', // Added description key
                                'opening_balance' => 0,
                            ]
                        );

                        if($revenue_account && $cash_account){

                            AccountController::record_journal_entry([
                                'store_id' => Auth::user()->store_id,
                                'account_id' => $revenue_account->id,
                                'reference_type' => 'sales_order',
                                'reference_id' => $order->id,
                                'credit' => $order->net_total,
                                'debit' => 0,
                                'transaction_date' => $request->has('bill_date') ? $request->bill_date : date('Y-m-d',time()),
                                'note' => 'This transaction is made by '.Auth::user()->name.' for order '. $order->tran_no .'',
                                'source_account' => $cash_account->id
                            
                            ]);
                         
                        }
                    }

                    if($request->has('order_tyoe') && $request->order_tyoe !== 'pos'){
                        $party = Parties::find($order->customer_id);
                        if($party){
                        $group_validation = PartiesController::is_customer_group($party->id);
                        $is_customer = $group_validation["is_customer"];
                        $is_vendor = $group_validation["is_vendor"];
                           $party_account = Account::firstOrCreate(
                                [ 
                                    'store_id' => Auth::user()->store_id, // and store_id,
                                    'reference_type' => $is_customer ? 'customer' : 'vendor',
                                    'reference_id' => $party->id,
                                ],
                                [
                                    'title' => $party->party_name,
                                    'type' => $is_customer ? 'assets' : 'liabilities',
                                    'description' => 'This account is created by system on creating sale order '.$order->tran_no, // Added description key
                                    'opening_balance' => 0,
                                ]
                            );

                            if($party_account){

                                AccountController::record_journal_entry([
                                    'store_id' => Auth::user()->store_id,
                                    'account_id' => $revenue_account->id,
                                    'reference_type' => 'sales_order',
                                    'reference_id' => $order->id,
                                    'credit' => $order->net_total,
                                    'debit' => 0,
                                    'transaction_date' => $request->has('bill_date') ? $request->bill_date : date('Y-m-d',time()),
                                    'note' => 'This transaction is made by '.Auth::user()->name.' for order '. $order->tran_no .'',
                                    'source_account' => $party_account->id
                                
                                ]);


                                // $debit = AccountTransaction::create([
                                //     'store_id' => Auth::user()->store_id,
                                //     'account_id' => $revenue_account->id,
                                //     'reference_type' => 'sales_order',
                                //     'reference_id' => $order->id,
                                //     'credit' => $order->net_total,
                                //     'debit' => 0,
                                //     'transaction_date' => $request->has('bill_date') ? $request->bill_date : date('Y-m-d',time()),
                                //     'note' => 'This transaction is made by '.Auth::user()->name.' for order '. $order->tran_no .'',
                                // ]);

                                // $credit = AccountTransaction::create([
                                //     'store_id' => Auth::user()->store_id,
                                //     'account_id' => $party_account->id,
                                //     'reference_type' => 'sales_order',
                                //     'reference_id' => $order->id,
                                //     'credit' => 0,
                                //     'debit' => $order->net_total,
                                //     'transaction_date' => $request->has('bill_date') ? $request->bill_date : date('Y-m-d',time()),
                                //     'note' => 'This transaction is made by '.Auth::user()->name.' for order '. $order->tran_no .'',
                                // ]);
                            }
                        }
                    }
                }
                    

                    toast('Order Created!', 'success');
                    
                    return redirect()->back()->with('openNewWindow',$request->has('print_invoice')  ? $order->id : false );
                } else {
                    return 'Un-authorized Action';
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function calculateCOGS($orderId, $itemIds, $quantities)
    {
        $totalCOGS = 0;

        for ($i = 0; $i < count($itemIds); $i++) {
            $itemId = $itemIds[$i];
            $quantitySold = $quantities[$i];
            
            // Get purchase records for the item from purchase_invoice_details (FIFO: oldest purchases first)
            $purchases = PurchaseInvoiceDetails::where('item_id', $itemId)
                ->with("invoice")
                ->orderBy('invoice.doc_date', 'asc')
                ->get();
            
            $remainingQuantity = $quantitySold;

            foreach ($purchases as $purchase) {
                if ($remainingQuantity == 0) {
                    break;
                }

                $purchaseQuantity = $purchase->qty;
                $purchaseRate = $purchase->rate;

                // Calculate how much we can use from this purchase record
                $quantityToUse = min($remainingQuantity, $purchaseQuantity);
                
                // Calculate the COGS for this quantity
                $cogsForThisBatch = $quantityToUse * $purchaseRate;
                $totalCOGS += $cogsForThisBatch;

                // Reduce the remaining quantity to be fulfilled
                $remainingQuantity -= $quantityToUse;

                // Optionally, update the purchase record to reduce the qty available in stock
                $purchase->qty -= $quantityToUse;
                $purchase->save();
            }

            // After matching, update the SalesDetails table with COGS for this item
            SalesDetails::where('sale_id', $orderId)
                ->where('item_id', $itemId)
                ->update(['cogs' => $totalCOGS]);
        }

        // Record the COGS in the accounting system (same as in the previous example)
        $this->recordCOGSAccountingEntry($orderId, $totalCOGS);
    }

    /**
     * Record COGS entry in the accounting journal
     */
    public function recordCOGSAccountingEntry($orderId, $totalCOGS)
    {
        // Get or create the COGS and Inventory accounts
        $cogsAccount = Account::firstOrCreate(
            [
                'title' => 'Cost of Goods Sold',
                'store_id' => Auth::user()->store_id,
                'account_number' => 5000,
            ],
            [
                'type' => 'expense',
                'description' => 'COGS for sales orders',
                'opening_balance' => 0,
            ]
        );

        $inventoryAccount = Account::firstOrCreate(
            [
                'title' => 'Inventory',
                'store_id' => Auth::user()->store_id,
                'account_number' => 1500,
            ],
            [
                'type' => 'assets',
                'description' => 'Tracks store’s inventory',
                'opening_balance' => 0,
            ]
        );

        // Debit COGS, credit Inventory
        AccountController::record_journal_entry([
            'store_id' => Auth::user()->store_id,
            'account_id' => $cogsAccount->id,
            'reference_type' => 'sales_order',
            'reference_id' => $orderId,
            'debit' => $totalCOGS,
            'credit' => 0,
            'transaction_date' => date('Y-m-d'),
            'note' => 'COGS for order #' . $orderId,
            'source_account' => $inventoryAccount->id,
        ]);

        // Credit Inventory account (reduce inventory)
        AccountController::record_journal_entry([
            'store_id' => Auth::user()->store_id,
            'account_id' => $inventoryAccount->id,
            'reference_type' => 'sales_order',
            'reference_id' => $orderId,
            'debit' => 0,
            'credit' => $totalCOGS,
            'transaction_date' => date('Y-m-d'),
            'note' => 'Inventory reduction for order #' . $orderId,
            'source_account' => $cogsAccount->id,
        ]);
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function show(Sales $sales)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id, Sales $sales)
    {
        try {

            DB::enableQueryLog();
            $config = Configuration::filterByStore()->first();
            $order = Sales::where('id', $id)->with('order_details.item_details','dynamicFeildsData')->filterByStore()->first();
            // dump($order);
            // dd(DB::getQueryLog());
            if ($order) {
                $group = PartyGroups::where('group_name', 'LIKE', 'Customer%')->first();
                
                if ($group) {
                    $customers = Parties::filterByStore()->with("groups")->get()->groupBy(function ($customer) {
                        return optional($customer->groups)->group_name;
                    });
                } else {
                    $customers = [];
                }

                $dynamicFields = AppForms::where("name",'sales')
                ->with("fields")->whereHas("fields", function($query){
                    $query->filterByStore();
                })->first();


                return view('sales.sale_orders.new_order', compact('order', 'customers','config','dynamicFields'));
            }
            Alert::toast('invalid_request','error');
            return redirect()->back()->withErrors('invalid_request', 'Ooops! Your Request is Invalid!');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function update(int  $id, Request $request)
    {
        try {
            $validate = $request->validate([
                'item_id' => 'required',
                'rate' => 'required',
                'qty' => 'required',
                'tax' => 'required',
                'order_tyoe' => 'required',
                // 'payment_method' => 'required',
                // 'recieved' => 'required',
                'gross_total' => 'required',
                'discount' => 'required',
                'other_charges' => 'required',
                'note' => 'string | nullable'

            ]);

            if ($validate) {
                DB::beginTransaction();
                $order  =  Sales::where('id',$id)->filterByStore()->first();
                $old_customer_id = $order->customer_id;
                
                if (!$order) {
                    Alert::toast('Invalid Request','error');
                    return redirect()->back()->withErrors('error', 'Invalid Request');
                }
                $old_amount = $order->recieved;
                // dd($old_amount,);
                $order->customer_id = ($request->party_id ? $request->party_id : 0);
                $order->gross_total = $request->gross_total;
                $order->other_charges = $request->other_charges;
                $order->recieved = ($request->has('recieved') ? $request->recieved : 0);
                $order->note = $request->note;
                if($request->has('bill_date')){
                    $order->bill_date = $request->bill_date;
                    $order->created_at = strtotime($request->bill_date);

                }else{
                    $order->bill_date = date('Y-m-d',time());
                }
                $order->payment_method = $request->has('payment_method') ? $request->payment_method : 'cash';
                $discount = 0;
                if ($request->has('discount') && (substr($request->discount, 0, 1) == '%')) {
                    $order->discount_type = 'PERCENT';
                    $order->discount = ((int)ltrim($request->discount, '%'));
                    $discount = (($request->gross_total / 100) *  ((int)ltrim($request->discount, '%')));
                } else if ($request->has('discount') && $request->discount > 0) {
                    $order->discount_type = 'FLAT';
                    $order->discount = $request->discount;
                    $discount =  $request->discount;
                }
                $order->user_id = Auth::user()->id;
                $order->net_total = $request->gross_total - $discount + ($request->has('other_charges') && $request->other_charges > 1 ? $request->other_charges : 0);
                $order->gp_no = $request->gp_no;
                $order->condition = $request->condition;
                $order->truck_no = $request->truck_no;
                $order->broker = $request->broker;
                $order->save();

                   // Dynamic Fields Storing
                   if(isset($request->dynamicFields) && count($request->dynamicFields)){
                    foreach ($request->dynamicFields as $key => $value) {
                        if(!empty($value)){
                        $form = AppForms::where("name",'sales')->first();
                        foreach ($value as $key => $field_value) {
                        
                        $form_field = AppFormFields::where('form_id',$form->id)->where('name',$key)->filterByStore()->first();
                        // dd($form_field);
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
                

                if ($order && count($request->item_id)) {
                    $deleteItems = SalesDetails::where('sale_id', $id)->whereNotIn('item_id', $request->item_id);
                    if(count($deleteItems->get())){
                        $transaction_description = (count($deleteItems->get()) ? 'Return Items in order'.$deleteItems->get()->pluck('item_details.name') : '');
                    }
                    $this->updateOrderTransaction($order->id,($order->customer_id ?? 0),$old_amount,$order->recieved,isset($transaction_description) ? $transaction_description :'');
                   if($this->allowInventoryCheck){
                    $this->deletedItemsOnOrderUpdate($deleteItems->get());
                   }
                    $deleteItems->delete();
                    for ($i = 0; $i < count($request->item_id); $i++) {
                        $details =  SalesDetails::where('sale_id', $id)->where('item_id', $request->item_id[$i])->first();
                        if (!$details) {
                            $details = new SalesDetails();
                        }
                        // Updating The Inventory
                       if($this->allowInventoryCheck){
                        $this->updateInventoryOnUpdateOrder(
                            $request->item_id[$i],
                            $details->qty,
                            $request->qty[$i],
                            ($request->uom[$i] > 1 ? true : false),
                            $details->is_base_unit
                        );
                       }

                        $details->sale_id = $order->id;
                        $details->item_id = $request->item_id[$i];
                        $details->bags = isset($request->bags[$i]) ? $request->bags[$i] : null;
                        $details->bag_size = isset($request->bag_size[$i]) ? $request->bag_size[$i] : null;
                        $details->is_base_unit = ($request->uom[$i] > 1 ? true : false);
                        $details->tax = $request->tax[$i];
                        $details->qty = $request->qty[$i];
                        $details->rate = $request->rate[$i];
                        if ($request->has('item_disc')) {
                            $details->total = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]) - ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->item_disc[$i]));
                        } else {
                            $details->total = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]));
                        }
                        $details->save();

                    }

                    if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                        $revenue_coa = AccountController::get_coa_account(['title' => 'Revenue']);

                        $revenue_account = Account::firstOrCreate(
                            [
                                'title' => 'Sales Revenue', // Search by title
                                'pre_defined' => 1,      // and pre_defined
                                'store_id' => Auth::user()->store_id, // and store_id
                                'account_number' => 4000,
                                'account_number' => 4000,
                                'parent_id' => $revenue_coa->id ?? null,
                                'head_account' => true    
                            ],  
                            [
                                'type' => 'income',
                                'description' => 'This account handles the Sales Revenue transactions', // Added description key
                                'opening_balance' => 0,
                            ]
                        );


                        AccountController::reverse_transaction([
                            'reference_type' => 'sales_order',
                            'reference_id' => $order->id,
                            'transaction_count'=> 2,
                            'order_by' => 'DESC',
                            'order_column' => 'id',
                            'description' => 'Transaction reversed because Order '.$order->tran_no.'   is updated by '. Auth::user()->name.''
                        ]);

                    
    
                        if($request->has('order_tyoe') && $request->order_tyoe === 'pos'){
                            $current_asset_coa = AccountController::get_coa_account(['title' => 'Current Assets']);
                            $cash_account = Account::firstOrCreate(
                                [
                                    'title' => 'Cash', // Search by title
                                    'pre_defined' => 1,      // and pre_defined
                                    'store_id' => Auth::user()->store_id, 
                                    'account_number' => 1000,
                                    'parent_id' => $current_asset_coa->id,
                                    'head_account' => true// and store_id
                                ],
                                [
                                    'type' => 'assets',
                                    'description' => 'This account is created by system on cash sales', // Added description key
                                    'opening_balance' => 0,
                                ]
                            );
    
                            if($revenue_account && $cash_account){


                                AccountController::record_journal_entry([
                                    'account_id' => $revenue_account->id, 
                                    'reference_type' => 'sales_order',
                                    'reference_id' => $order->id,
                                    'credit' => $order->net_total,
                                    'debit' => 0,
                                    'note' => 'This transaction is made by '.Auth::user()->name.' for order '. $order->tran_no .'',
                                    'source_account' => $cash_account->id,
                                    'transaction_date' => $request->has('bill_date') ? $request->bill_date : date('Y-m-d',time()),
                                ]);
                            }
                        }
    
                        if($request->has('order_tyoe') && $request->order_tyoe !== 'pos'){
                            $party = Parties::find($order->customer_id);
                            if($party){
                                $group_validation = PartiesController::is_customer_group($party->id);
                                $is_customer = $group_validation["is_customer"];
                                $is_vendor = $group_validation["is_vendor"];
                              
                                $party_account = Account::firstOrCreate(
                                    [ 
                                        'store_id' => Auth::user()->store_id, // and store_id,
                                        'reference_type' => $is_customer ? 'customer' : 'vendor',
                                        'reference_id' => $party->id,
                                    ],
                                    [
                                        'title' => $party->party_name,
                                        'type' => $is_customer ? 'assets' : 'liabilities',
                                        'description' => 'This account is created by system on creating sale order '.$order->tran_no, // Added description key
                                        'opening_balance' => 0,
                                    ]
                                );
    
                                if($party_account){

                                    AccountController::record_journal_entry([
                                        'account_id' => $revenue_account->id,
                                        'reference_type' => 'sales_order',
                                        'reference_id' => $order->id,
                                        'credit' =>  $order->net_total,
                                        'debit' => 0,
                                        'note' => 'This transaction is made by '.Auth::user()->name.' for order '. $order->tran_no .'',
                                        'source_account' => $party_account->id,
                                        'transaction_date' => $request->has('bill_date') ? $request->bill_date : date('Y-m-d',time()),
                                    ]);

                                    // $debit = AccountTransaction::create([
                                    //     'store_id' => Auth::user()->store_id,
                                    //     'account_id' => $revenue_account->id,
                                    //     'reference_type' => 'sales_order',
                                    //     'reference_id' => $order->id,
                                    //     'credit' =>  $order->net_total,
                                    //     'debit' =>0,
                                    //     'transaction_date' => $request->has('bill_date') ? $request->bill_date : date('Y-m-d',time()),
                                    //     'note' => 'This transaction is made by '.Auth::user()->name.' for order '. $order->tran_no .'',
                                    // ]);
    
                                    // $credit = AccountTransaction::create([
                                    //     'store_id' => Auth::user()->store_id,
                                    //     'account_id' => $party_account->id,
                                    //     'reference_type' => 'sales_order',
                                    //     'reference_id' => $order->id,
                                    //     'credit' => 0,
                                    //     'debit' => $order->net_total,
                                    //     'transaction_date' => $request->has('bill_date') ? $request->bill_date : date('Y-m-d',time()),
                                    //     'note' => 'This transaction is made by '.Auth::user()->name.' for order '. $order->tran_no .'',
                                    // ]);
                                }
                            }
                        }
                    }

                    DB::commit();

                    toast('Order Updated!', 'info');
                    return redirect()->back()->with('openNewWindow',$request->has('print_invoice')  ? $order->id : false );
                } else {
                    DB::rollBack();
                    return 'error';
                }
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    public function search_sale(Request $request){
        try {
            
            $doc_no = $request->has("doc_no") && (int) $request->input("doc_no") ?  (int) $request->input("doc_no") : null;
            if(!$doc_no){
                toast("Invalid document no.",'error');
                return redirect()->back();
            }

            $order = Sales::where("id",$doc_no)->filterByStore()->first();

            if(!$order){
                toast("Invalid document no.",'error');
                return redirect()->back();
            }

            return redirect()->to('/sales/edit/' . $order->id);

        } catch (\Throwable $th) {
            throw $th;
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        try {
            $sale = Sales::where('id',$id)->filterByStore()->first();
            if($sale){
                $this->updateOrderTransaction($sale->id,$sale->customer_id,$sale->recieved,0,'Deleted Order '.$sale->tran_no);
                if($sale->delete()){
                    
                     $details = SalesDetails::where('sale_id' , $id);
                     $this->deletedItemsOnOrderUpdate($details->get());
                     $details->delete();


                     if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                        $reversible_transactions = AccountTransaction::where([
                            'store_id' => Auth::user()->store_id,
                            'reference_type' => 'sales_order',
                            'reference_id' => $id,
                        ])->orderBy("id","DESC")->take(2)->get();
        
                        if($reversible_transactions && count($reversible_transactions) > 0){
                            foreach ($reversible_transactions as $key => $reversible_transaction) {
                                if($reversible_transaction->credit && $reversible_transaction->credit > 0){
                                    AccountTransaction::create([
                                        'store_id' => Auth::user()->store_id,
                                        'account_id' => $reversible_transaction->account_id,
                                        'reference_type' => 'sales_order',
                                        'reference_id' => $id,
                                        'credit' => 0,
                                        'debit' => $reversible_transaction->credit,
                                        'transaction_date' => date('Y-m-d',time()),
                                        'note' => 'This transaction is reversed transaction Ref ID '.$reversible_transaction->id.' because Order '.$id.'   is deleted by '. Auth::user()->name.'',
                                    ]);
                                }else{
                                    AccountTransaction::create([
                                        'store_id' => Auth::user()->store_id,
                                        'account_id' => $reversible_transaction->account_id,
                                        'reference_type' => 'sales_order',
                                        'reference_id' => $id,
                                        'credit' => $reversible_transaction->debit,
                                        'debit' => 0,
                                        'transaction_date' => date('Y-m-d',time()),
                                        'note' => 'This transaction is reversed transaction Ref ID '.$reversible_transaction->id.'  because Order '.$id.'   is deleted by  '. Auth::user()->name.'',
                                    ]);
                                }
                            }
                        }
                    }
                    
                    Alert::toast( 'Sale '.$sale->tran_no.' Deleted  Successfuly!', 'success');
                    return redirect('/sales');
                    
                }
            }

            

            Alert::toast('invalid_request','error');
            return redirect()->back()->withErrors('error' , 'Invalid Request');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function addNewOrder($orderid = null) //$orderid if pass show the invoice popup
    {
        try {
           
            $group = PartyGroups::where('group_name', 'LIKE', 'Customer%')->first();
            $config = Configuration::filterByStore()->first();
            if ($group) {
                // $customers = Parties::filterByStore()->with("groups")->get();
                $customers = Parties::filterByStore()->with("groups")->get()->groupBy(function ($customer) {
                    return optional($customer->groups)->group_name;
                });
                // dd($customers);
            } else {
                $customers = [];
            }

            $dynamicFields = AppForms::where("name",'sales')
            ->with("fields")->whereHas("fields", function($query){
                $query->filterByStore();
            })->first();

            return view('sales.sale_orders.new_order', compact('customers','config','orderid','dynamicFields'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function receipt(int $id)
    {
        try {
            $config = Configuration::filterByStore()->first();
            $inv_type = $config->invoice_type;
            $template  = $config->invoice_template;
            $order = Sales::where('id', $id)->with('order_details.item_details', 'customer', 'user')->filterByStore()->first();
            $viewName = 'sales.invoices.'.($inv_type == 0 ? 'web.' : 'thermal.' ).$template;
            return view($viewName, compact('order', 'config'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function changeOrderStatus(Request $request)  {
        
        if($request->has('order_id') && $request->has('status')){
            $order = Sales::find($request->order_id);
            $order->update(['order_process_status' => $request->status]);
            return redirect()->back();
        }
    
        return redirect()->back();
    }

    function printChallan($id) {
        try {
            $config = Configuration::filterByStore()->first();
            $template  = $config->dc_template ?? 'challan1';
            $order = Sales::where('id', $id)->with('order_details.item_details', 'customer', 'user')->filterByStore()->first();
            if(!$config->enable_dc || !$order){
                Alert::toast("Invalid Request!",'error');
                return redirect()->back();
            }
            $viewName = 'sales.delivery-challans.'.$template;
            return view($viewName, compact('order', 'config'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    function cliendCheckStatusView()  {

        return view('sales.sale_orders.check-order-form');
    }



    function showOrderDetailsClient(Request $request) {
        try {
            if($request->has('password') && $request->has('tran_no')){
                $order = Sales::where("password", $request->password)->where('tran_no',$request->tran_no)->with('store.config','customer','order_details.item_details')->first();
                if(!$order){
                Alert::toast('Invalid Credentials','error');
                return redirect()->back();
                }
                return view("sales.sale_orders.check-order", compact("order"));
            }
            Alert::toast('Invalid Credentials','error');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function get_order_details(Request $request) {
        try {
            
            $request->validate([
                'store_id' => 'required',
                'tran_no' => 'required'
            ]);

            $tran_no = $request->input('tran_no');
            $store_id = $request->input('store_id');

            $order = Sales::where("store_id", $store_id)->where("tran_no",$tran_no)
            ->with("order_details","customer")
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
