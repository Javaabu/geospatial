<?php

namespace Javaabu\Geospatial\Tests;

use Javaabu\Geospatial\GeospatialServiceProvider;
use Javaabu\Geospatial\Tests\TestSupport\Providers\TestServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->app['config']->set('app.key', 'base64:yWa/ByhLC/GUvfToOuaPD7zDwB64qkc/QkaQOrT5IpE=');

        $this->app['config']->set('session.serialization', 'php');

    }

    protected function getPackageProviders($app)
    {
        return [
            GeospatialServiceProvider::class,
            TestServiceProvider::class,
        ];
    }
}
