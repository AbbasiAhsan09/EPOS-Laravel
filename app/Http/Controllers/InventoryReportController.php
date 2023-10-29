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
                ->byUser()
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
