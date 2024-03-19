<?php

namespace App\Helpers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;


class ConfigHelper {
    
static function getStoreConfig(){
    try {
            $userId = Auth::user()->id;
            $user = User::find($userId);
                
            if($userId && $user){
                
                return $user->storeConfig ? 
                $user->storeConfig->toArray() 
                : [
                    "symbol" => "Rs.",
                    "app_title" => "TradeWise Super Account"
                ];
            }

            return null;

        } catch (\Throwable $th) {
            throw $th;
        }    
    }

}

?>