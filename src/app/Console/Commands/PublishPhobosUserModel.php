<?php


namespace Phobos\Framework\App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class PublishPhobosUserModel extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phobos:publish-user-model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish the PhobosUser model to App\PhobosUser';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../../Models/PhobosUser.php';
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function handle()
    {
        $destination_path = $this->laravel['path'].'/PhobosUser.php';

        if ($this->files->exists($destination_path)) {
            $this->error('PhobosUser model already exists!');

            return false;
        }

        $this->makeDirectory($destination_path);

        $this->files->put($destination_path, $this->buildClass());

        $this->info($this->laravel->getNamespace().'PhobosUser.php created successfully.');
    }

    /**
     * Build the class. Replace Backpack namespace with App one.
     *
     * @param string $name
     *
     * @return string
     */
    protected function buildClass($name = false)
    {
        $stub = $this->files->get($this->getStub());

        return $this->makeReplacements($stub);
    }

    /**
     * Replace the namespace for the given stub.
     * Replace the User model, if it was moved to App\Models\User.
     *
     * @param string $stub
     * @param string $name
     *
     * @return $this
     */
    protected function makeReplacements(&$stub)
    {
        $stub = str_replace('Phobos\Framework\App\Models;', $this->laravel->getNamespace(), $stub);

        if (!$this->files->exists($this->laravel['path'].'/PhobosUser.php') && $this->files->exists($this->laravel['path'].'/PhobosUser.php')) {
            $stub = str_replace($this->laravel->getNamespace().'User', $this->laravel->getNamespace().'PhobosUser', $stub);
        }

        return $stub;
    }
}
