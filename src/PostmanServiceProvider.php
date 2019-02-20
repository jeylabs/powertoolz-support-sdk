<?php

namespace Jeylabs\Postman;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Container\Container as Application;

class PostmanServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $source = __DIR__ . '/config/postman.php';
        $this->publishes([$source => config_path('postman.php')]);
        $this->mergeConfigFrom($source, 'postman');
    }

    public function register()
    {
        $this->registerBindings($this->app);
    }

    protected function registerBindings(Application $app)
    {
        $app->singleton('postman', function ($app) {
            $config = $app['config'];
            return new Postman(
                $config->get('postman.access_token', null),
                $config->get('postman.postman_api_base_uri', null),
                $config->get('postman.async_requests', false)
            );
        });
        $app->alias('postman', Postman::class);

    }
}
