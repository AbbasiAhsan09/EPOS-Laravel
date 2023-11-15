<?php

namespace App\Http\Controllers;

use App\Http\Trait\TransactionsTrait;
use App\Models\CustomerLedger;
use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;

class CustomerLedgerController extends Controller
{
    use TransactionsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        session()->forget('l_start_date');
        session()->forget('l_end_date');

        $group_id  = PartyGroups::where('group_name', 'LIKE', "Customer%")->first()->id;
        $items = Parties::where('group_id', $group_id)->byUser()
            ->with(['sales' => function($query) use($request){
                $query->when($request->has('start_date') && $request->has('end_date') && ($request->start_date != null) && ($request->end_date != null) ,function($query) use($request){
                    session()->put('l_start_date', $request->start_date);
                    session()->put('l_end_date', $request->end_date);
                    $query->whereBetween(DB::raw('sales.created_at'), [$request->start_date, $request->end_date]);
                })
                ->when($request->has('customer_id') && $request->customer_id !== null , function($query) use($request){
                    $query->where('sales.customer_id' , $request->customer_id);
                });
            }])->paginate(20)->withQueryString();

        return view('customer-ledger.customer-ledger', compact('items'));
    }

    public function main()
    {
        return view('ledgers.main');
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\CustomerLedger  $customerLedger
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $customer = Parties::where('id',$id)->byUser()->first();
        if ($customer) {
            $items = Sales::where('customer_id', $id)
                ->whereRaw('net_total - recieved > (0.99)')
                ->paginate(10);

            return view(
                'customer-ledger.details',
                compact('items', 'customer')
            );
        }

        return redirect()->back()->withErrors(['error' => 'Invalid Request!']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\CustomerLedger  $customerLedger
     * @return \Illuminate\Http\Response
     */
    public function edit(CustomerLedger $customerLedger)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\CustomerLedger  $customerLedger
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $customer_id)
    {
        $validate = $request->validate([
            'amount' => 'required | integer | min:1 ',
            'date' => 'date | required'
        ]);


        if($validate){
            $amount = $request->amount;
            $orders = Sales::where('customer_id' , $customer_id)
            ->whereRaw('net_total - recieved > (0.99)')->get();

            if($orders->count()){
                foreach ($orders as $key => $order) {
                    $balance = $order->net_total -  $order->recieved;
                    if($amount >= $balance){
                        if($order->update(['recieved' =>$order->recieved + $balance , 'updated_at' => strtotime($request->date)])){
                            $amount = $amount - $balance;
                            $this->createOrderTransactionHistory($order->id,$order->customer_id,$balance,$request->date,'recieved');
                        }
                    }else{
                        if($order->update(['recieved' =>$order->recieved + $amount, 'updated_at' => strtotime($request->date)])){
                            $this->createOrderTransactionHistory($order->id,$order->customer_id,$amount,$request->date,'recieved');
                            $amount = 0;
                            break;
                        } 
                    }
                }
            }
            
            Alert::toast('Bulk Payment Updated!','success');
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\CustomerLedger  $customerLedger
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerLedger $customerLedger)
    {
        //
    }
}
