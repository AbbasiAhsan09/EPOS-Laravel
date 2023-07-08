<?php

namespace App\Http\Controllers;

use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\Products;
use App\Models\Sales;
use App\Models\SalesDetails;
use Barryvdh\DomPDF\Facade\Pdf;
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
        $party = PartyGroups::where('group_name', 'LIKE', 'customer%')->first();
        $customers = Parties::where('group_id', $party->id)->get();
        $records = Sales::orderBy('id', 'DESC')
            ->when(($request->has('start_date') && $request->start_date != null) && ($request->has('end_date') && $request->end_date != null), function ($query) use ($request) {
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                session()->put('sales_report_start_date', $request->start_date);
                session()->put('sales_report_end_date', $request->end_date);
            })
            ->when($request->has('customer') && $request->customer != null, function ($query) use ($request) {
                $query->where('customer_id', $request->customer);
                session()->put('sales_report_customer', $request->customer);
            })
            ->when($request->has('filter_deleted') && $request->filter_deleted == 'true' , function($query){
                $query->onlyTrashed();
                session()->put('sales_filter_deleted', true);
            });


        if ($request->type === 'pdf') {
            $records = $records->get();
            $data = [
                'records' => $records,
                'from' => $from,
                'to' => $to,
            ];
            $pdf = Pdf::loadView('reports.sales-report.pdf-report1', $data)->setPaper('a4', 'landscape');
            return $pdf->stream();
        } else {
            $records = $records->paginate(20);
            return view('reports.sales-report.report1', compact('records', 'from', 'to', 'customers'));
        }
    }

    public function detail(Request $request)
    {
        try {
            session()->forget('sales-detail-report-start-date');
            session()->forget('sales-detail-report-end-date');
            session()->forget('sales-detail-report-category');
            session()->forget('sales-detail-report-field');
            session()->forget('sales-detail-report-product');
            $products = Products::orderBy('name', 'ASC')->get();

            $from = $request->has('start_date') ? $request->start_date : null;
            $to = $request->has('end_date') ? $request->end_date : null;

            $records = SalesDetails::when($from != null && $to != null, function ($query) use ($request) {
                session()->put('sales-detail-report-start-date', $request->start_date);
                session()->put('sales-detail-report-end-date', $request->end_date);
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })
            ->when($request->has('field') && $request->field != null,function($query) use($request){
                $query->whereHas('item_details.categories',  function($q) use($request){
                    $q->where('parent_cat', $request->field);
                    session()->put('sales-detail-report-field',$request->field  );

                });
            })
            ->when($request->has('category') && $request->category != null,function($query) use($request){
                $query->whereHas('item_details',  function($q) use($request){
                    $q->where('category', $request->category);
                    session()->put('sales-detail-report-category',$request->category);

                });
            })
            ->when($request->has('product') && $request->product != null, function ($query) use ($request) {
                session()->put('sales-detail-report-product', $request->product);
                $query->where('item_id', $request->product);
                session()->put('sales-detail-report-product',$request->product);

            });

            // dd($records->get());

            if ($request->has('type') && $request->type === 'pdf') {

                $records = $records->get();
                $data = [
                    'records' => $records,
                    'from' => $from,
                    'to' => $to,
                ];
                $pdf = PDF::loadView('reports.sales-report.pdf-report2', $data)->setPaper('a4', 'landscape');
                return $pdf->stream();
            } else {

                $records = $records->paginate(20);
                return view('reports.sales-report.report2', compact('records', 'from', 'to', 'products'));
            }
        } catch (\Throwable $th) {
            throw $th;
        
        }
    }

    public function summary(Request $request)
    {
        try {
            //session remove
            session()->forget('sale-summary-report-start-date');
            session()->forget('sale-summary-report-end-date');
            session()->forget('sale-summary-vendor');
            $from = $request->start_date;
            $to = $request->end_date;

            $group = PartyGroups::where('group_name', 'LIKE' ,'customer%')->first();
            $customers = Parties::where('group_id',(isset($group->id) && $group->id) ? $group->id : 0)->get();
            $records  = Parties::where('group_id',(isset($group->id) && $group->id) ? $group->id : 0)
            ->when($request->has('customer') && $request->customer !== null, function($query) use($request){
               session()->put('sale-summary-customer',  $request->customer);
                $query->where('id' , $request->customer);
            })
            ->when(($request->has('start_date') && $request->has('end_date')) 
            && ($request->start_date !== null && $request->end_date !== null) , function($query) use($request) {
                $query->with(['sales' => function($sub) use($request){
                    session()->put('sale-summary-report-start-date', $request->start_date);
                    session()->put('sale-summary-report-end-date',$request->end_date);
                    $sub->whereBetween('created_at' , [$request->start_date , $request->end_date]);
                }]);
            });
            if($request->type == 'pdf'){
                $records = $records->get();
                $data = [
                    'records' => $records,
                    'from' => $from,
                    'to' => $to,
                ];
                $pdf = PDF::loadView('reports.sales-report.pdf-report3', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }

            $records = $records->paginate(10);
            // dd($records);
            return view('reports.sales-report.report3', compact('records' , 'from' , 'to' , 'customers'));
        } catch (\Throwable $th) {
            throw $th;
        }
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
