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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
