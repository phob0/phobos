<?php

namespace Phobos\Framework\App\Console\Commands\Presets;

use Illuminate\Filesystem\Filesystem;

abstract class Preset
{
    protected $path;

    protected $name;

    protected $stub;

    protected $files;

    /**
     * Create a new creator command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     * @return void
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * Update the example component.
     *
     * @return void
     */
    protected function updateComponent()
    {
        $this->makeDirectory($this->path);

        $this->files->put($this->path, $this->buildClass($this->name));
    }

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        return $this->replaceDummy($stub, $name);
    }

    /**
     *
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return file_exists($this->stub)
            ? $this->stub
            : __DIR__.$this->stub;
    }

    /**
     * Replace the class name for the given stub.
     *
     * @param  string  $stub
     * @param  string  $name
     * @return string
     */
    protected function replaceDummy($stub)
    {
        return str_replace(['Dummy', 'dummy'], [ucfirst($this->name) ,$this->name], $stub);
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory(dirname($path))) {
            $this->files->makeDirectory(dirname($path), 0777, true, true);
        }

        return $path;
    }
}
