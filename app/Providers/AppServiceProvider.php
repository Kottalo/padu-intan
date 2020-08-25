<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\View\Components\ConfirmModal;

use App\Models\{ Order,OrderItem };
use App\Observers\{ OrderItemObserver, OrderObserver };

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
        Blade::component('confirm-modal', ConfirmModal::class);
        OrderItem::observe(OrderItemObserver::class);
        Order::observe(OrderObserver::class);
    }
}
