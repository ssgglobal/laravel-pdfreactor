<?php

namespace StepStone\PdfReactor;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/services.php', 'services');

        $this->app->singleton('StepStone\PdfReactor\PdfReactor', function ($app) {
            $services   = $app['config']['services'];

            return new PdfReactor($services['pdfreactor.host'], $services['pdfreactor.port'], $services['pdfreactor.key']);
        });
    }
}