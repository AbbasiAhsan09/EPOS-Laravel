<?php

namespace App\Http\Controllers;

use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\Sales;
use App\Models\SalesDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = Sales::orderBy('id','DESC')->paginate(20);
        // dd($items);
        return view('sales.index',compact('items'));
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
            // dd($request);
            $validate = $request->validate([
                'item_id' => 'required',
                'uom' => 'required',
                'rate' => 'required',
                'qty' => 'required',
                'tax' => 'required',
                'order_tyoe' =>'required',
                'payment_method' => 'required',
                'recieved' => 'required',
                'gross_total' => 'required',
                'discount' => 'required',
                'other_charges' => 'required'
            ]);

            if($validate){
                $order  = new Sales();
                $order->customer_id = $request->party_id;
                $order->gross_total = $request->gross_total;
                $order->other_charges = $request->other_charges;
                $order->user_id = Auth::user()->id;
                $order->net_total = $request->gross_total - ($request->has('discount') && $request->discount > 1 ? $request->discount : 0) + ($request->has('other_charges') && $request->other_charges > 1 ? $request->other_charges : 0 );
                $order->save();
                if($order && count($request->item_id)){
                   for ($i=0; $i < count($request->item_id); $i++) { 
                     $details = new SalesDetails();
                     $details->sale_id = $order->id;
                     $details->item_id = $request->item_id[$i];
                     $details->is_base_unit = ($details->uom > 1 ? true : false );
                     $details->tax = $request->tax[$i];
                     $details->qty = $request->qty[$i];
                     
                     $details->rate = $request->rate[$i];
                        if($request->has('item_disc')){
                            $details->total = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]) - ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->item_disc[$i]));
                        } else{
                            $details->total = (($request->qty[$i] * $request->rate[$i]) + ((($request->qty[$i] * $request->rate[$i]) / 100) * $request->tax[$i]));
                        }  
                    $details->save();
                   }
                   toast('Order Created!','success');
                   return redirect('/sales');
                }else{
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
    public function edit(Sales $sales)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Sales $sales)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sales  $sales
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sales $sales)
    {
        //
    }

    public function addNewOrder()
    {
        try {
            $group = PartyGroups::where('group_name','LIKE','Customer%')->first();
            $customers = Parties::where('group_id',$group->id)->get();
            return view('sales.sale_orders.new_order',compact('customers'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
