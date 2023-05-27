<?php

namespace App\Http\Controllers;

use App\Http\Trait\TransactionsTrait;
use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\PurchaseInvoice;
use App\Models\VendorLedger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RealRashid\SweetAlert\Facades\Alert;
// use Symfony\Contracts\Translation\TranslatorTrait;

use function PHPUnit\Framework\isNull;

class VendorLedgerController extends Controller
{
    use TransactionsTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        session()->forget('p_start_date');
        session()->forget('p_end_date');
        session()->forget('l_vendor_id');
        // dd($request->all());
        $group_id  = PartyGroups::where('group_name', 'LIKE', "Vendo%")->first()->id;
        $vendors = Parties::where('group_id', $group_id)->orderBy('party_name','ASC')->get();
        $items = Parties::where('group_id', $group_id)
            ->with(['purchases' => function($query) use($request){
                $query->when(($request->has('start_date') && $request->has('end_date')) && ($request->start_date != null) && ($request->end_date != null),function($query) use($request){
                    session()->put('p_start_date', $request->start_date);
                    session()->put('p_end_date', $request->end_date);
                    $query->whereBetween(DB::raw('purchase_invoices.created_at'), [$request->start_date, $request->end_date]);
                })
                ->when($request->has('vendor_id') && ($request->vendor_id != null) , function($query) use($request){
                    $query->where('purchase_invoices.party_id' , $request->vendor_id);
                    session()->put('l_vendor_id',$request->vendor_id);
                });
            }])->paginate(20)->withQueryString();
            // dd($items);

        return view('vendor-ledger.vendor-ledger', compact('items','vendors'));
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\VendorLedger  $vendorLedger
     * @return \Illuminate\Http\Response
     */
    public function show( int $id)
    {
        $vendor = Parties::find($id);
        if ($vendor) {
            $items = PurchaseInvoice::where('party_id', $id)
                ->whereRaw('net_amount - recieved > (0.99)')
                ->paginate(10);

            return view(
                'vendor-ledger.details',
                compact('items', 'vendor')
            );
        }

        return redirect()->back()->withErrors(['error' => 'Invalid Request!']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\VendorLedger  $vendorLedger
     * @return \Illuminate\Http\Response
     */
    public function edit(VendorLedger $vendorLedger)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\VendorLedger  $vendorLedger
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $vendor_id)
    {
        
        $validate = $request->validate([
            'amount' => 'required | integer | min:1 ',
            'date' => 'date | required'
        ]);


        if($validate){
            $amount = $request->amount;
            $invoices = PurchaseInvoice::where('party_id' , $vendor_id)
            ->whereRaw('net_amount - recieved > (0.99)')->get();

            if($invoices->count()){
                foreach ($invoices as $key => $invoice) {
                    $balance = $invoice->net_amount -  $invoice->recieved;
                    if($amount >= $balance){
                        if($invoice->update(['recieved' =>$invoice->recieved + $balance , 'updated_at' => strtotime($request->date)])){
                            $amount = $amount - $balance;
                            // $this->create;
                            $this->createPurchaseTransactionHistory($invoice->id,$invoice->party_id,$balance,$request->date,'paid');
                        }
                    }else{
                        if($invoice->update(['recieved' =>$invoice->recieved + $amount, 'updated_at' => strtotime($request->date)])){
                            $this->createPurchaseTransactionHistory($invoice->id,$invoice->party_id,$amount,$request->date,'paid');
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
     * @param  \App\Models\VendorLedger  $vendorLedger
     * @return \Illuminate\Http\Response
     */
    public function destroy(VendorLedger $vendorLedger)
    {
        //
    }
}
