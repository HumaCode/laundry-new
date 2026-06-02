<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\OutletRepositoryInterface::class,
            \App\Repositories\Eloquent\OutletRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\EmployeeRepositoryInterface::class,
            \App\Repositories\Eloquent\EmployeeRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\BusinessRepositoryInterface::class,
            \App\Repositories\Eloquent\BusinessRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\CustomerRepositoryInterface::class,
            \App\Repositories\Eloquent\CustomerRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\OrderRepositoryInterface::class,
            \App\Repositories\Eloquent\OrderRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\ServiceRepositoryInterface::class,
            \App\Repositories\Eloquent\ServiceRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\PickupRepositoryInterface::class,
            \App\Repositories\Eloquent\PickupRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\InventoryRepositoryInterface::class,
            \App\Repositories\Eloquent\InventoryRepository::class
        );
        $this->app->bind(
            \App\Repositories\Contracts\LaporanRepositoryInterface::class,
            \App\Repositories\Eloquent\LaporanRepository::class
        );
        $this->app->bind(
            \App\Services\Contracts\LaporanServiceInterface::class,
            \App\Services\LaporanService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
