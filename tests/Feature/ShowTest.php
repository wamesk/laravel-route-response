<?php

namespace Wame\LaravelRouteResource\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Exception;
use Wame\LaravelRouteResource\Tests\TestCase;

class ShowTest extends TestCase
{

    /**
     * @return void
     */
    public function test_Show_Api(): void
    {
        $resources = config('wame-route.resources');
        $auth = config('wame-route.testing.auth.access_token');

        Log::channel('stderr')->info('Starting ' . class_basename($this));
        foreach ($resources as $resourceName => $resource) {

            // Get route name
            $id = DB::table($resourceName)?->first()?->id ?? null;
            $routeName = config('wame-route.group.prefix', '') ?
                config('wame-route.group.prefix', '') . '.'   . $resourceName : $resourceName;

            Log::channel('stderr')->info('ROUTE ' . route($routeName.'.show', $id));
            // Call API
            $response =
                $this
                    ->withToken($auth)
                    ->getJson(route($routeName.'.show', $id), [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json'
                    ]);

            if ($response->getStatusCode() !== 200) {
                dd(json_decode($response->getContent(), true));
            }

            // Check status & structure
            $response
                ->assertStatus(200)
                ->assertJson(fn (AssertableJson $json) => $this->checkShowStructure($json));

            Log::channel('stderr')->info('SUCCESS ' . route($routeName.'.show', $id));
        }
        Log::channel('stderr')->info('Done ' . class_basename($this));
    }

    /**
     * @param $rule
     * @param $attribute
     * @return array|false|mixed|string|string[]
     */
    private function fakerLookForAttribute($rule, $attribute): mixed
    {
        $rulesArray = explode('|', $rule);
        if (str_contains($rule, $attribute)) {
            foreach ($rulesArray as $oneRule) {
                if (str_contains($oneRule, $attribute)) {
                    if (str_replace($attribute, '', $oneRule) == "") {
                        return $attribute;
                    } else {
                        return str_replace($attribute, '', $oneRule);
                    }
                }
            }
        }
        return false;
    }

    /**
     * @param $rule
     * @return int|null
     */
    private function fakerLookForMax($rule): ?int
    {
        $rulesArray = explode('|', $rule);
        foreach ($rulesArray as $oneRule) {
            if (str_contains($oneRule, 'max:')) {
                return (int) str_replace('max:', '', $oneRule);
            }
        }
        return null;
    }


    private function fakerLookForRequired($rule): ?int
    {
        $rulesArray = explode('|', $rule);
        foreach ($rulesArray as $oneRule) {
            if (str_contains($oneRule, 'required')) return true;
        }
        return false;
    }

    /**
     * @param AssertableJson $json
     * @return void
     */
    private function checkIndexStructure(AssertableJson $json): void
    {
        // Structure
        $json->hasAll(['data', 'links', 'meta', 'message', 'code', 'errors']);

        // Data
        $json->has('data');

        // Meta
        $json
            ->has('meta')->has('meta.current_page')->has('meta.from')->has('meta.last_page')->has('meta.links')
            ->has('meta.path')->has('meta.per_page')->has('meta.to')->has('meta.total');

        // Pagination
        $json->has('links')->has('links.first')->has('links.last')->has('links.prev')->has('links.next');
    }

    private function checkShowStructure(AssertableJson $json): void
    {
        // Structure
        $json->hasAll(['data', 'message', 'code', 'errors']);

        // Data
        $json->has('data');
    }
}
