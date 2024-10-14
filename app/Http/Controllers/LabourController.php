<?php

namespace App\Http\Controllers;

use App\Models\Labour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LabourController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $items = Labour::filterByStore()->orderBy("name",'ASC');
        
        if($request->has('search') && $request->search){
            $items = $items->where("name",'LIKE','%'.$request->search.'%')
            ->orWhere("phone",'LIKE','%'.$request->search.'%')
            ->orWhere("address",'LIKE','%'.$request->search.'%');
        }


        $items = $items->paginate(20);

        return view("labour.index", compact('items'));
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
            $request->validate([
                'name' => "required"
            ]);
    
            $input = $request->all();
            $input["store_id"] = Auth::user()->store_id;

            // dd($input);
            DB::beginTransaction();
            $labour = Labour::create($input);
    
            if(!$labour){
                DB::rollBack();
                toast("Failed to add new labour",'error');
                return redirect()->back();
            }

            DB::commit();
            toast('Labour : '.$labour->name." added successfully", 'success');
            return redirect()->back();

        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Labour  $labour
     * @return \Illuminate\Http\Response
     */
    public function show(Labour $labour)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Labour  $labour
     * @return \Illuminate\Http\Response
     */
    public function edit(Labour $labour)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Labour  $labour
     * @return \Illuminate\Http\Response
     */
    public function update(int $id, Request $request)
    {
        try {
            
            $request->validate([
                'name' => 'required'
            ]);

            $labour = Labour::where("id",$id)->filterByStore()->first();

            if(!$labour){
                toast("Labour does not exist",'error');
                return redirect()->back();
            }

            $input = $request->all();
            $input["store_id"] = Auth::user()->store_id;
            
            $labour->update($input);


            toast("Labour updated successfully",'success');
            return redirect()->back();

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Labour  $labour
     * @return \Illuminate\Http\Response
     */
    public function destroy(Labour $labour)
    {
        //
    }
}
