<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Http;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Http::macro('twitch', function () {
            return Http::withHeaders([
                'Authorization' => 'Bearer ui4kpdmtnsude1udzn2lblbp1lme4w',
                'Client-Id' => 'zf4lxk9cqqf9qnwpb700r5swgvwfh2',
            ])->baseUrl('https://api.twitch.tv/helix/');
        });
    }
}
