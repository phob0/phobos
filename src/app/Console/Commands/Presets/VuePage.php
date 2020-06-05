<?php

namespace Phobos\Framework\App\Console\Commands\Presets;


class VuePage extends Preset
{
    /**
     * Create the preset.
     *
     * @return void
     */
    public function create($name)
    {
        $this->name = $name;
        $this->stub = __DIR__.'/../../stubs/vue-stubs/Page.vue';
        $this->path = base_path('/frontend/src/pages').'/'.$name.'/'.ucfirst($name).'Page.vue';
        $this->updateComponent();
    }
}
