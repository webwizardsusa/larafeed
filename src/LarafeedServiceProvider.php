<?php

namespace Webwizardsusa\Larafeed;

use Illuminate\Support\ServiceProvider;

class LarafeedServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/..//config/larafeed.php', 'larafeed');
        $this->publishes([
            __DIR__.'/../config/larafeed.php' => config_path('larafeed.php'),
        ], ['larafeed', 'larafeed:config']);
    }
}
