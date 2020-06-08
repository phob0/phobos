<?php

// --------------------------
// Custom Phobos Routes
// --------------------------
// This route file is loaded automatically by Phobos\Frameworks.
// Routes you generate using Phobos\Generators will be placed here.

Route::group([
    'middleware' => ['auth:api'],
    'prefix' => 'api',
    'namespace'  => 'App\Http\Controllers',
], function () { // custom routes
}); // this should be the absolute last line of this file

