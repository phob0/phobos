<?php


namespace Phobos\Framework\app\Console\Commands\Presets;


class VueListingPage extends Preset
{
    /**
     * Create the preset.
     *
     * @return void
     */
    public function create($name)
    {
        $this->name = $name;
        $this->stub = __DIR__.'/../../stubs/vue-stubs/ListingPage.vue';
        $this->path = base_path('/frontend/src/pages').'/'.$name.'/'.ucfirst($name).'ListingPage.vue';
        $this->updateComponent();
    }
}
