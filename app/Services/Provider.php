<?php

namespace App\Services;

use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\PrintSheetService\PrintSheetService::class);
        $this->app->singleton(\App\Services\PrintSheetPDFService\PrintSheetPDFService::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
