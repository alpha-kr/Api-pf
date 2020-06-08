<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class FBnotication extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        require_once app_path().'/helpers/NotificationFB.php' ;
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
