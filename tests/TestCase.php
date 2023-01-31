<?php

namespace Wame\LaravelRouteResource\Tests;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Tests\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{

    use DatabaseMigrations;

    public function setUp() : void
    {
        Log::channel('stderr')->info('Test set up in progress');
        parent::setUp();

        ini_set('memory_limit', '512M');
        ini_set('max_execution_time', '600');

        // Seed Database
        Artisan::call('optimize:clear');
        Artisan::call('db:seed');

        $userData = config('wame-route.user');
        if (isset($userData['password'])) $userData['password_confirmation'] = $userData['password'];

        $routePath = route('auth.register');
        Log::channel('stderr')->info('ROUTE' . $routePath);
        $response = $this->postJson($routePath, $userData);

        $response->assertStatus(201);
        Log::channel('stderr')->info('SUCCESS ' . $routePath);

        Config::set('wame-route.testing', [
            'user' => $response['data']['user'],
            'auth' => $response['data']['auth']
        ]);
        Log::channel('stderr')->info('Test set up done');
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
