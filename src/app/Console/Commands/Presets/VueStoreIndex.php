<?php


namespace Phobos\Framework\App\Console\Commands\Presets;


class VueStoreIndex extends Preset
{
    /**
     * Create the preset.
     *
     * @return void
     */
    public function create($name)
    {
        $this->name = $name;
        $this->stub = __DIR__.'/../../stubs/vue-stubs/index.js';
        $this->path = base_path('/frontend/src/store').'/'.$name.'/index.js';
        $this->updateComponent();
    }
}
