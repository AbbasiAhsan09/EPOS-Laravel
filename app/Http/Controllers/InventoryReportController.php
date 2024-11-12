<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Products;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            session()->forget('inventory_report_name');
            session()->forget('type');
            session()->forget('inventory_filterBy');
            session()->forget('inventory-report-field');
            session()->forget('inventory-report-category');
            session()->forget('inventory-report-product');
            
            $records = Products::select(
                '*',
                'products.name',
                'products.tp',
                'product_categories.category',
                'mou.uom',
                'mou.base_unit',
                'mou.base_unit_value',
                'fields.name as field'
            )
                ->leftJoin('inventories', 'inventories.item_id', '=', 'products.id')
                ->join('product_categories', 'products.category', '=', 'product_categories.id')
                ->join('fields' , 'product_categories.parent_cat' , '=' ,'fields.id')
                ->leftJoin('mou', 'products.uom', '=', 'mou.id')
                ->orderBy('product_categories.category')
                ->orderBy('products.name')
                ->where('products.store_id' , Auth::user()->store_id ?? 0)
                // ->byUser()
                ->when(($request->has('name') && $request->name != null)
                    ,function ($query) use ($request) {
                       $query->where('products.name', 'LIKE', '%'.$request->name.'%')
                       ->orWhere(function($qr) use($request){
                        $qr->where('product_categories.category', 'LIKE', '%'.$request->name.'%');
                       })
                       ->orWhere(function($qp) use($request){
                        $qp->where('fields.name', 'LIKE', '%'.$request->name.'%');
                       });
                       session()->put('inventory_report_name', $request->name);
                })
                ->when($request->has('field') && $request->field != null, function($query) use ($request){
                        $query->where('fields.id', $request->field);
                        session()->put('inventory-report-field', $request->field);

                })
                ->when($request->has('category') && $request->category != null, function($query) use ($request){
                    $query->where('product_categories.id', $request->category);
                    session()->put('inventory-report-category', $request->category);

                })
                ->when($request->has('product') && $request->product != null, function($query) use ($request){
                    $query->where('products.id', $request->product);
                    session()->put('inventory-report-product', $request->product);

                })
                ->when($request->has('filterBy') && $request->filterBy == 'lowStock', function($query) use($request){
                    $query->where(DB::raw('inventories.stock_qty / IFNULL(mou.base_unit_value,1)'), '<=', DB::raw('products.low_stock'));
                    session()->put('inventory_filterBy', true);
                });
                
                if ($request->type === 'pdf') {
                    $records = $records->get();
                    $data = [
                        'records' => $records,
                        'report_title' => 'Inventory Balance Report'
                    ];
                    $pdf = Pdf::loadView('reports.inventory-report.pdf-report1', $data)->setPaper('a4', 'landscape');
                    return $pdf->stream();
                } else {
                    $records = $records->paginate(20)->withQueryString();
                    return view('reports.inventory-report.report1', compact('records'));
                }
            // dd(collect($records));

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function real_inventory(Request $request) {
        try {
            $results = DB::table('products as p')
                ->selectRaw("
                    p.name,
                    p.id AS item_id,
                    fields.name as field,
                    p.low_stock as low_stock_indicator,
                    product_categories.category as category,
                    COALESCE(pid.total_qty, 0) AS purchased_qty,
                    COALESCE(p.opening_stock, 0) AS opening_qty,
                    COALESCE(sd.total_qty, 0) AS sold_qty,
                    COALESCE(srd.total_returned_qty, 0) AS sold_returned_qty,
                    COALESCE(prd.total_preturned_qty, 0) AS purchase_return_qty,
                    (COALESCE(pid.total_qty, 0) + COALESCE(srd.total_returned_qty, 0) + COALESCE(p.opening_stock, 0)) - 
                    (COALESCE(sd.total_qty, 0) + COALESCE(prd.total_preturned_qty, 0)) AS avl_qty,
                    CASE 
                        WHEN (COALESCE(pid.total_qty, 0) + COALESCE(p.opening_stock, 0)) = 0 THEN 0
                        ELSE ROUND(
                            (COALESCE(pid.total_value, 0) + COALESCE(p.opening_stock * p.opening_stock_unit_cost, 0)) 
                            / (COALESCE(pid.total_qty, 0) + COALESCE(p.opening_stock, 0)), 
                            2
                        )
                    END AS avg_rate
                ")
                ->leftJoin('product_categories', 'p.category', '=', 'product_categories.id')
                ->leftJoin('fields' , 'product_categories.parent_cat' , '=' ,'fields.id')
                ->leftJoin(DB::raw("(SELECT item_id, SUM(qty) AS total_qty, SUM(rate * qty) AS total_value FROM purchase_invoice_details WHERE deleted_at IS NULL GROUP BY item_id) AS pid"), 'p.id', '=', 'pid.item_id')
                ->leftJoin(DB::raw("(SELECT item_id, SUM(qty) AS total_qty FROM sales_details WHERE deleted_at IS NULL GROUP BY item_id) AS sd"), 'p.id', '=', 'sd.item_id')
                ->leftJoin(DB::raw("(SELECT item_id, SUM(returned_qty) AS total_returned_qty FROM sale_return_details WHERE deleted_at IS NULL GROUP BY item_id) AS srd"), 'p.id', '=', 'srd.item_id')
                ->leftJoin(DB::raw("(SELECT item_id, SUM(returned_qty) AS total_preturned_qty FROM purchase_return_details WHERE deleted_at IS NULL GROUP BY item_id) AS prd"), 'p.id', '=', 'prd.item_id')
                ->whereNull('p.deleted_at')
                ->where('p.store_id', Auth::user()->store_id)
                ->groupBy('p.name', 'p.id')->orderBy('p.name');
                if($request->has('name') && !empty($request->name)){
                    $results = $results->where(function($query) use ($request){
                        $query->where("p.name",'LIKE',"%".$request->name."%")
                        ->orWhere("fields.name",'LIKE',"%".$request->name."%")
                        ->orWhere("product_categories.category",'LIKE',"%".$request->name."%");
                    });
                }

                if($request->has('field') && !empty($request->field)){
                    $results = $results->where("product_categories.parent_cat",$request->field);
                }

                if($request->has('category') && !empty($request->category)){
                    $results = $results->where("p.category",$request->category);
                }
                

                if($request->has('product') && !empty($request->product)){
                    $results = $results->where("p.id",$request->product);
                }

                // if($request->has('filterBy') && $request->input('filterBy') == 'lowStock' ){
                //     // dd("test");
                //     $resulst = $results->where(DB::raw('p.low_stock <= (COALESCE(pid.total_qty, 0) + COALESCE(srd.total_returned_qty, 0) + COALESCE(p.opening_stock, 0)) - 
                //     (COALESCE(sd.total_qty, 0) + COALESCE(prd.total_preturned_qty, 0))'));
                // }
                if($request->type === 'pdf'){
                    $results = $results->get();
                    $data = [
                        'results' => $results,
                        'report_title' => 'Inventory Balance Report'
                    ];
                    $pdf = Pdf::loadView('reports.inventory-report.pdf_real_inventory', $data)->setPaper('a4', 'landscape');
                    return $pdf->stream();
                }
                $results = $results->paginate(20)->withQueryString(); // Paginate with 10 items per page

                return view("reports.inventory-report.real_inventory", compact("results"));
            

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
