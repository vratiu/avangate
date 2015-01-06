<?php

namespace Vratiu\Avangate\Laravel;

use Illuminate\Support\ServiceProvider;
use Vratiu\Avangate\Avangate;

class AvangateServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('vratiu/avangate', 'avangate', __DIR__ . '/../../../');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAvangate();
    }

    protected function registerAvangate()
    {
        $this->app->singleton('avangate', function ($app) {
            return new Avangate($app['config']->get('avangate::auth'));
        });
    }

    public function provides()
    {
        return array(
            'avangate',
        );
    }

}
