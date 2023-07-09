<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class StoreIdObserver
{
    public function created($model){
      $model->store_id = Auth::user()->store_id;
      $model->save();  
    }
}
