<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\PartiesController;
use App\Http\Controllers\PurchaseInvoiceController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\UnitController;
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



Route::post('sales/order', [SalesController::class, 'get_order_details'])->middleware("throttle:1000000:1");
Route::post('purchase/invoice', [PurchaseInvoiceController::class, 'get_invoice_details'])->middleware("throttle:1000000:1");
Route::get('items/{exact}/{param}/{storeId}', 'App\Http\Controllers\ProductController@getProductApi')->middleware("throttle:1000000:1");
Route::get('party/balance/{party_id}', [PartiesController::class , 'get_party_balance'])->middleware("throttle:1000000:1");
Route::get('account/coa/{coa_id}/{store_id}', [AccountController::class , 'get_heads_by_coa'])->middleware("throttle:1000000:1");
Route::get('account/{accountId}/{storeId}', [AccountController::class , 'account_details'])->middleware("throttle:1000000:1");
Route::get('units/{unitTypeId}/{storeId}', [UnitController::class , 'get_units_by_unit_type'])->middleware("throttle:1000000:1");