<?php

namespace App\Http\Controllers;

use App\Helpers\ConfigHelper;
use App\Http\Trait\InventoryTrait;
use App\Models\Account;
use App\Models\Configuration;
use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\SaleReturn;
use App\Models\SaleReturnDetail;
use App\Models\Sales;
use App\Models\Stores;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SaleReturnController extends Controller
{
    use InventoryTrait; 
    

    public function index(Request $request) {
        try {
            
            $items = SaleReturn::filterByStore()->orderBy('return_date','DESC');
            
            session()->forget("sreturn_start_date");
            session()->forget("sreturn_end_date");
            session()->forget("sreturn_party_id");
            session()->forget("sreturn_type");


            if(!empty($request->input("start_date")) && !empty($request->input("end_date"))){
                $range = [$request->input("start_date"), $request->input("end_date")];
                $items = $items->whereBetween("return_date", $range);
                session()->put('sreturn_start_date',$request->input("start_date"));
                session()->put('sreturn_end_date',$request->input("end_date"));
            }
           
            if(!empty($request->input("party_id"))){
                session()->put('sreturn_party_id',$request->input("party_id"));
                $items = $items->where("party_id", $request->input('party_id'));
            }

            if($request->input("type") === 'pdf'){
                $data = ["records" => $items->get()];
                $pdf = Pdf::loadView('reports.sales-report.return.report', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }

            $items = $items->paginate(20);

            $parties = Parties::filterByStore()->with("groups")->get()->groupBy(function ($customer) {
                return optional($customer->groups)->group_name;
            });

            return view('sales.sale_orders.return.listing', compact('items','parties'));

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function create_update_sales_return(int $orderid = null) {
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

            if($orderid && !empty($orderid)){
                $order = SaleReturn::where("id",$orderid)->with('order_details.item_details')->filterByStore()->first();
                // dd($order);
                if(!$order){

                    toast('Invalid request','error');
                    return redirect()->back();
                }

                return view('sales.sale_orders.return.create_sale_return', compact('customers','config','orderid','order'));
            }

            $order = null;
            
            return view('sales.sale_orders.return.create_sale_return', compact('customers','config','orderid','order'));

        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function store(Request $request){
        try {

            $request->validate([
                'return_date' => "required",
                'total' => "required",
            ]);

            $item_selected = $request->has('item_id') && count($request->item_id);
            if(!$item_selected){
                toast('Please select any item ', 'error');
                return redirect()->back();
            }

            $store_prefix = "SR";
            // Check if existed sale order
            $existedInvoice = $request->has("invoice_no") && $request->invoice_no && !$request->has("party_id");
            $data = [];
            $data["store_id"] = Auth::user()->store_id;
            $data["doc_no"] = $store_prefix . '/' . (isset(SaleReturn::latest()->first()->id) ? (SaleReturn::max("id") + 1) : 1);
            $data["user_id"] = Auth::user()->id;
            $data["return_date"] = $request->has("return_date") ? $request->return_date : date('Y-m-d',time());
            if($existedInvoice){
                $sale = Sales::where("tran_no", $request->input("invoice_no"))->filterByStore()->first()->toArray();
                if(!$sale){
                    toast("Invalid Sale No.",'error');
                    return redirect()->back();
                }
                $data["sale_id"] = $sale["id"];
                $data["party_id"] = $sale["customer_id"];
                
            }else{
                $data["party_id"] = $request->has("party_id") ? (int)($request->input("party_id")) : null ;
            }

            $data["invoice_no"] = $request->has("invoice_no") ? $request->input("invoice_no") : null;
            $data["reason"] = $request->has("reason") && $request->input('reason') ? $request->input('reason') : null;
            $data["total"] = $request->has('total') ? $request->input('total') : 0;
            $data["other_charges"] = $request->has('other_charges') ? $request->input('other_charges') : 0;
            $discount = 0;
            if ($request->has('discount') && (substr($request->discount, 0, 1) == '%')) {
                $data["discount_type"] = 'PERCENT';
                $data["discount"] = ((int)ltrim($request->discount, '%'));
                $discount = (($request->total / 100) *  ((int)ltrim($request->discount, '%')));
            } else if ($request->has('discount') && $request->discount > 0) {
                $data["discount_type"] = 'FLAT';
                $data["discount"]= $request->discount;
                $discount =  $request->discount;
            }
            $data["net_total"] = $request->total - $discount + ($request->has('other_charges') && $request->other_charges > 1 ? $request->other_charges : 0);

            DB::beginTransaction();
            $this->configInventoryChecks();

            $return = SaleReturn::create($data);

            
            if($return){
                for ($i=0; $i < count($request->item_id) ; $i++) { 
                    $return_detail = [];
                    $return_detail["item_id"] = $request->item_id[$i];
                    $return_detail["is_base_unit"] = ($request->uom[$i] > 1 ? true : false);
                    $return_detail["sale_id"] = $return->id;
                    if(isset($request->bags)){
                        $return_detail["bags"] = $request->bags[$i];
                    }

                    if(isset($request->bag_size)){
                        $return_detail["bag_size"] = $request->bag_size[$i];
                    }

                    $return_detail["returned_rate"] = $request->rate[$i];
                    $return_detail["returned_tax"] = $request->tax[$i];
                    $return_detail["returned_qty"] = $request->qty[$i];
                    if ($request->has('item_disc')) {
                        $return_detail["returned_total"] = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]) - ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->item_disc[$i]));
                    } else {
                        $return_detail["returned_total"] = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]));
                    }

                    $detail = SaleReturnDetail::create($return_detail);

                    if($detail && ConfigHelper::getStoreConfig()["inventory_tracking"]){
                        $this->returnQtyInventory($detail->is_base_unit,$detail->returned_qty,$detail->item_id);
                    }

                }

                if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
               
                    $revenue_coa = AccountController::get_coa_account(['title' => 'Revenue']);
    
    
                    $revenue_account = Account::firstOrCreate(
                        [
                            'pre_defined' => 1,      // and pre_defined
                            'store_id' => Auth::user()->store_id, // and store_id
                            'account_number' => 4000,
                            'parent_id' => $revenue_coa->id ?? null,
                            'head_account' => true
                        ],
                        [
                            'title' => 'Sales Revenue', // Search by title
                            'type' => 'income',
                            'description' => 'This account handles the Sales Revenue transactions', // Added description key
                            'opening_balance' => 0,
                        ]
                    );
                    // dd($revenue_account);
                    if((!$return->party_id) || $return->party_id === 0){
                        $current_asset_coa = AccountController::get_coa_account(['title' => 'Current Assets']);
                        $cash_account = Account::firstOrCreate(
                            [
                                'pre_defined' => 1,      // and pre_defined
                                'store_id' => Auth::user()->store_id, // and store_id
                                'account_number' => 1000,
                                'parent_id' => $current_asset_coa->id,
                                'head_account' => true// and store_id
                                
                            ],
                            [
                                'title' => 'Cash', // Search by title
                                'type' => 'assets',
                                'description' => 'This account is created by system on cash sales', // Added description key
                                'opening_balance' => 0,
                            ]
                        );
    
                        if($revenue_account && $cash_account){
    
                            AccountController::record_journal_entry([
                                'store_id' => Auth::user()->store_id,
                                'account_id' => $cash_account->id,
                                'reference_type' => 'sales_return',
                                'reference_id' => $return->id,
                                'credit' => $return->net_total,
                                'debit' => 0,
                                'transaction_date' => $return->return_date ?? date('Y-m-d',time()),
                                'note' => 'This transaction is made by '.Auth::user()->name.' for sale return '. $return->doc_no .'',
                                'source_account' => $revenue_account->id
                            
                            ]);
                         
                        }
                    }
    
                    if($return->party_id && $return->party_id !== 0){

                        $party = Parties::find($return->party_id);
                        if($party){
                        $group_validation = PartiesController::is_customer_group($party->group_id);
                        $is_customer = $group_validation["is_customer"];
                           $party_account = Account::firstOrCreate(
                                [ 
                                    'store_id' => Auth::user()->store_id, // and store_id,
                                    'reference_type' => $is_customer ? 'customer' : 'vendor',
                                    'reference_id' => $party->id,
                                ],
                                [
                                    'title' => $party->party_name,
                                    'type' => $is_customer ? 'assets' : 'liabilities',
                                    'description' => 'This account is created by system on creating sale order '.$return->doc_no, // Added description key
                                    'opening_balance' => 0,
                                ]
                            );

    
                            if($party_account){
    
                                AccountController::record_journal_entry([
                                    'store_id' => Auth::user()->store_id,
                                    'account_id' => $party_account->id,
                                    'reference_type' => 'sales_return',
                                    'reference_id' => $return->id,
                                    'credit' => $return->net_total,
                                    'debit' => 0,
                                    'transaction_date' => $return->return_date ?? date('Y-m-d',time()),
                                    'note' => 'This transaction is made by '.Auth::user()->name.' for sale return '. $return->doc_no .'',
                                    'source_account' => $revenue_account->id
                                
                                ]);
                            }
                        }
                    }
                }
            }

            

            DB::commit();

            toast('Sale return created', 'success');
            return redirect()->back();


        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function update(int $id, Request $request){
        try {
            // dd($request->all());
            $request->validate([
                'return_date' => "required",
                'total' => "required",
            ]);

            $item_selected = $request->has('item_id') && count($request->item_id);
            if(!$item_selected){
                toast('Please select any item ', 'error');
                return redirect()->back();
            }

            $return = SaleReturn::where('id',$id)->filterByStore()->first();

            if(!$return){
                toast('Not found any return against ID : '.$id, 'error');
                return redirect()->back();
            }

            // Check if existed sale order
            $existedInvoice = $request->has("invoice_no") && $request->invoice_no && !$request->has("party_id");
            $data = [];
            $data["store_id"] = Auth::user()->store_id;
            $data["user_id"] = Auth::user()->id;
            $data["return_date"] = $request->has("return_date") ? $request->return_date : date('Y-m-d',time());
            if($existedInvoice){
                $sale = Sales::where("tran_no", $request->input("invoice_no"))->filterByStore()->first()->toArray();
                if(!$sale){
                    toast("Invalid Sale No.",'error');
                    return redirect()->back();
                }
                $data["sale_id"] = $sale["id"];
                $data["party_id"] = $sale["customer_id"];
                
            }else{
                $data["party_id"] = $request->has("party_id") ? (int)($request->input("party_id")) : null ;
            }

            $data["invoice_no"] = $request->has("invoice_no") ? $request->input("invoice_no") : null;
            $data["reason"] = $request->has("reason") && $request->input('reason') ? $request->input('reason') : null;
            $data["total"] = $request->has('total') ? $request->input('total') : 0;
            $data["other_charges"] = $request->has('other_charges') ? $request->input('other_charges') : 0;
            $discount = 0;
            if ($request->has('discount') && (substr($request->discount, 0, 1) == '%')) {
                $data["discount_type"] = 'PERCENT';
                $data["discount"] = ((int)ltrim($request->discount, '%'));
                $discount = (($request->total / 100) *  ((int)ltrim($request->discount, '%')));
            } else if ($request->has('discount') && $request->discount > 0) {
                $data["discount_type"] = 'FLAT';
                $data["discount"]= $request->discount;
                $discount =  $request->discount;
            }
            $data["net_total"] = $request->total - $discount + ($request->has('other_charges') && $request->other_charges > 1 ? $request->other_charges : 0);

            DB::beginTransaction();
            $this->configInventoryChecks();

            $return->update($data);

            
            if($return){
                for ($i=0; $i < count($request->item_id) ; $i++) {
                    
                    $deleteItems = SaleReturnDetail::where('sale_id' , $return->id)->whereNotIn('item_id' , $request->item_id);
                    foreach ($deleteItems->get() as $deleteItem) {
                        if($deleteItem && ConfigHelper::getStoreConfig()["inventory_tracking"]){
                            $this->UpdateReturnQtyInventory(0,$deleteItem->is_base_unit,$deleteItem->returned_qty,0,$deleteItem->item_id);
                        }
                    }
                    $deleteItems->delete();

                    $detail = SaleReturnDetail::where(["item_id" => $request->item_id[$i], 'sale_id' => $return->id])->first();
                    $oldQty = $detail->returned_qty ?? 0;
                    $was_base_unit = $detail->is_base_unit ?? false;
                    $return_detail = [];
                    $return_detail["item_id"] = $request->item_id[$i];
                    $return_detail["is_base_unit"] = ($request->uom[$i] > 1 ? true : false);
                    $return_detail["sale_id"] = $return->id;
                    if(isset($request->bags)){
                        $return_detail["bags"] = $request->bags[$i];
                    }

                    if(isset($request->bag_size)){
                        $return_detail["bag_size"] = $request->bag_size[$i];
                    }

                    $return_detail["returned_rate"] = $request->rate[$i];
                    $return_detail["returned_tax"] = $request->tax[$i];
                    $return_detail["returned_qty"] = $request->qty[$i];
                    if ($request->has('item_disc')) {
                        $return_detail["returned_total"] = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]) - ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->item_disc[$i]));
                    } else {
                        $return_detail["returned_total"] = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]));
                    }

                    $detail->update($return_detail);
                    if($detail && ConfigHelper::getStoreConfig()["inventory_tracking"]){
                        $this->UpdateReturnQtyInventory($detail->is_base_unit,$was_base_unit,$oldQty,$detail->returned_qty,$detail->item_id);
                    }
                    // dd($oldQty,$detail);


                }

                if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
               
                     // Reverse transactions
                     AccountController::reverse_transaction([
                        'reference_type' => 'sales_return',
                        'reference_id' => $return->id,
                        'date' => (isset($return->return_date) && $return->return_date) ? $return->return_date : null,
                        'description' => 'This transaction is reversed transaction because sale return'.$return->doc_no.'   is update by '. Auth::user()->name.'',
                        'transaction_count' => 2,
                        'order_by' => 'DESC',
                        'order_column' => 'id'
                    ]);

                    $revenue_coa = AccountController::get_coa_account(['title' => 'Revenue']);
    
    
                    $revenue_account = Account::firstOrCreate(
                        [
                            'pre_defined' => 1,      // and pre_defined
                            'store_id' => Auth::user()->store_id, // and store_id
                            'account_number' => 4000,
                            'parent_id' => $revenue_coa->id ?? null,
                            'head_account' => true
                        ],
                        [
                            'title' => 'Sales Revenue', // Search by title
                            'type' => 'income',
                            'description' => 'This account handles the Sales Revenue transactions', // Added description key
                            'opening_balance' => 0,
                        ]
                    );
                    // dd($revenue_account);
                    if((!$return->party_id) || $return->party_id === 0){
                        $current_asset_coa = AccountController::get_coa_account(['title' => 'Current Assets']);
                        $cash_account = Account::firstOrCreate(
                            [
                                'pre_defined' => 1,      // and pre_defined
                                'store_id' => Auth::user()->store_id, // and store_id
                                'account_number' => 1000,
                                'parent_id' => $current_asset_coa->id,
                                'head_account' => true// and store_id
                                
                            ],
                            [
                                'title' => 'Cash', // Search by title
                                'type' => 'assets',
                                'description' => 'This account is created by system on cash sales', // Added description key
                                'opening_balance' => 0,
                            ]
                        );
    
                        if($revenue_account && $cash_account){
    
                            AccountController::record_journal_entry([
                                'store_id' => Auth::user()->store_id,
                                'account_id' => $cash_account->id,
                                'reference_type' => 'sales_return',
                                'reference_id' => $return->id,
                                'credit' => $return->net_total,
                                'debit' => 0,
                                'transaction_date' => $return->return_date ?? date('Y-m-d',time()),
                                'note' => 'This transaction is made by '.Auth::user()->name.' for sale return '. $return->doc_no .'',
                                'source_account' => $revenue_account->id
                            
                            ]);
                         
                        }
                    }
    
                    if($return->party_id && $return->party_id !== 0){

                        $party = Parties::find($return->party_id);
                        if($party){
                        $group_validation = PartiesController::is_customer_group($party->group_id);
                        $is_customer = $group_validation["is_customer"];
                           $party_account = Account::firstOrCreate(
                                [ 
                                    'store_id' => Auth::user()->store_id, // and store_id,
                                    'reference_type' => $is_customer ? 'customer' : 'vendor',
                                    'reference_id' => $party->id,
                                ],
                                [
                                    'title' => $party->party_name,
                                    'type' => $is_customer ? 'assets' : 'liabilities',
                                    'description' => 'This account is created by system on creating sale order '.$return->doc_no, // Added description key
                                    'opening_balance' => 0,
                                ]
                            );

    
                            if($party_account){
    
                                AccountController::record_journal_entry([
                                    'store_id' => Auth::user()->store_id,
                                    'account_id' => $party_account->id,
                                    'reference_type' => 'sales_return',
                                    'reference_id' => $return->id,
                                    'credit' => $return->net_total,
                                    'debit' => 0,
                                    'transaction_date' => $return->return_date ?? date('Y-m-d',time()),
                                    'note' => 'This transaction is made by '.Auth::user()->name.' for sale return '. $return->doc_no .'',
                                    'source_account' => $revenue_account->id
                                
                                ]);
                            }
                        }
                    }
                }
            }

            

            DB::commit();

            toast('Sale return created', 'success');
            return redirect()->back();


        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    public function invoice(int $id){
        try {

            $order = SaleReturn::where("id",$id)->filterByStore()->first();
            $config = Configuration::filterByStore()->first();
            if(!$order){
                toast("Invalid invoice no",'error');
                return redirect()->back();
            }

            return view("sales.invoices.web.credit-note", compact("order",'config'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }



    public function destroy(int $id){
        try {
            $return= SaleReturn::where("id",$id)->filterByStore()->first();

            if(!$return){
                toast("Invalid Request", 'error');
            }

            DB::beginTransaction();

            $details = SaleReturnDetail::where("sale_id", $return->id);

            foreach ($details->get() as $key => $detail) {
                $this->UpdateReturnQtyInventory(0,$detail->is_base_unit,$detail->returned_qty,0,$detail->item_id);
            }
            $details->delete();

            if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                AccountController::reverse_transaction([
                    'reference_type' => 'sale_return',
                    'reference_id' => $return->id,
                    'description' => 'This transaction is reversed because sale return '.$return->doc_no.'   is deleted by '. Auth::user()->name.'',
                    'transaction_count' => 0,
                    'order_by' => 'DESC',
                    'order_column' => 'id',
                ]);
               }


            $return->delete();


            DB::commit();

            toast('Sale return deleted','success');

            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;

        }
    }


    public function details(Request $request) {
        try {

            // dd($request->all());
            
            $records = SaleReturnDetail::with('return')
            ->whereHas('return', function($query) {
                $query->filterByStore();
            });

            if($request->has("from") && !empty($request->input("from"))){
                $records = $records->whereHas("return",function($query) use($request){
                    $query->where('return_date','>=', $request->from);
                });
            }

            if($request->has("to") && !empty($request->input("to"))){
                $records = $records->whereHas("return",function($query) use($request){
                    $query->where('return_date','<=', $request->to);
                });
            }

            if($request->has("category") && !empty($request->input("category"))){
                $records = $records->whereHas("item_details",function($query) use($request){
                    $query->where("category", $request->category);
                });
            }

            if($request->has("field") && !empty($request->input("field"))){
                $records = $records->whereHas('item_details.categories',  function($q) use($request){
                    $q->where('parent_cat', $request->field);
                });
            }

            if($request->has("product") && !empty($request->input("product"))){
                $records = $records->where('item_id', $request->product);
            }

            if ($request->has('type') && $request->type === 'pdf') {

                $records = $records->get();
                $data = [
                    'records' => $records,
                    'from' => $request->from ?? null,
                    'to' => $request->to ?? null,
                    'report_title' => "Sales Return Detail Report"
                ];
                $pdf = PDF::loadView('sales.sale_orders.return.pdf-detail-report', $data)->setPaper('a4', 'landscape');
                return $pdf->stream();
            }


            $records = $records->paginate(20);

            return view("sales.sale_orders.return.detail-report", compact("records"));

        } catch (\Throwable $th) {
            throw $th;
        }
    }

}
