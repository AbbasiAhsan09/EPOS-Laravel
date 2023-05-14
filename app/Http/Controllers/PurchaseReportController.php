<?php

namespace App\Http\Controllers;

use App\Models\Parties;
use App\Models\PartyGroups;
use App\Models\Products;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseInvoiceDetails;
use PDF;
use Illuminate\Http\Request;

class PurchaseReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {

            session()->forget('purchase-report-start-date');
            session()->forget('vendor');
            session()->forget('purchase-report-end-date');
            session()->forget('filter_deleted');
            $from = $request->start_date;
            // dd($request->all());
            $to = $request->end_date;
            if ($request->type == 'pdf') {
                $records = PurchaseInvoice::with('created_by_user', 'party', 'order')
                    ->when($request->has('filter_deleted')  && $request->filter_deleted == 'true',  function ($query) {
                        $query->onlyTrashed();
                    })
                    ->when(
                        $request->has('start_date') && $request->has('end_date') &&
                            ($request->start_date != null) && ($request->end_date != null),
                        function ($query) use ($request) {
                            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                        }
                    )->when(
                        $request->has('vendor') && ($request->vendor != null),
                        function ($query) use ($request) {
                            $query->where('party_id', $request->vendor);
                        }
                    )
                    ->get();
                $data = [
                    'records' => $records,
                    'from' => $from,
                    'to' => $to
                ];
                $pdf = PDF::loadView('reports.purchase-report.pdf-report1', $data)->setPaper('a4', 'landscape');
                return $pdf->stream();
            } else {

                $records = PurchaseInvoice::with('created_by_user', 'party', 'order')
                ->when($request->has('filter_deleted') && $request->filter_deleted == 'true',  function ($query) {
                    $query->onlyTrashed();
                    session()->put('filter_deleted', true);
                })
                    ->when(
                        $request->has('start_date') && $request->has('end_date') &&
                            ($request->start_date != null) && ($request->end_date != null),
                        function ($query) use ($request) {
                            session()->put('purchase-report-start-date', $request->start_date);
                            session()->put('purchase-report-end-date',  $request->end_date);
                            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
                        }
                    )
                    ->when(
                        $request->has('vendor') && ($request->vendor != null),
                        function ($query) use ($request) {
                            session()->put('vendor', $request->vendor);
                            $query->where('party_id', $request->vendor);
                        }
                    )
                    ->paginate(20)->withQueryString();
                $group = PartyGroups::where('group_name', 'LIKE',  'vendor%')->first();
                $vendors = Parties::where('group_id', $group->id)->get();

                return view('reports.purchase-report.report1', compact('records', 'from', 'to', 'vendors'));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    public function detail(Request $request)
    {
        try {
            session()->forget('purchase-detail-report-start-date');
            session()->forget('purchase-detail-report-end-date');
            session()->forget('purchase-detail-report-category');
            session()->forget('purchase-detail-report-product');
            $products = Products::orderBy('name', 'ASC')->get();

            $from = $request->has('start_date') ? $request->start_date : null;
            $to = $request->has('end_date') ? $request->end_date : null;

            $records = PurchaseInvoiceDetails::when($from != null && $to != null, function ($query) use ($request) {
                session()->put('purchase-detail-report-start-date', $request->start_date);
                session()->put('purchase-detail-report-end-date', $request->end_date);
                $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
            })->when($request->has('product') && $request->product != null, function ($query) use ($request) {
                session()->put('purchase-detail-report-product', $request->product);
                $query->where('item_id', $request->product);
            });


            if ($request->has('type') && $request->type === 'pdf') {

                $records = $records->get();
                $data = [
                    'records' => $records,
                    'from' => $from,
                    'to' => $to,
                ];
                $pdf = PDF::loadView('reports.purchase-report.pdf-report2', $data)->setPaper('a4', 'landscape');
                return $pdf->stream();
            } else {

                $records = $records->paginate(20);
                return view('reports.purchase-report.report2', compact('records', 'from', 'to', 'products'));
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function summary(Request $request)
    {
        try {
            //session remove
            session()->forget('purchase-summary-report-start-date');
            session()->forget('purchase-summary-report-end-date');
            session()->forget('purchase-summary-vendor');
            $from = $request->start_date;
            $to = $request->end_date;

            $group = PartyGroups::where('group_name', 'LIKE' ,'vendor%')->first();
            $vendors = Parties::where('group_id',(isset($group->id) && $group->id) ? $group->id : 0)->get();
            $records  = Parties::where('group_id',(isset($group->id) && $group->id) ? $group->id : 0)
            ->when($request->has('vendor') && $request->vendor !== null, function($query) use($request){
               session()->put('purchase-summary-vendor',  $request->vendor);
                $query->where('id' , $request->vendor);
            })
            ->when(($request->has('start_date') && $request->has('end_date')) 
            && ($request->start_date !== null && $request->end_date !== null) , function($query) use($request) {
                $query->with(['purchases' => function($sub) use($request){
                    session()->put('purchase-summary-report-start-date', $request->start_date);
                    session()->put('purchase-summary-report-end-date',$request->end_date);
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
                $pdf = PDF::loadView('reports.purchase-report.pdf-report3', $data)->setPaper('a4', 'portrait');
                return $pdf->stream();
            }

            $records = $records->paginate(10);
            // dd($records);
            return view('reports.purchase-report.report3', compact('records' , 'from' , 'to' , 'vendors'));
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
