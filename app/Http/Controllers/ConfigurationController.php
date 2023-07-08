<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ConfigurationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       try {
        $currenConfig = Configuration::latest()->first();
        // dd($currenConfig);
        return view('configuration.index',compact('currenConfig'));
       } catch (\Throwable $th) {
        //throw $th;
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
        try {
            $validate = $request->validate([
                'business' => 'required | string'
            ]);

            if($validate){
                $config = Configuration::latest()->first();
                $config->app_title  = $request->business;
                if($request->file('logo')){
                    $file = $request->file('logo');
                    $ext = $file->getClientOriginalName();
                    $rename_file = time().''.$ext;
                    $file->move(public_path('images/logo/'),$rename_file);
                    $config->logo = $rename_file;
                }
                $config->phone  =  $request->phone;
                $config->address = $request->address;
                $config->invoice_message = $request->inv_message;
                $config->ntn = $request->ntn;
                $config->ptn = $request->ptn;
                $config->invoice_type = $request->invoice_type;
                $config->invoice_template = $request->invoice_template;
                $config->search_filter = $request->search_filter;
                $config->show_ntn = ($request->has('show_ntn') ? $request->show_ntn : false);
                $config->show_ptn = ($request->has('show_ptn') ? $request->show_ptn : false);
                $config->mutltiple_sales_order = ($request->has('is_multi_order') ? $request->is_multi_order : false);
                $config->inventory_tracking = ($request->has('track_inventory') ? $request->track_inventory : false);
                $config->save();
                Alert::toast('Configurations Updated!','success');
                return redirect('/system/configurations');
            }
            

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function show(Configuration $configuration)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function edit(Configuration $configuration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Configuration $configuration)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Configuration  $configuration
     * @return \Illuminate\Http\Response
     */
    public function destroy(Configuration $configuration)
    {
        //
    }
}
