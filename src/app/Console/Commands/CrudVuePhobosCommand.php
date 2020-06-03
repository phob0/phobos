<?php


namespace Phobos\Framework\app\Console\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Illuminate\Filesystem\Filesystem;

class CrudVuePhobosCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'phobos:crud-vue
                    { type : The preset type (vue_page) }
                    {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Swap the front-end scaffolding for the application';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        if (static::hasMacro($this->argument('type'))) {
            return call_user_func(static::$macros[$this->argument('type')], $this);
        }

        if (! in_array($this->argument('type'), ['vue_page', 'vue_listing_page', 'vue_store_actions', 'vue_store_index', 'vue'])) {
            throw new InvalidArgumentException('Invalid preset.');
        }

        $this->{$this->argument('type')}();
    }

    /**
     * Create the "VuePage" preset.
     *
     * @return void
     */
    protected function vue_page()
    {
        $page = new \Phobos\Framework\app\Console\Commands\Presets\VuePage(new Filesystem);
        $page->create($this->option('name'));

        $this->info('Vue page template created successfully.');
    }

    /**
     * Create the "VueListingPage" preset.
     *
     * @return void
     */
    protected function vue_listing_page()
    {
        $page = new \Phobos\Framework\app\Console\Commands\Presets\VueListingPage(new Filesystem);
        $page->create($this->option('name'));

        $this->info('Vue listing template created successfully.');
    }

    /**
     * Create the "actions" preset.
     *
     * @return void
     */
    protected function vue_store_actions()
    {
        $page = new \Phobos\Framework\app\Console\Commands\Presets\VueStoreActions(new Filesystem);
        $page->create($this->option('name'));

        $this->info('Vue actions template created successfully.');
    }

    /**
     * Create the "index" preset.
     *
     * @return void
     */
    protected function vue_store_index()
    {
        $page = new \Phobos\Framework\app\Console\Commands\Presets\VueStoreIndex(new Filesystem);
        $page->create($this->option('name'));

        $this->info('Vue index template created successfully.');
    }

    /**
     * Update the root "index" file.
     *
     * @return void
     */
    protected function vue_root_index()
    {
        $page = new \Phobos\Framework\app\Console\Commands\Presets\VueRootIndex(new Filesystem);
        $page->create($this->option('name'));

        $this->info('Vue index template created successfully.');
    }

    /**
     * Update the "sideMenu" file.
     *
     * @return void
     */
    protected function vue_sidemenu()
    {
        $page = new \Phobos\Framework\app\Console\Commands\Presets\VueSideMenu(new Filesystem);
        $page->create($this->option('name'));

        $this->info('Vue index template created successfully.');
    }

    /**
     * Update the "routes" file.
     *
     * @return void
     */
    protected function vue_routes()
    {
        $page = new \Phobos\Framework\app\Console\Commands\Presets\VueRoutes(new Filesystem);
        $page->create($this->option('name'));

        $this->info('Vue index template created successfully.');
    }

    /**
     * Create the "vue" preset.
     *
     * @return void
     */
    protected function vue()
    {
        $this->vue_page();
        $this->vue_listing_page();
        $this->vue_store_actions();
        $this->vue_store_index();
        $this->vue_root_index();
        $this->vue_sidemenu();
        $this->vue_routes();
    }
}
