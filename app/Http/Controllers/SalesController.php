<?php

namespace App\Http\Controllers;

use App\Http\Trait\InventoryTrait;
use App\Http\Trait\TransactionsTrait;
use App\Models\Configuration;
use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\Sales;
use App\Models\SalesDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

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
        $items = Sales::orderBy('id', 'DESC')
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
        try {

            $validate = $request->validate([
                'item_id' => 'required',
                'rate' => 'required',
                'qty' => 'required',
                'tax' => 'required',
                'order_tyoe' => 'required',
                'payment_method' => 'required',
                'recieved' => 'required',
                'gross_total' => 'required',
                'discount' => 'required',
                'other_charges' => 'required'
            ]);

            if ($validate) {

                $store_prefix = 'SA';
                $order  = new Sales();
                $order->tran_no = date('d') . '/' . $store_prefix . '/' . date('y') . '/' . date('m') . '/' . (isset(Sales::latest()->first()->id) ? (Sales::latest()->first()->id + 1) : 1);
                $order->customer_id = ($request->party_id ? $request->party_id : 0);
                $order->gross_total = $request->gross_total;
                $order->other_charges = $request->other_charges;
                $order->recieved = $request->recieved;
                $order->payment_method = $request->payment_method;
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
                $order->save();

                $this->createOrderTransactionHistory($order->id,$order->customer_id,$order->recieved,date('Y-m-d'),'recieved');

                if ($order && count($request->item_id)) {
                    for ($i = 0; $i < count($request->item_id); $i++) {
                        $details = new SalesDetails();
                        $details->sale_id = $order->id;
                        $details->item_id = $request->item_id[$i];
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

                        if ($this->allowInventoryCheck) {
                            // dd('hi');
                            if ($this->allowLowInventory) {
                                // dd($this->checkAvaialableInventory($details->item_id, $details->is_base_unit));
                                $this->subtractInventoryWithOrder($details->item_id, $details->qty, $details->is_base_unit);
                            } else {
                                if ($this->checkAvaialableInventory($details->item_id, $details->is_base_unit) && $this->checkAvaialableInventory($details->item_id, $details->is_base_unit) >= $details->qty) {
                                    // dd($this->checkAvaialableInventory($details->item_id, $details->is_base_unit));                                    
                                    $this->subtractInventoryWithOrder($details->item_id, $details->qty, $details->is_base_unit);
                                } else {
                                    $details->delete();
                                }
                            }
                        }
                    }
                    toast('Order Created!', 'success');
                    return redirect('/sales');
                } else {
                    return 'error';
                }
            }
        } catch (\Throwable $th) {
            throw $th;
        }
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
            $order = Sales::where('id', $id)->with('order_details.item_details')->first();
            if ($order) {
                $group = PartyGroups::where('group_name', 'LIKE', 'Customer%')->first();
                if ($group) {
                    $customers = Parties::where('group_id', $group->id)->get();
                } else {
                    $customers = [];
                }

                return view('sales.sale_orders.new_order', compact('order', 'customers'));
            }


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
                'payment_method' => 'required',
                'recieved' => 'required',
                'gross_total' => 'required',
                'discount' => 'required',
                'other_charges' => 'required'
            ]);

            if ($validate) {
                $order  =  Sales::find($id);
                
                if (!$order) {
                    return redirect()->back()->withErrors('error', 'Invalid Request');
                }
                $old_amount = $order->recieved;
                // dd($old_amount,);
                $order->customer_id = ($request->party_id ? $request->party_id : 0);
                $order->gross_total = $request->gross_total;
                $order->other_charges = $request->other_charges;
                $order->recieved = $request->recieved;
                $order->payment_method = $request->payment_method;
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
                $order->save();
                

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
                   
                    toast('Order Updated!', 'info');
                    return redirect('/sales');
                } else {
                    return 'error';
                }
            }
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
            $sale = Sales::find($id);
            if($sale){
                $this->updateOrderTransaction($sale->id,$sale->customer_id,$sale->recieved,0,'Deleted Order '.$sale->tran_no);
                if($sale->delete()){
                    
                     $details = SalesDetails::where('sale_id' , $id);
                     $this->deletedItemsOnOrderUpdate($details->get());
                     $details->delete();
                    
                     Alert::toast( 'Sale '.$sale->tran_no.' Deleted  Successfuly!', 'success');
                        return redirect('/sales');
                    
                }
            }
            return redirect()->back()->withErrors('error' , 'Invalid Request');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function addNewOrder()
    {
        try {
            $group = PartyGroups::where('group_name', 'LIKE', 'Customer%')->first();
            if ($group) {
                $customers = Parties::where('group_id', $group->id)->get();
            } else {
                $customers = [];
            }
            return view('sales.sale_orders.new_order', compact('customers'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function receipt(int $id)
    {
        try {
            $config = Configuration::latest()->first();
            $inv_type = $config->invoice_type;
            $template  = $config->invoice_template;
            $order = Sales::where('id', $id)->with('order_details.item_details', 'customer', 'user')->first();
            $viewName = 'sales.invoices.'.($inv_type == 0 ? 'web.' : 'thermal.' ).$template;
            return view($viewName, compact('order', 'config'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
