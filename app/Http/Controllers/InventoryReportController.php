<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\Products;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
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
            $records = Products::select(
                '*',
                'products.name',
                'product_categories.category',
                'mou.uom',
                'mou.base_unit',
                'mou.base_unit_value'
            )
                ->leftJoin('inventories', 'inventories.item_id', '=', 'products.id')
                ->join('product_categories', 'products.category', '=', 'product_categories.id')
                ->leftJoin('mou', 'products.uom', '=', 'mou.id')
                ->orderBy('product_categories.category')
                ->orderBy('products.name')
                ->when(($request->has('name') && $request->name != null)
                    ,function ($query) use ($request) {
                       $query->where('products.name', 'LIKE', '%'.$request->name.'%')->orWhere(function($qr) use($request){
                        $qr->where('product_categories.category', 'LIKE', '%'.$request->name.'%');
                        session()->put('inventory_report_name', $request->name);
                       });
                })
                
                ->when($request->has('filterBy') && $request->filterBy == 'lowStock', function($query) use($request){
                    $query->where('inventories.stock_qty', '=<', DB::raw('products.low_stock * mou.base_unit_value'));
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
