<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
    public function boot(): void
{
    DB::statement("SET time_zone = '+06:00'");
}

    /**
     * Bootstrap any application services.
     */
   
}



