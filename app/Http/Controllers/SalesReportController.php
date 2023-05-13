<?php

namespace App\Http\Controllers;

use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\Sales;
use Illuminate\Http\Request;

class SalesReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        session()->forget('sales_filter_deleted');
        session()->forget('sales_report_start_date');
        session()->forget('sales_report_end_date');
        session()->forget('sales_report_customer');
        $from = $request->start_date;
        $to = $request->end_date;
        $party = PartyGroups::where('group_name' , 'LIKE', 'customer%')->first();
        $customers = Parties::where('group_id', $party->id)->get();
        $records = Sales::orderBy('id', 'DESC')
        ->when(($request->has('start_date') && $request->start_date != null) && ($request->has('end_date') && $request->end_date != null), function($query) use($request){
            $query->whereBetween('created_at' , [$request->start_date , $request->end_date]);
            session()->put('sales_report_start_date', $request->start_date);
            session()->put('sales_report_end_date', $request->end_date);
        })
        ->when($request->has('customer') && $request->customer != null, function($query) use($request){
            $query->where('customer_id',$request->customer);
            session()->put('sales_report_customer', $request->customer);
        });

        
        if($request->type === 'pdf'){
           $records = $records->get();
        }else{
           $records = $records->paginate(20);
        }
        
        return view('reports.sales-report.report1', compact('records', 'from', 'to', 'customers'));

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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
