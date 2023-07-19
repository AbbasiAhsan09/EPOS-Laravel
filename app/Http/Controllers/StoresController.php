<?php

namespace App\Http\Controllers;

use App\Models\Stores;
use App\Models\User;
use Illuminate\Http\Request;
use Alert;
use App\Models\Configuration;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;

class StoresController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $stores = Stores::with('users')
            ->when($request->filter == 'expired',function($query){
                $query->where('renewal_date','<=',date('Y-m-d'))->orderBy('renewal_date','DESC');
                session()->put('expired',true);
                session()->forget('all');
                session()->forget('running');
                session()->forget('trial');
            })
            ->when($request->filter == 'trial',function($query){
                $query->where('is_trial','=',1)->orderBy('created_at','DESC');
                session()->forget('expired');
                session()->forget('all');
                session()->forget('running');
                session()->put('trial',true);
            })
            ->when($request->filter == 'running',function($query){
                $query->where('renewal_date','>',date('Y-m-d'))->orderBy('renewal_date','DESC');
                session()->forget('expired');
                session()->forget('all');
                session()->put('running',true);
                session()->forget('trial');
            })
            ->when($request->filter == 'all',function($query){
                
                session()->forget('expired');
                session()->put('all',true);
                session()->forget('running');
                session()->forget('trial');
            })
            ->get();



            if(Auth::user()->store_id){
            $stores = Stores::with('users')->where('id',Auth::user()->store_id)->get();
            }
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
                if($request->status == 'trial'){
                    $store->is_trial = 1;
                }else{
                    $store->is_locked = $request->status;
                }
                $store->renewal_date = isEmpty($request->renewal_date) ? null : $request->renewal_date;
                $store->save();

                if($store){
                    Configuration::firstOrCreate([
                        'store_id' => $store->id,
                    ],[
                        'app_title' => $store->store_name,
                        'phone' => $store->store_phone,
                        'address' => $store->store_location,
                        'start_date' => date('Y-m-d',strtotime($store->created_at)),
                        'contract_duration' => 12,
                        'added_by' => Auth::user()->id,
                        'store_id' => $store->id,
                    ]);
                }
                
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
                if($request->status == 'trial'){
                    $store->is_trial = 1;
                }else{
                    $store->is_trial = 0;
                    $store->is_locked = $request->status;
                }
                $store->renewal_date = $request->renewal_date;
              
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
