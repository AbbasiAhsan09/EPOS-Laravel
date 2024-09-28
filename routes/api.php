<?php

use App\Http\Controllers\AccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



Route::get('items/{exact}/{param}/{storeId}', 'App\Http\Controllers\ProductController@getProductApi')->middleware("throttle:1000000:1");
Route::get('account/coa/{coa_id}/{store_id}', [AccountController::class , 'get_heads_by_coa'])->middleware("throttle:1000000:1");
Route::get('account/{accountId}/{storeId}', [AccountController::class , 'account_details'])->middleware("throttle:1000000:1");