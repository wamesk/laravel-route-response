This package automatically creates API routes for model entities with testing.


### Instalation
```bash
composer require wamesk/laravel-route-resource
```

Publish configuaration file:
```bash
php artisan vendor:publish --provider="Wame\LaravelRouteResource\LaravelRouteResourceServiceProvider" --tag="config"
```

Content of `config/wame-route.php` file:
```php
<?php

return [
    // Data passed to user registration request
    'user' => [
        'name' => 'Testing User',
        'email' => 'testing@wame.sk',
        'password' => 'password123',
        // 'platform' => 'ios',
        // addition content...
    ],

    // Route Group Rules
    'group' => [
        'prefix' => 'api/v1',
        'middleware' =>  'auth:api',
        // addition content...
    ],

    // Add route resources
    'resources' => [
        /*'
        // EXAMPLE USAGE:
        posts' => [
            'controller' => '\App\Http\Controllers\v1\PostController::class',  <-- Resource Controller
            'store_data' => [                                                  <-- Array to Store Data
                'title' => 'string|required|max:255',
                'description' => 'string|max:255',
                'category_id' => 'uuid|required|exists:categories,id',
                'image' => 'file|mimes:jpg,jpeg,png|max:262144|required',
                'published' => 'boolean|required'
            ],
            'update_data' => [                                                  <-- Array to Update Data
                'title' => 'string|max:255',
                'description' => 'string|max:255'
            ]
            'soft_delete' => true                                               <-- If model uses Soft Delete
        ]
        */
    ]
];

```

Generated routes should look like this:
```
GET:    /api/v1/posts                       <- INDEX        (v1.posts.index)    <– route name       
POST:   /api/v1/posts                       <- STORE        (v1.posts.store)         
GET:    /api/v1/posts/{postId}              <- SHOW         (v1.posts.show)          
POST:   /api/v1/posts/{postId}              <- UPDATE       (v1.posts.update)   
DELETE: /api/v1/posts/{postId}              <- DELETE       (v1.posts.delete)   
POST:   /api/v1/posts/restore/{postId}      <- RESTORE      (v1.posts.restore)   
DELETE: /api/v1/posts/forceDelete/{postId}  <- FORCE DELETE (v1.posts.forceDelete)   
```

### Usage
Controller should have these methods:
```php

/** 
 * Get Post Index
 * Returns status code 200 
 */
public function index(\Illuminate\Http\Request $request) {}

/** 
 * Store Post in Database
 * Returns status code 201 
 */
public function store(\Illuminate\Http\Request $request) {}

/** 
 * Get one Post by ID
 * Returns status code 200 
 */
public function show(\Illuminate\Http\Request $request, string $postId) {}

/** 
 * Update Post by ID
 * Returns status code 200 
 */
public function update(\Illuminate\Http\Request $request, string $postId) {}

/** 
 * Delete Post by ID
 * Returns status code 200 
 */
public function delete(\Illuminate\Http\Request $request, string $postId) {}

/** 
 * Restore Post by ID
 * Returns status code 200
 * Only if config has "soft_delete" to "true" 
 */
public function restore(\Illuminate\Http\Request $request, string $postId) {}

/** 
 * Force Delete Post by ID
 * Returns status code 200 
 * Only if config has "soft_delete" to "true"
 */
public function forceDelete(\Illuminate\Http\Request $request, string $postId) {}
```

### Testing
Add following in `phpunit.xml` file inside root directory:

Inside `<testsuites>` element add: 
```xml
<testsuite name="LaravelRouteResource">
    <directory suffix="Test.php">./vendor/wamesk/laravel-route-resource</directory>
</testsuite>
```

Setup database for testing (use MySql)
```xml
<php>
    <env name="APP_KEY" value="base64:2U6xa56Ic3e96220e/T58R7gEayJ2aBpl331GaMnswc="/>
    <env name="APP_ENV" value="local"/>
    <env name="BCRYPT_ROUNDS" value="4"/>
    <env name="CACHE_DRIVER" value="array"/>
    <env name="DB_CONNECTION" value="mysql"/>
    <env name="DB_DATABASE" value="database_name"/>
    <env name="MAIL_MAILER" value="array"/>
    <env name="QUEUE_CONNECTION" value="sync"/>
    <env name="SESSION_DRIVER" value="array"/>
    <env name="TELESCOPE_ENABLED" value="false"/>
</php>
```
Add in `composer.json`
```json
"autoload-dev": {
    "psr-4": {
        "Wame\\LaravelRouteResource\\Tests\\": "vendor/wamesk/laravel-route-resource/tests/"
    }
}
```
Run
```bash
composer dump-autoload
```
Run tests

```bash
php artisan test
```

