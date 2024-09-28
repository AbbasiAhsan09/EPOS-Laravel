<?php

namespace App\Http\Controllers;

use App\Models\Fields;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class FieldsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $fields = Fields::byUser()->paginate(20);
        return view('fields.fields-view', compact('fields'));
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
        try {
            Fields::create(['name' => $request->field]);
            Alert::toast('Field Added!', 'success');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Fields  $fields
     * @return \Illuminate\Http\Response
     */
    public function show(Fields $fields)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Fields  $fields
     * @return \Illuminate\Http\Response
     */
    public function edit(Fields $fields)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Fields  $fields
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $field = Fields::where('id',$id)->byUser()->first();
            $field->update(['name' => $request->field]);
            Alert::toast('Field Updated!', 'info');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Fields  $fields
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $field = Fields::where('id',$id)->byUser()->first();

            $categories = ProductCategory::where("parent_cat",$field->id)->count();

            if($categories){
                toast('You cannot delete this category because it has ('.$categories.') active categories', 'error');
                
                return redirect()->back();
            }

            $field->delete();
            Alert::toast('Field Deleted!', 'success');
            return redirect()->back();
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
