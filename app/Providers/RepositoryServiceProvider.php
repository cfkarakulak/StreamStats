<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

class RepositoryServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Contracts\TwitchUserContract::class,
            \App\Repositories\TwitchUserRepository::class
        );

        $this->app->bind(
            \App\Contracts\TwitchDataContract::class,
            \App\Repositories\TwitchDataRepository::class
        );
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [
            \App\Contracts\TwitchUserContract::class,
            \App\Contracts\TwitchDataContract::class,
        ];
    }
}
