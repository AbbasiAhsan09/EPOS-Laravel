<?php

namespace App\Http\Controllers;

use App\Models\ArrtributeValue;
use App\Models\ProductArrtributes;
use Illuminate\Http\Request;

class ProductArrtributesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = ProductArrtributes::all();
        return view('arrtributes.index',compact('items'));
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
            $arrtribute = new ProductArrtributes();
            $arrtribute->arrtribute = $request->arrtribute;
            $arrtribute->save();

            for ($i=0; $i < count($request->value); $i++) { 
              $value = new ArrtributeValue();
              $value->art_id  = $arrtribute->id;
              $value->value_name = $request->value[$i];
              $value->save();
            }

            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ProductArrtributes  $productArrtributes
     * @return \Illuminate\Http\Response
     */
    public function show(ProductArrtributes $productArrtributes)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ProductArrtributes  $productArrtributes
     * @return \Illuminate\Http\Response
     */
    public function edit(ProductArrtributes $productArrtributes)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ProductArrtributes  $productArrtributes
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ProductArrtributes $productArrtributes)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ProductArrtributes  $productArrtributes
     * @return \Illuminate\Http\Response
     */
    public function destroy(ProductArrtributes $productArrtributes)
    {
        //
    }
}
