<?php

namespace Beliven\Notarify\Tests;

use Beliven\Notarify\NotarifyServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            NotarifyServiceProvider::class
        ];
    }
}
