<?php


namespace Phobos\Framework\App\Console\Commands\Presets;


class VueStoreActions extends Preset
{
    /**
     * Create the preset.
     *
     * @return void
     */
    public function create($name)
    {
        $this->name = $name;
        $this->stub = __DIR__.'/../../stubs/vue-stubs/actions.js';
        $this->path = base_path('/frontend/src/store').'/'.$name.'/actions.js';
        $this->updateComponent();
    }
}
