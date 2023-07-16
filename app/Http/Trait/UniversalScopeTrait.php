<?php
namespace App\Http\Trait;

use Illuminate\Support\Facades\Auth;

trait UniversalScopeTrait
{
    public function scopeFilterByStore($query) {
        $store_id = Auth::user()->store_id;
        if($store_id!=null){
            return $query->where('store_id' , $store_id);
        }
        return $query;
    }

    public function scopeByUser($query){
       if (Auth::check()) {
        $user = Auth::user();
        $role = Auth::user()->userroles->role_name;
        // dd($role);
        if ($role === 'SuperAdmin'){
            return $query;
        } else if($role === 'Admin' || $role === 'Manager'){
            return $query->where('store_id' ,$user->store_id);
        }
        return $query->where('user_id' ,$user->id);
       }
    }
}
