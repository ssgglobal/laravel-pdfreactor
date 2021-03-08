<?php

namespace StepStone\PdfReactor;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/services.php', 'services');

        $this->app->singleton('StepStone\PdfReactor\PdfReactor', function ($app) {
            $config   = $app['config']['services']['pdfreactor'];

            return new PdfReactor($config['host'], $config['port'], $config['key']);
        });
    }
}