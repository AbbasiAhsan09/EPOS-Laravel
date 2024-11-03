<?php

namespace App\Http\Controllers;

use App\Helpers\ConfigHelper;
use App\Http\Trait\InventoryTrait;
use App\Models\Account;
use App\Models\Configuration;
use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    use InventoryTrait; 
    

    public function index(Request $request) {
        try {
            
            $items = PurchaseReturn::filterByStore()->orderBy('return_date','DESC');
            
            session()->forget("preturn_start_date");
            session()->forget("preturn_end_date");
            session()->forget("preturn_party_id");
            session()->forget("preturn_type");


            if(!empty($request->input("start_date")) && !empty($request->input("end_date"))){
                $range = [$request->input("start_date"), $request->input("end_date")];
                $items = $items->whereBetween("return_date", $range);
                session()->put('preturn_start_date',$request->input("start_date"));
                session()->put('preturn_end_date',$request->input("end_date"));
            }
           
            if(!empty($request->input("party_id"))){
                session()->put('preturn_party_id',$request->input("party_id"));
                $items = $items->where("party_id", $request->input('party_id'));
            }

            if($request->input("type") === 'pdf'){
                $data = ["records" => $items->get()];
                $pdf = Pdf::loadView('reports.purchase-report.return.report', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }

            $items = $items->paginate(20);

            $parties = Parties::filterByStore()->with("groups")->get()->groupBy(function ($customer) {
                return optional($customer->groups)->group_name;
            });

            return view('purchase.invoices.return.listing', compact('items','parties'));

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function create_update_purchase_return(int $orderid = null) {
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
                $order = PurchaseReturn::where("id",$orderid)->with('order_details.item_details')->filterByStore()->first();
                // dd($order);
                if(!$order){

                    toast('Invalid request','error');
                    return redirect()->back();
                }

                return view('purchase.invoices.return.create_purchase_return', compact('customers','config','orderid','order'));
            }

            $order = null;
            
            return view('purchase.invoices.return.create_purchase_return', compact('customers','config','orderid','order'));

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

            // dd($request->all());

            $item_selected = $request->has('item_id') && count($request->item_id);
            if(!$item_selected){
                toast('Please select any item ', 'error');
                return redirect()->back();
            }

            $store_prefix = "PR";
            // Check if existed purchase order
            $existedInvoice = $request->has("invoice_no") && $request->invoice_no && !$request->has("party_id");
            $data = [];
            $data["store_id"] = Auth::user()->store_id;
            $data["doc_no"] = $store_prefix . '/' . (isset(PurchaseReturn::latest()->first()->id) ? (PurchaseReturn::max("id") + 1) : 1);
            $data["user_id"] = Auth::user()->id;
            $data["return_date"] = $request->has("return_date") ? $request->return_date : date('Y-m-d',time());
            if($existedInvoice){
                $purchase = PurchaseInvoice::where("doc_num", $request->input("invoice_no"))->filterByStore()->first()->toArray();
                if(!$purchase){
                    toast("Invalid Purchase No.",'error');
                    return redirect()->back();
                }
                $data["purchase_id"] = $purchase["id"];
                $data["party_id"] = $purchase["party_id"];
                
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

            $return = PurchaseReturn::create($data);

            
            if($return){
                for ($i=0; $i < count($request->item_id) ; $i++) { 
                    $return_detail = [];
                    $return_detail["item_id"] = $request->item_id[$i];
                    $return_detail["is_base_unit"] = ($request->uom[$i] > 1 ? true : false);
                    $return_detail["purchase_return_id"] = $return->id;
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

                    $detail = PurchaseReturnDetail::create($return_detail);

                    if($detail && ConfigHelper::getStoreConfig()["inventory_tracking"]){
                        $this->returnQtyInventory($detail->is_base_unit,-(int)$detail->returned_qty,$detail->item_id);
                    }

                }

                if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
               
                    $inventory_head= AccountController::get_head_account(['account_number' => 1030]);

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
                                    'description' => 'This account is created by system on creating purchase return '.$return->doc_no, // Added description key
                                    'opening_balance' => 0,
                                ]
                            );

    
                            if($party_account && $inventory_head){
    
                                AccountController::record_journal_entry([
                                    'store_id' => Auth::user()->store_id,
                                    'account_id' => $inventory_head->id,
                                    'reference_type' => 'purchase_return',
                                    'reference_id' => $return->id,
                                    'credit' => $return->net_total,
                                    'debit' => 0,
                                    'transaction_date' => $return->return_date ?? date('Y-m-d',time()),
                                    'note' => 'This transaction is made by '.Auth::user()->name.' for Purchase Return '. $return->doc_no .'',
                                    'source_account' => $party_account->id
                                
                                ]);
                            }
                        }
                    }
                }
            }

            

            DB::commit();

            toast('Purchase Return created', 'success');
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

            $return = PurchaseReturn::where('id',$id)->filterByStore()->first();

            if(!$return){
                toast('Not found any return against ID : '.$id, 'error');
                return redirect()->back();
            }

            // Check if existed purchase invoice
            $existedInvoice = $request->has("invoice_no") && $request->invoice_no && !$request->has("party_id");
            $data = [];
            $data["store_id"] = Auth::user()->store_id;
            $data["user_id"] = Auth::user()->id;
            $data["return_date"] = $request->has("return_date") ? $request->return_date : date('Y-m-d',time());
            if($existedInvoice){
                $purchase = PurchaseInvoice::where("doc_num", $request->input("invoice_no"))->filterByStore()->first()->toArray();
                if(!$purchase){
                    toast("Invalid Purchase No.",'error');
                    return redirect()->back();
                }
                $data["purchase_id"] = $purchase["id"];
                $data["party_id"] = $purchase["party_id"];
                
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
                    
                    $deleteItems = PurchaseReturnDetail::where('purchase_return_id' , $return->id)->whereNotIn('item_id' , $request->item_id);
                    foreach ($deleteItems->get() as $deleteItem) {
                        if($deleteItem && ConfigHelper::getStoreConfig()["inventory_tracking"]){
                            $this->UpdateReturnQtyInventory(0,$deleteItem->is_base_unit,-$deleteItem->returned_qty,0,$deleteItem->item_id);
                        }
                    }
                    $deleteItems->delete();

                    $detail = PurchaseReturnDetail::where(["item_id" => $request->item_id[$i], 'purchase_return_id' => $return->id])->first();
                    $oldQty = $detail->returned_qty ?? 0;
                    $was_base_unit = $detail->is_base_unit ?? false;
                    $return_detail = [];
                    $return_detail["item_id"] = $request->item_id[$i];
                    $return_detail["is_base_unit"] = ($request->uom[$i] > 1 ? true : false);
                    $return_detail["purchase_return_id"] = $return->id;
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
                        $this->UpdateReturnQtyInventory($detail->is_base_unit,$was_base_unit,-$oldQty,-$detail->returned_qty,$detail->item_id);
                    }
                    // dd($oldQty,$detail);


                }

                if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
               
                     // Reverse transactions
                     AccountController::reverse_transaction([
                        'reference_type' => 'purchase_return',
                        'reference_id' => $return->id,
                        'date' => (isset($return->return_date) && $return->return_date) ? $return->return_date : null,
                        'description' => 'This transaction is reversed transaction because Purchase Return'.$return->doc_no.'   is update by '. Auth::user()->name.'',
                        'transaction_count' => 2,
                        'order_by' => 'DESC',
                        'order_column' => 'id'
                    ]);

                    $inventory_head= AccountController::get_head_account(['account_number' => 1030]);
    
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
                                    'description' => 'This account is created by system on creating purchase return '.$return->doc_no, // Added description key
                                    'opening_balance' => 0,
                                ]
                            );

    
                            if($party_account  && $inventory_head){
    
                                AccountController::record_journal_entry([
                                    'store_id' => Auth::user()->store_id,
                                    'account_id' => $inventory_head->id,
                                    'reference_type' => 'purchase_return',
                                    'reference_id' => $return->id,
                                    'credit' => $return->net_total,
                                    'debit' => 0,
                                    'transaction_date' => $return->return_date ?? date('Y-m-d',time()),
                                    'note' => 'This transaction is made by '.Auth::user()->name.' for Purchase Return '. $return->doc_no .'',
                                    'source_account' => $party_account->id
                                
                                ]);
                            }
                        }
                    }
                }
            }

            

            DB::commit();

            toast('Purchase Return created', 'success');
            return redirect()->back();


        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }


    public function invoice(int $id){
        try {

            $order = PurchaseReturn::where("id",$id)->filterByStore()->first();
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
            $return= PurchaseReturn::where("id",$id)->filterByStore()->first();

            if(!$return){
                toast("Invalid Request", 'error');
            }

            DB::beginTransaction();

            $details = PurchaseReturnDetail::where("purchase_return_id", $return->id);

            foreach ($details->get() as $key => $detail) {
                $this->UpdateReturnQtyInventory($detail->is_base_unit,0,0,$detail->returned_qty,$detail->item_id);
            }
            $details->delete();

            if(ConfigHelper::getStoreConfig()["use_accounting_module"]){
                AccountController::reverse_transaction([
                    'reference_type' => 'purchase_return',
                    'reference_id' => $return->id,
                    'description' => 'This transaction is reversed because purchase return '.$return->doc_no.'   is deleted by '. Auth::user()->name.'',
                    'transaction_count' => 0,
                    'order_by' => 'DESC',
                    'order_column' => 'id',
                ]);
               }


            $return->delete();


            DB::commit();

            toast('Purchase return deleted','success');

            return redirect()->back();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;

        }
    }

}
