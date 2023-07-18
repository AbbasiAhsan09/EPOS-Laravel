<?php

namespace App\Http\Controllers;

use App\Http\Trait\UniversalScopeTrait;
use App\Models\Configuration;
use App\Models\Stores;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterStoreController extends Controller
{
    use UniversalScopeTrait;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('registration.register');
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
        $request->validate([
            'email' => 'email | required | max:255|unique:users,email,NULL,id,deleted_at,NULL',
            'password' => 'required | confirmed | max:15 | min:8',
            'password_confirmation' => 'required',
            'name' => 'required | max:100',
            'business' => 'required | max:100',
            'company_size' => 'required',
            'plan' => 'required | string ',
            'phone' => 'required | string',
            'terms' => 'required'
        ]);
        
        $is_trial = $request->plan == 'trial' ? true : false;
        $store =  Stores::create([
        'store_name' => $request->business, 
        'store_phone' => $request->phone, 
        'business_size' => $request->company_size,
        'is_trial'  => $is_trial,
        'store_location' => 'unknown',
        'store_supervisor' =>1, 
        'type' => 'site',
        'domain' => 'www.demo.etc',
        'email' => $request->email,
        'phone' => $request->phone,
        'is_locked' => (!$is_trial ? true : false),
        ]);

        if($store->id){  
        $user = User::create(['email' => $request->email, 'role_id' => 2 ,'password' => Hash::make($request->password), 'name' => $request->name]);
          $con= Configuration::create([
                'app_title' => $store->store_name,
                'phone' => $store->store_phone,
                'address' => $store->store_location,
                'start_date' => date('Y-m-d',strtotime($store->created_at)),
                'contract_duration' => 12,
                'added_by' => $user->id,
                'store_id' => $user->id,
            ]);
            $store->update([
                'store_supervisor' => $user->id, 
            ]);
            $con->update(['store_id' => $store->id]);
            $user->update(['store_id' => $store->id]);
            
        
      
        }
        Auth::attempt(['email' => $user->email, 'password' => $request->password]);
        if($is_trial){
            return redirect('/');
        }
        $store->update(['is_locked' => true]);
        return redirect('/payment');
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
