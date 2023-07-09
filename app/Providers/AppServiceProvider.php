<?php

namespace App\Providers;

use App\Models\Products;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseQuotation;
use App\Models\Sales;
use App\Observers\StoreIdObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Products::observe(StoreIdObserver::class);
        Sales::observe(StoreIdObserver::class);
        PurchaseOrder::observe(StoreIdObserver::class);
        PurchaseInvoice::observe(StoreIdObserver::class);
        PurchaseQuotation::observe(StoreIdObserver::class);
           
    }
}
