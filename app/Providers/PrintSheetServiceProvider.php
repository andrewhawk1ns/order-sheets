<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class PrintSheetServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\App\Services\PrintSheetService\PrintSheetService::class);
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
