<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;

class AddedByObserve
{
    public function created($model) {
        $model->user_id = Auth::user()->id;
        $model->save();
    }
}
