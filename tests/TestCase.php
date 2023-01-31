<?php

namespace Wame\LaravelRouteResource\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{

    use DatabaseMigrations;

    public function setUp() : void
    {

    }

    /**
     * @param $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            'Wame\LaravelRouteResource\LaravelRouteResourceServiceProvider'
        ];
    }
}
