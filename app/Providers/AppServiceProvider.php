<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (! app()->runningInConsole() && request()->hasSession()) {
            App::setLocale(session('locale', 'ar'));
        }

        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}