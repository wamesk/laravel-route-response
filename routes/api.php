<?php

use \Illuminate\Support\Facades\Route;
use Illuminate\Support\Pluralizer;

$resources = config('wame-route.resources');
$group = config('wame-route.group');

Route::group($group, function () use ($resources, $group) {
    foreach ($resources as $resourceName => $resource)  {
        $idName = Pluralizer::singular($resourceName);
        $routeName = $group['prefix'] ? $group['prefix'] . '.' . $resourceName  : $resourceName;
        Route::get($resourceName, [$resource['controller'], 'index'])->name($routeName . '.index');
        Route::post($resourceName, [$resource['controller'], 'store'])->name($routeName . '.store');
        Route::get($resourceName . "/{{$idName}Id}", [$resource['controller'], 'show'])->name($routeName . '.show');
        Route::post($resourceName . "/{{$idName}Id}", [$resource['controller'], 'update'])->name($routeName . '.update');
        Route::delete($resourceName . "/{{$idName}Id}", [$resource['controller'], 'delete'])->name($routeName .'.delete');
    }
});
