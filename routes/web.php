<?php

use App\Http\Controllers\ConfigurationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//
Route::prefix('store','App\Http\Controllers\StoresController')->group(function(){
Route::get('/','App\Http\Controllers\StoresController@index');
Route::post('/add','App\Http\Controllers\StoresController@store')->name('add.stores');
Route::put('/edit/{id}','App\Http\Controllers\StoresController@edit')->name('update.stores');
Route::delete('/delete/{id}','App\Http\Controllers\StoresController@destroy')->name('delete.stores');
});

Route::prefix('product-category')->group(function () {
    Route::get('/', 'App\Http\Controllers\ProductCategoryController@index');
    Route::post('/add','App\Http\Controllers\ProductCategoryController@store')->name('add.category');
    Route::put('/edit/{id}','App\Http\Controllers\ProductCategoryController@update')->name('update.category');
    Route::put('/delete/{id}','App\Http\Controllers\ProductCategoryController@destroy')->name('delete.category');
});

Route::prefix('products')->group(function(){
    Route::get('/','App\Http\Controllers\ProductController@index');
    Route::post('/add','App\Http\Controllers\ProductController@store')->name('add.product');
    Route::put('/edit/{id}','App\Http\Controllers\ProductController@update')->name('update.product');
    Route::delete('/delete/{id}','App\Http\Controllers\ProductController@destroy')->name('delete.product');
    Route::post('/restore/{id}','App\Http\Controllers\ProductController@restore')->name('restore.product');
    Route::post('/import-csv','App\Http\Controllers\ProductController@importCsv')->name('import.product');
});

Route::prefix('uom')->group(function(){
    Route::get('/','App\Http\Controllers\MOUController@index');
    Route::post('/add','App\Http\Controllers\MOUController@store')->name('add.uom');
    Route::put('/edit/{id}','App\Http\Controllers\MOUController@update')->name('edit.uom');
    Route::put('/delete/{id}','App\Http\Controllers\MOUController@destroy')->name('delete.uom');
});


Route::prefix('product-arrtribute')->group(function(){
    Route::get('/','App\Http\Controllers\ProductArrtributesController@index');
    Route::post('/add','App\Http\Controllers\ProductArrtributesController@store')->name('add.arrtribute');
    Route::put('/edit/{id}','App\Http\Controllers\ProductArrtributesController@update')->name('edit.arrtribute');
    Route::delete('/delete/{id}','App\Http\Controllers\ProductArrtributesController@destroy')->name('delete.arrtribute');
    Route::put('/restore/{id}','App\Http\Controllers\ProductArrtributesController@destroy')->name('restore.arrtribute');
});


Route::prefix('users')->group(function(){
    Route::get('/','App\Http\Controllers\UsersController@index');
    Route::post('/add','App\Http\Controllers\UsersController@store')->name('add.user');
    Route::put('/edit/{id}','App\Http\Controllers\UsersController@update')->name('edit.user');
    Route::delete('/delete/{id}','App\Http\Controllers\UsersController@destroy')->name('delete.user');
});


Route::prefix('party-groups')->group(function(){
    Route::get('/','App\Http\Controllers\PartyGroupsController@index');
    Route::post('/add','App\Http\Controllers\PartyGroupsController@store')->name('add.partyGroup');
    Route::put('/edit/{id}','App\Http\Controllers\PartyGroupsController@update')->name('edit.partyGroup');
    Route::delete('/delete/{id}','App\Http\Controllers\PartyGroupsController@destroy')->name('delete.partyGroup');
    
});

Route::prefix('parties')->group(function () {
    Route::get('/{group?}','App\Http\Controllers\PartiesController@index');
    Route::post('/add','App\Http\Controllers\PartiesController@store')->name('add.party');
    Route::put('/edit/{id}','App\Http\Controllers\PartiesController@update')->name('edit.party');
    Route::delete('/delete/{id}','App\Http\Controllers\PartiesController@destroy')->name('delete.party');

});


Route::prefix('sales')->group(function () {
    Route::get('/','App\Http\Controllers\SalesController@index');
    Route::get('/add','App\Http\Controllers\SalesController@addNewOrder');
    Route::post('/add','App\Http\Controllers\SalesController@store')->name('add.sale');
    Route::put('/edit/{id?}','App\Http\Controllers\SalesController@update')->name('edit.sale');
    Route::delete('/delete/{id?}','App\Http\Controllers\SalesController@destroy')->name('delete.sale');
});


Route::prefix('purchase')->group(function () {
Route::get('/','App\Http\Controllers\PurchaseRequestController@main');
        Route::resource('/request','App\Http\Controllers\PurchaseRequestController');
});
Route::prefix('invoice')->group(function(){
    Route::get('/thermal/{id}','App\Http\Controllers\SalesController@receipt');
});


Route::prefix('system')->group(function () {
    Route::resource('configurations', ConfigurationController::class);
});