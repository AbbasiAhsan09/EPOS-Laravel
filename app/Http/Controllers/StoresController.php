<?php

namespace App\Http\Controllers;

use App\Models\Stores;
use App\Models\User;
use Illuminate\Http\Request;
use Alert;

class StoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $stores = Stores::with('users')->get();
            $users = User::paginate(10);
            return view('stores.index',compact('stores','users'));
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
         
            $validation = $request->validate([
                'store_name' => 'required | string',
                'store_phone' => 'required | string',
                'store_location' => 'required | string',
                'type' => 'string',
                'store_supervisor' => 'integer',
            ]);

            if($validation){
                $store = new Stores();
                $store->store_name = $request->store_name;
                $store->store_phone = $request->store_phone;
                $store->store_location = $request->store_location;
                $store->type = $request->type;
                $store->store_supervisor = $request->store_supervisor;
                $store->domain = $request->domain;
                $store->email = $request->email;
                $store->phone = $request->phone;
                $store->save();
                
                 toast('Added New Store','success');
                return Redirect('/store');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stores  $stores
     * @return \Illuminate\Http\Response
     */
    public function show(Stores $stores)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stores  $stores
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id, Request $request)
    {
        try {
            $validation = $request->validate([
                'store_name' => 'required | string',
                'store_phone' => 'required | string',
                'store_location' => 'required | string',
                'type' => 'string',
                'store_supervisor' => 'integer',
            ]);

            if($validation){
                $store =  Stores::find($id);
                $store->store_name = $request->store_name;
                $store->store_phone = $request->store_phone;
                $store->store_location = $request->store_location;
                $store->type = $request->type;
                $store->store_supervisor = $request->store_supervisor;
                $store->domain = $request->domain;
                $store->email = $request->email;
                $store->phone = $request->phone;
                $store->save();
                toast('Updated Store','info');

                return Redirect('/store');
            }
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stores  $stores
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Stores $stores)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stores  $stores
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
       try {
        $store = Stores::find($id);
        if (!is_null($store)) {
            $store->delete();
        }
        return view('store.index');
       } catch (\Throwable $th) {
        throw $th;
       }


    }
}
