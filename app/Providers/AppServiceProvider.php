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
                'Authorization' => sprintf('Bearer %s', env('TWITCH_CLIENT_SECRET')),
                'Client-Id' => env('TWITCH_CLIENT_ID'),
            ])->baseUrl('https://api.twitch.tv/helix/');
        });
    }
}
