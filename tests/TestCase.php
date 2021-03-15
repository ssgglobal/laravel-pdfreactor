<?php

namespace StepStone\PdfReactor\Tests;

use Orchestra\Testbench\TestCase as BaseTestCase;
use StepStone\PdfReactor\Facade;
use StepStone\PdfReactor\ServiceProvider;

class TestCase extends BaseTestCase
{
    public function getPackageAliases($app)
    {
        return [
            'PdfReactor'    => Facade::class,
        ];
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}