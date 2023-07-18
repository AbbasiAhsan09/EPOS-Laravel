<?php

namespace App\Providers;

use App\Models\Configuration;
use App\Models\Fields;
use App\Models\MOU;
use App\Models\Parties;
use App\Models\ProductCategory;
use App\Models\Products;
use App\Models\PurchaseInvoice;
use App\Models\PurchaseOrder;
use App\Models\PurchaseQuotation;
use App\Models\Sales;
use App\Models\User;
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
        Parties::observe(StoreIdObserver::class);
        ProductCategory::observe(StoreIdObserver::class);
        Fields::observe(StoreIdObserver::class);
        MOU::observe(StoreIdObserver::class);
        User::observe(StoreIdObserver::class);
        Configuration::observe(StoreIdObserver::class);
    }
}
