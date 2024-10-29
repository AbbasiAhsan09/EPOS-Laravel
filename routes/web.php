<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountingReportController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\CustomerLedgerController;
use App\Http\Controllers\FieldsController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryReportController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseReportController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\RegisterStoreController;
use App\Http\Controllers\SaleReturnController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\SendBackupToMailController;
use App\Http\Controllers\VendorLedgerController;
use App\Http\Controllers\VoucherController;
use App\Models\Voucher;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
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



Auth::routes();

Route::resource('/register',RegisterStoreController::class);
Route::get('/optimize', function() {
    Artisan::call('optimize');
    return 'Application optimized successfully!';
});
Route::get('/payment',[PaymentMethodController::class, 'index']);
Route::get('/check-order',[SalesController::class, 'showOrderDetailsClient']);
Route::get('/check-status',[SalesController::class, 'cliendCheckStatusView']);

Route::middleware(['auth','is_trial.check'])->group(function () {
Route::get('logout-auth', function(){
     Auth::logout();
     return redirect()->route('login');
})->name('auth.logout');




Route::middleware('manager.role')->prefix('charts')->group(function(){
    Route::get('weekly-sales', [HomeController::class,'weeklySales']);
    Route::get('monthly-sales', [HomeController::class, 'monthlySales']);
    Route::get('monthly-purchases', [HomeController::class, 'purchaseMonthlySales']);
});

Route::middleware('manager.role')->get('/',  [App\Http\Controllers\HomeController::class, 'index']);
Route::middleware('manager.role')->get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
//
Route::middleware('super.role')->prefix('store','App\Http\Controllers\StoresController')->group(function(){
Route::get('/','App\Http\Controllers\StoresController@index');
Route::post('/add','App\Http\Controllers\StoresController@store')->name('add.stores');
Route::put('/edit/{id}','App\Http\Controllers\StoresController@edit')->name('update.stores');
Route::middleware('admin.role')->delete('/delete/{id}','App\Http\Controllers\StoresController@destroy')->name('delete.stores');
});

Route::middleware('manager.role')->prefix('product-category')->group(function () {
    Route::get('/', 'App\Http\Controllers\ProductCategoryController@index');
    Route::post('/add','App\Http\Controllers\ProductCategoryController@store')->name('add.category');
    Route::put('/edit/{id}','App\Http\Controllers\ProductCategoryController@update')->name('update.category');
    Route::middleware('admin.role')->put('/delete/{id}','App\Http\Controllers\ProductCategoryController@destroy')->name('delete.category');
});

Route::middleware('manager.role')->prefix('products')->group(function(){
    Route::get('/','App\Http\Controllers\ProductController@index');
    Route::post('/add','App\Http\Controllers\ProductController@store')->name('add.product');
    Route::middleware('admin.role')->put('/edit/{id}','App\Http\Controllers\ProductController@update')->name('update.product');
    Route::middleware('admin.role')->delete('/delete/{id}','App\Http\Controllers\ProductController@destroy')->name('delete.product');
    Route::middleware('admin.role')->post('/restore/{id}','App\Http\Controllers\ProductController@restore')->name('restore.product');
    Route::middleware('admin.role')->post('/import-csv','App\Http\Controllers\ProductController@importCsv')->name('import.product');
});

Route::middleware('manager.role')->prefix('uom')->group(function(){
    Route::get('/','App\Http\Controllers\MOUController@index');
    Route::post('/add','App\Http\Controllers\MOUController@store')->name('add.uom');
    Route::put('/edit/{id}','App\Http\Controllers\MOUController@update')->name('edit.uom');
    Route::middleware('admin.role')->put('/delete/{id}','App\Http\Controllers\MOUController@destroy')->name('delete.uom');
});

Route::middleware('manager.role')->prefix("account")->group(function(){
    Route::get("/",[AccountController::class, 'index']);
    Route::prefix('/report')->group(function(){
        Route::get('/trial-balance',[AccountController::class, 'trial_balance_report']);
        Route::get('/general-ledger',[AccountController::class, 'general_ledger_report']);
    });
    Route::post('/',[AccountController::class, 'store'])->name('account.add');
    Route::get('/journal',[AccountController::class, 'journal'])->name('journal.index');
    Route::post('/journal',[AccountController::class, 'journal_post'])->name('journal.post');
    Route::delete('/journal/{id}',[AccountController::class, 'transaction_destroy'])->name('journal.delete');
    Route::get('/transactions',[AccountController::class, 'journal_entries'])->name('account.transactions');
    Route::get('/general-ledger',[AccountController::class,'generate_sales_ledger_report']);
    Route::get('/generate-coa',[AccountController::class, 'generate_coa']);
    Route::put('/{id}',[AccountController::class, 'update'])->name('account.update');
});


Route::middleware('manager.role')->prefix('product-arrtribute')->group(function(){
    Route::get('/','App\Http\Controllers\ProductArrtributesController@index');
    Route::post('/add','App\Http\Controllers\ProductArrtributesController@store')->name('add.arrtribute');
    Route::put('/edit/{id}','App\Http\Controllers\ProductArrtributesController@update')->name('edit.arrtribute');
    Route::middleware('admin.role')->delete('/delete/{id}','App\Http\Controllers\ProductArrtributesController@destroy')->name('delete.arrtribute');
    Route::put('/restore/{id}','App\Http\Controllers\ProductArrtributesController@destroy')->name('restore.arrtribute');
});


Route::middleware('manager.role')->prefix('users')->group(function(){
    Route::get('/','App\Http\Controllers\UsersController@index');
    Route::post('/add','App\Http\Controllers\UsersController@store')->name('add.user');
    Route::put('/edit/{id}','App\Http\Controllers\UsersController@update')->name('edit.user');
    Route::middleware('admin.role')->delete('/delete/{id}','App\Http\Controllers\UsersController@destroy')->name('delete.user');
});


Route::middleware('manager.role')->prefix('party-groups')->group(function(){
    Route::get('/','App\Http\Controllers\PartyGroupsController@index');
    Route::post('/add','App\Http\Controllers\PartyGroupsController@store')->name('add.partyGroup');
    Route::put('/edit/{id}','App\Http\Controllers\PartyGroupsController@update')->name('edit.partyGroup');
    Route::middleware('admin.role')->delete('/delete/{id}','App\Http\Controllers\PartyGroupsController@destroy')->name('delete.partyGroup');
    
});

Route::middleware('manager.role')->prefix('parties')->group(function () {
    Route::get('/{group?}','App\Http\Controllers\PartiesController@index')->name("parties.index");
    Route::post('import-csv','App\Http\Controllers\PartiesController@importCSV')->name('parties.importCSV');
    Route::post('/add','App\Http\Controllers\PartiesController@store')->name('add.party');
    Route::put('/edit/{id}','App\Http\Controllers\PartiesController@update')->name('edit.party');
    Route::middleware('admin.role')->delete('/delete/{id}','App\Http\Controllers\PartiesController@destroy')->name('delete.party');

});




Route::prefix('sales')->group(function () {
    Route::get('/','App\Http\Controllers\SalesController@index');
    Route::get('/add','App\Http\Controllers\SalesController@addNewOrder');
    Route::get('change-status','App\Http\Controllers\SalesController@changeOrderStatus');
    Route::middleware('manager.role')->get('/edit/{id}','App\Http\Controllers\SalesController@edit');
    Route::post('/add','App\Http\Controllers\SalesController@store')->name('add.sale');
    Route::put('/edit/{id?}','App\Http\Controllers\SalesController@update')->name('edit.sale');
    Route::middleware('manager.role')->delete('/delete/{id?}','App\Http\Controllers\SalesController@destroy')->name('delete.sale');
    Route::get('return/{id?}',[SaleReturnController::class, 'create_update_sales_return']);
    Route::post('return',[SaleReturnController::class, 'store'])->name("add.return");
    Route::put('return/{id}',[SaleReturnController::class, 'update'])->name("update.return");
    Route::delete('return/{id}',[SaleReturnController::class, 'destroy'])->name("delete.return");
    Route::get('returns',[SaleReturnController::class, 'index'])->name("index.return");
    
});


Route::middleware('manager.role')->prefix('purchase')->group(function () {
Route::get('/','App\Http\Controllers\PurchaseRequestController@main');
        Route::resource('/request','App\Http\Controllers\PurchaseRequestController');
        Route::resource('/quotation','App\Http\Controllers\PurchaseQuotationController');
        Route::resource('/order','App\Http\Controllers\PurchaseOrderController');
        Route::resource('/invoice','App\Http\Controllers\PurchaseInvoiceController');
        Route::get("order/print/{id}",'App\Http\Controllers\PurchaseOrderController@print_invoice');
        Route::get("invoice/print/{id}",'App\Http\Controllers\PurchaseInvoiceController@print_invoice');
        Route::get('/invoice/{id}/create','App\Http\Controllers\PurchaseInvoiceController@create_inv');
        // Purchase return routes
        Route::get('return/{id?}',[PurchaseReturnController::class, 'create_update_purchase_return']);
        Route::get('returns',[PurchaseReturnController::class, 'index'])->name('index.purchase_return');
        Route::post('return',[PurchaseReturnController::class, 'store'])->name("add.purchase_return");
        Route::put('return/{id}',[PurchaseReturnController::class, 'update'])->name("update.purchase_return");
        Route::delete('return/{id}',[PurchaseReturnController::class, 'destroy'])->name("delete.purchase_return");
});

Route::middleware('manager.role')->prefix('reports')->group(function(){
    Route::get('/', [HomeController::class , 'reports']);
    Route::resources([
        'sales-report' => SalesReportController::class,
        'purchase-report' => PurchaseReportController::class,
        'inventory-report' => InventoryReportController::class,
    ]);
    
    Route::prefix('accounting')->group(function () {
        Route::get("customer-payments",[AccountingReportController::class,'customer_payments']);
    });
    Route::get('purchase-detail-report', [PurchaseReportController::class, 'detail'])->name('purchase-report.detail');
    Route::get('purchase-summary-report', [PurchaseReportController::class, 'summary'])->name('purchase-report.summary');
    Route::get('sales-summary-report', [SalesReportController::class, 'summary'])->name('sales-report.summary');
    Route::get('sales-detail-report', [SalesReportController::class, 'detail'])->name('sales-report.detail');
});


Route::middleware('admin.role')->prefix('voucher-type')->group(function(){
    Route::get("generate",[VoucherController::class,'generate_voucher_types']);
});


Route::middleware("manager.role")->prefix("voucher")->group(function(){
    Route::get("/",[VoucherController::class, 'index'])->name("voucher.index");
    Route::get("/create/{voucher_type_id}/{id?}",[VoucherController::class,'create']);
    Route::post("/store",[VoucherController::class, 'store'])->name("voucher.store");
    Route::put("/update/{id}",[VoucherController::class, 'update'])->name("voucher.update");
    Route::delete('/{id}',[VoucherController::class,'destroy'])->name("voucher.delete");
});
Route::get('/ledgers',[CustomerLedgerController::class,'main']);
Route::prefix('invoice')->group(function(){
    Route::get("/return/{id}",[SaleReturnController::class,'invoice'])->name("return.print");
    Route::get('/{id}','App\Http\Controllers\SalesController@receipt');
});
Route::prefix('challan')->group(function(){
    Route::get('/{id}',[SalesController::class, 'printChallan']);
});

Route::get("profile", [ProfileController::class, 'index']);
Route::post("profile/change-password", [ProfileController::class, 'updatePassword'])->name('password.change');
Route::middleware('manager.role')->resource('fields', FieldsController::class);
Route::middleware('manager.role')->prefix('system')->group(function () {
    Route::resource('configurations', ConfigurationController::class);
    
});

Route::get('/phpinfo', function () {
    phpinfo();
});
 
Route::resources([
    'customer-ledger' => CustomerLedgerController::class,
    'vendor-ledger'  => VendorLedgerController::class,
]);

Route::get('db-backup', [SendBackupToMailController::class, 'DbBackup']);

Route::get('check-inventory/{item_id}/{is_base_unit}', [InventoryController::class , 'checkInventory'])->name('check.inventory');
});