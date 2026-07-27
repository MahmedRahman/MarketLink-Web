<?php

namespace App\Providers;

use App\Support\WorkHub;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
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

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // خلف Cloudflare/Proxy الطلب الداخلي http، فـ Laravel يولّد روابط غير آمنة
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceScheme('https');
        }

        View::composer(['work.*'], function () {
            WorkHub::shareContext();
        });
    }
}
