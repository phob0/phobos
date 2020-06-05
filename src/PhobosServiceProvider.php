<?php

namespace Phobos\Framework;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Foundation\Http\Kernel;

class PhobosServiceProvider extends ServiceProvider
{
    const VERSION = '1.0.0';

    public $routeFilePath = '/routes/phobos.php';

    public $customRoutesFilePath = '/routes/custom.php';

    protected $commands = [
        \Phobos\Framework\App\Console\Commands\Install::class,
        \Phobos\Framework\App\Console\Commands\AddRouteContent::class,
        \Phobos\Framework\App\Console\Commands\CrudPhobosCommand::class,
        \Phobos\Framework\App\Console\Commands\CrudControllerPhobosCommand::class,
        \Phobos\Framework\App\Console\Commands\CrudRepositoryPhobosCommand::class,
        \Phobos\Framework\App\Console\Commands\CrudModelPhobosCommand::class,
        \Phobos\Framework\App\Console\Commands\CrudResourcePhobosCommand::class,
        \Phobos\Framework\App\Console\Commands\CrudPolicyPhobosCommand::class,
        \Phobos\Framework\App\Console\Commands\CrudVuePhobosCommand::class,
        \Phobos\Framework\App\Console\Commands\PublishPhobosUserModel::class,
        \Phobos\Framework\App\Console\Commands\PublishPhobosUserRoleModel::class,
        \Phobos\Framework\App\Console\Commands\PublishPhobosMiddleware::class,
        \Phobos\Framework\App\Console\Commands\PublishPhobosAppController::class,
    ];

    public function boot(\Illuminate\Routing\Router $router)
    {
        // use the vendor configuration file as fallback
        $this->mergeConfigFrom(
            __DIR__.'/config/phobos.php', 'phobos'
        );

        // add the root disk to filesystem configuration
        app()->config['filesystems.disks.'.config('phobos.root_disk_name')] = [
            'driver' => 'local',
            'root'   => base_path(),
        ];

        $this->setupMorphMap();
        self::setupCustomCache();
        $this->setupRoutes($this->app->router);
        $this->setupCustomRoutes($this->app->router);
        $this->setupRootPath();
        $this->forceHttps();
        $this->registerMiddlewareGroup($this->app->router);
        $this->publishFiles();
    }

    public function register()
    {
        // register the current package
        $this->app->bind('phobos', function ($app) {
            return new Phobos($app);
        });

        $this->commands($this->commands);
    }

    public function setupRoutes(Router $router)
    {
        // by default, use the routes file provided in vendor
        $routeFilePathInUse = __DIR__.$this->routeFilePath;

        // but if there's a file with the same name in routes, use that one
        if (file_exists(base_path().$this->routeFilePath)) {
            $routeFilePathInUse = base_path().$this->routeFilePath;
        }

        $this->loadRoutesFrom($routeFilePathInUse);
    }

    public function setupCustomRoutes(Router $router)
    {
        // if the custom routes file is published, register its routes
        if (file_exists(base_path().$this->customRoutesFilePath)) {
            $this->loadRoutesFrom(base_path().$this->customRoutesFilePath);
        }
    }

    public function registerMiddlewareGroup(Router $router)
    {
        $route_middleware_class = config('phobos.api_route_middleware');
        $middleware_class = config('phobos.middleware');

        foreach($route_middleware_class as $type) {
            if($type === 'push') {
                if (!is_array($type)) {
                    $router->pushMiddlewareToGroup('api', $type);

                    return;
                }

                foreach ($type as $class) {
                    $router->pushMiddlewareToGroup('api', $class);
                }
            } else {
                if (!is_array($type)) {
                    $router->prependMiddlewareToGroup('api', $type);

                    return;
                }

                foreach ($type as $class) {
                    $router->prependMiddlewareToGroup('api', $class);
                }
            }
        }

        foreach($middleware_class as $type) {
            if($type === 'push') {
                if (!is_array($type)) {
                    $router->pushMiddleware($type);

                    return;
                }

                foreach ($type as $class) {
                    $router->pushMiddleware($class);
                }
            } else {
                if (!is_array($type)) {
                    $router->prependMiddleware($type);

                    return;
                }

                foreach ($type as $class) {
                    $router->prependMiddleware($class);
                }
            }
        }
    }

    public function publishFiles()
    {
        $phobos_config = [__DIR__.'/config/phobos.php' => config_path()];
        $phobos_auth = [__DIR__.'/config/auth.php' => config_path()];
        $phobos_cache = [__DIR__.'/config/cache.php' => config_path()];

        $phobos_env = [__DIR__.'.env' => base_path()];
        $phobos_custom_routes_file = [__DIR__.$this->customRoutesFilePath => base_path($this->customRoutesFilePath)];

        $this->publishes($phobos_config, 'config');
        $this->publishes($phobos_auth, 'config');
        $this->publishes($phobos_cache, 'config');
        $this->publishes($phobos_env, '');

        $this->publishes([
            __DIR__.'/database/migrations/2014_10_12_000000_create_users_table.php' => base_path().'/database/migrations/2014_10_12_000000_create_users_table.php',
        ], 'migrations');

        $this->publishes([
            __DIR__.'/database/migrations/2019_08_07_124134_create_settings_table.php' => base_path().'/database/migrations/2019_08_07_124134_create_settings_table.php',
        ], 'migrations');

        $this->publishes([
            __DIR__.'/database/migrations/2019_08_19_182451_create_user_roles_table.php' => base_path().'/database/migrations/2019_08_19_182451_create_user_roles_table.php',
        ], 'migrations');

        //Seeder

        $this->publishes([
            __DIR__.'/database/seeds/DatabaseSeeder.php' => base_path().'/database/seeds/DatabaseSeeder.php',
        ], 'seeds');

        $this->publishes([__DIR__.'/frontend' => base_path('frontend')], 'frontend');

        $this->publishes($phobos_custom_routes_file, 'custom_routes');
    }

    private function setupMorphMap()
    {
        Relation::morphMap([
            'users' => \App\Models\PhobosUser::class,
        ]);
    }

    public static function setupCustomCache()
    {
        Cache::extend('tagged_file', function ($app) {
            return Cache::repository(new TaggedFile($app['files'], $app['config']['cache.stores.tagged_file']['path']));
        });
    }

    private function setupRootPath()
    {
        \URL::forceRootUrl(config('phobos.dynamic_url'));
    }

    private function forceHttps()
    {
        if (strpos(config('phobos.dynamic_url'), 'https://') === 0) {
            \URL::forceScheme('https');
        }
    }
}
