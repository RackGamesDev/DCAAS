<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Casts\PermisosUsuarioCast;
use Illuminate\Support\Facades\Eloquent;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //Eloquent::cast('permisos', PermisosUsuarioCast::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
