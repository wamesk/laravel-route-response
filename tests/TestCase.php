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
        parent::setUp();

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '600');
        // Seed Database
        Artisan::call('optimize:clear');
        Artisan::call('db:seed');

        $userData = config('wame-route.user');
        if (isset($userData['password'])) $userData['password_confirmation'] = $userData['password'];

        $response = $this->postJson(route('auth.register'), $userData);

        $response->assertStatus(201);

        Config::set('wame-route.testing', [
            'user' => $response['data']['user'],
            'auth' => $response['data']['auth']
        ]);
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
