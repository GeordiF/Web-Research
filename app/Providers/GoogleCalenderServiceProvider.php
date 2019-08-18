<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Library\Services\GoogleCalender;

class GoogleCalenderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('App\Library\Services\GoogleCalender', function ($app) {
            return new GoogleCalender();
        });    }
}
