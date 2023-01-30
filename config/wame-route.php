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
