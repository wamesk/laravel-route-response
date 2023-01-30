<?php

namespace Wame\LaravelRouteResource\Tests\Feature;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Testing\Fluent\AssertableJson;
use PHPUnit\Exception;
use Wame\LaravelRouteResource\Tests\TestCase;

class UpdateTest extends TestCase
{

    public function test_Update_Api(): void
    {
        $resources = config('wame-route.resources');
        $auth = config('wame-route.testing.auth.access_token');

        Log::channel('stderr')->info('Starting ' . class_basename($this));
        foreach ($resources as $resourceName => $resource) {

            // Get route name
            $id = DB::table($resourceName)?->first()?->id ?? null;
            $routeName = config('wame-route.group.prefix', '') ?
                config('wame-route.group.prefix', '') . '.'   . $resourceName : $resourceName;

            Log::channel('stderr')->info('ROUTE ' . route($routeName.'.update', $id));

            $fakeData = $this->generateFakeData($resource['update_data'], $resource, $resourceName);

            // Call API
            $response = $this->withToken($auth)->postJson(route($routeName.'.update', $id), $fakeData);

            if ($response->getStatusCode() !== 200) {
                dd(json_decode($response->getContent(), true));
            }

            // Check status & structure
            $response
                ->assertStatus(200);

            Log::channel('stderr')->info('SUCCESS ' . route($routeName.'.update', $id));
        }
        Log::channel('stderr')->info('Done ' . class_basename($this));
    }

    /**
     * @param array $data
     * @param array $resource
     * @param string $resourceName
     * @return array
     */
    private function generateFakeData(array $data, array $resource, string $resourceName): array
    {
        $fakeData = [];
        foreach ($data as $key => $rule) {

            // Generate Random String
            if ($this->fakerLookForAttribute($rule, 'string')) {
                // Check if string must be url
                if (!$this->fakerLookForRequired($rule) && !fake()->boolean()) {

                } else {
                    $max = $this->fakerLookForAttribute($rule, 'max:') ?? null;

                    if ($this->fakerLookForAttribute($rule, 'url')) {
                        $fakeData[$key] = fake()->url();
                    } else {
                        $fakeData[$key] = fake()->text($max ? $max : 255);
                    }
                }
            }

            // Generate UUID / ULID
            if ($this->fakerLookForAttribute($rule, 'uuid') || $this->fakerLookForAttribute($rule, 'ulid')) {
                $exists = $this->fakerLookForAttribute($rule, 'exists:');

                if (!$this->fakerLookForRequired($rule) && !fake()->boolean()) {

                } else {
                    if ($exists) {
                        $exists = explode(',', $exists);
                        $table = $exists[0];
                        $column = $exists[1];
                        $row = \Illuminate\Support\Facades\DB::table($table)->first();
                        $fakeData[$key] = $row?->$column;
                    }
                }
            }

            // Generate Integer
            if ($this->fakerLookForAttribute($rule, 'integer')) {
                if (!$this->fakerLookForRequired($rule) && !fake()->boolean()) {

                } else {
                    $min = $this->fakerLookForAttribute($rule, 'min:') ?? 0;
                    $max = $this->fakerLookForAttribute($rule, 'max:') ?? 999999;
                    $fakeData[$key] = fake()->numberBetween($min ? $min : 0, $max ? $max : 2147483647);
                }
            }

            // Generate File
            if ($this->fakerLookForAttribute($rule, 'file')) {
                if (!$this->fakerLookForRequired($rule) && !fake()->boolean()) {

                } else {
                    $mimes = $this->fakerLookForAttribute($rule, 'mimes:');
                    // Check if file is image
                    if (
                        str_contains($mimes, 'jpg') ||
                        str_contains($mimes, 'jpeg') ||
                        str_contains($mimes, 'png')
                    ) {
                        $image = fake()->image();
                        $file = \Illuminate\Http\UploadedFile::fake()->create($image);
                        $fakeData[$key] = $file;
                    }
                }
            }

            // Generate Boolean
            if ($this->fakerLookForAttribute($rule, 'bool')) {
                // Check if string must be url
                if (!$this->fakerLookForRequired($rule) && !fake()->boolean()) {

                } else {
                    $fakeData[$key] = true;
                }
            }
        }

        return $fakeData;
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
