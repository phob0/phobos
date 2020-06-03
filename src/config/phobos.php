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

    'middleware' => [
        'push' => [],
        'prepend' => \Barryvdh\Cors\HandleCors::class
    ],

    'api_route_middleware' => [
        'push' => [
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Barryvdh\Cors\HandleCors::class
        ],
        'prepend' => App\Http\Middleware\ExtractApiTokenFromCookie::class
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
];
