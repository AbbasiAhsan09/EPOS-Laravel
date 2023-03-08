<?php

namespace App\Http\Controllers;

use App\Models\MOU;
use Illuminate\Http\Request;

class MOUController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = MOU::all();
        return view('uom.index',compact('items'));
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
        $uom = new MOU();
        $uom->base_unit = $request->base_unit;
        $uom->base_unit_value = $request->base_unit_value;
        $uom->uom = $request->uom;
        $uom->save();

        toast('UOM Added','success');
        return redirect()->back();
        
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\MOU  $mOU
     * @return \Illuminate\Http\Response
     */
    public function show(MOU $mOU)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\MOU  $mOU
     * @return \Illuminate\Http\Response
     */
    public function edit(MOU $mOU)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\MOU  $mOU
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request, MOU $mOU)
    {
        $uom =  MOU::find($id);
        $uom->base_unit = $request->base_unit;
        $uom->base_unit_value = $request->base_unit_value;
        $uom->uom = $request->uom;
        $uom->save();
        toast('UOM Updated','info');

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\MOU  $mOU
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id, MOU $mOU)
    {
        try {
            $item = MOU::find($id);
            if($item){
                $item->delete();
                toast("Deleted $item->mou",'error');
                
            }else{
                toast("Error occured while deleting $item->mou",'error');
            }

            return redirect()->back();

        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
