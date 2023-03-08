<?php

namespace App\Http\Controllers;

use App\Models\Stores;
use App\Models\User;
use App\Models\UserRoles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{

    public function index()
    {
        try {
            $users = User::with(['userroles'])->get();
            $user_roles = UserRoles::all();
            // dump($users);
            $stores = Stores::all();
            return view('users.index',compact('users','user_roles','stores'));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function store(Request $request)
    {
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->role_id = $request->role_id;
            $user->phone = $request->phone;
            $user->business_id = $request->business_id;
            $user->save();


            toast('User Added!','success');
            return redirect()->back();

        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function update(int $id, Request $request)
    {
        try {
            $user =  User::find($id);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role_id = $request->role_id;
            $user->business_id = $request->business_id;
            $user->isActive = $request->status;
            $user->save();
            
            toast('User Updated!','info');
            return redirect()->back();
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function destroy(int $id)
    {
        try {
            $user = User::find($id);
            $user->isActive = 0;
            $user->isDeleted = 1;
            $user->save();
            $user->delete();

            
            toast('User Deleted!','error');
            return redirect()->back();


        } catch (\Throwable $th) {
            //throw $th;
        }
    }
}
