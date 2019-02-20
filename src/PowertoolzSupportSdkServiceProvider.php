<?php

namespace Jeylabs\PowertoolzSupportSdk;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container as Application;

class PowertoolzSupportSdkServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $source = __DIR__ . '/config/powertoolz-sdk.php';
        $this->publishes([$source => config_path('powertoolz-sdk.php')]);
        $this->mergeConfigFrom($source, 'powertoolz-sdk');
    }

    public function register()
    {
        $this->registerBindings($this->app);
    }

    protected function registerBindings(Application $app)
    {
        $app->singleton('PowertoolzSdk', function ($app) {
            $config = $app['config'];
            return new PowertoolzSupportSdk(
                $config->get('powertoolz-sdk.access_token', null),
                $config->get('powertoolz-sdk.postman_api_base_uri', null),
                $config->get('powertoolz-sdk.async_requests', false)
            );
        });
        $app->alias('PowertoolzSdk', PowertoolzSupportSdk::class);
    }
}
