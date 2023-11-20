<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class ProfileController extends Controller
{
    function index()  {
        try {
            return view('profile.index');
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    function updatePassword(Request $request) {
        try {
            $this->validate($request, [
                'current_password' => 'required|string',
                'new_password' => 'required|confirmed|min:8|string'
            ]);
            $auth = Auth::user();
     
     // The passwords matches
            if (!Hash::check($request->get('current_password'), $auth->password)) 
            {
                Alert::toast("Current Password is Invalid", 'error');
                return back()->with('error', "Current Password is Invalid");
            }
     
    // Current password and new password same
            if (strcmp($request->get('current_password'), $request->new_password) == 0) 
            {
                Alert::toast("New Password cannot be same as your current password", 'error');
                return redirect()->back()->with("error", "New Password cannot be same as your current password.");
            }
     
            $user =  User::find($auth->id);
            $user->password =  Hash::make($request->new_password);
            $user->save();
            Alert::toast("Password Changed Successfully", 'success');
            return back()->with('success', "Password Changed Successfully");
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
