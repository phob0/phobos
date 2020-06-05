<?php


namespace Phobos\Framework\App\Console\Commands;

use Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CrudPhobosCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phobos:crud {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a CRUD interface: Controller, Model, Repository, Policy, Request';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = ucfirst($this->argument('name'));
        $prefix = strtolower($this->argument('name')).'s';
        // Create the CRUD Controller and show output
        Artisan::call('phobos:crud-controller', ['name' => $name.'Controller', '--class-name' => $name]);
        echo Artisan::output();

        // Create the CRUD Repository and show output
        Artisan::call('phobos:crud-repository', ['name' => $name.'Repository', '--class-name' => $name]);
        echo Artisan::output();

        // Create the CRUD Model and show output
        Artisan::call('phobos:crud-model', ['name' => $name]);
        echo Artisan::output();

        // Create the CRUD Resource and show output
        Artisan::call('phobos:crud-resource', ['name' => $name.'Resource']);
        echo Artisan::output();

        // Create the CRUD Policy and show output
        Artisan::call('phobos:crud-policy', ['name' => $name.'Policy', '--class-name' => $name]);
        echo Artisan::output();

        // Create the CRUD frontend module
        Artisan::call('phobos:crud-vue', ['type' => 'vue', '--name' => $this->argument('name')]);
        echo Artisan::output();


        // Create the CRUD route
        Artisan::call('phobos:add-route', [
            'code' => "
                Route::prefix('".$prefix."')->group(function() {
                    Route::get('', '".$name."Controller@list');
                    Route::get('{".strtolower($this->argument('name'))."}', '".$name."Controller@item');
                    Route::post('', '".$name."Controller@create');
                    Route::put('{".strtolower($this->argument('name'))."}', '".$name."Controller@update');
                    Route::delete('{".strtolower($this->argument('name'))."}', '".$name."Controller@destroy');
                });
            "
        ]);
        echo Artisan::output();
    }
}
