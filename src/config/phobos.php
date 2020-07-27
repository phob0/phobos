<?php


return [

    /*
    |--------------------------------------------------------------------------
    | Look & feel customizations
    |--------------------------------------------------------------------------
    |
    | Make it yours.
    |
    */

    // Project name.
    'project_name' => 'Phobos',

    'dynamic_url' => env('DOCKER_URL', env('APP_URL', 'http://localhost')),

    'middleware' => [
        'push' => [],
        'prepend' => \Barryvdh\Cors\HandleCors::class
    ],

    'api_route_middleware' => [
        'push' => [
            \Barryvdh\Cors\HandleCors::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            "throttle:60,1"
        ],
        'prepend' => Phobos\Framework\App\Http\Middleware\ExtractApiTokenFromCookie::class
    ],

    'remove_middlewares' => [
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | File System
    |--------------------------------------------------------------------------
    */

    // Phobos\Framework sets up its own filesystem disk, just like you would by
    // adding an entry to your config/filesystems.php. It points to the root
    // of your project and it's used throughout all Phobos packages.
    //
    // You can rename this disk here. Default: root
    'root_disk_name' => 'root',

    /*
    |--------------------------------------------------------------------------
    | Where the templates for the generators are stored...
    |--------------------------------------------------------------------------
    |
    */

    'migration_template_path' => base_path('vendor/phobos/framework/src/Generators/templates/migration.txt'),

    /*
    |--------------------------------------------------------------------------
    | Where the generated files will be saved...
    |--------------------------------------------------------------------------
    |
    */

    'migration_target_path'   => base_path('database/migrations'),

];
