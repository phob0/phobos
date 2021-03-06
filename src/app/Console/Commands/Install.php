<?php

namespace Phobos\Framework\App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Install extends Command
{
    protected $progressBar;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phobos:install
                                {--timeout=300} : How many seconds to allow each process to run.
                                {--debug} : Show process output or not. Useful for debugging.';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Require dev packages and publish files for Phobos\Framework to work';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->progressBar = $this->output->createProgressBar(6);
        $this->progressBar->start();
        $this->info(" Phobos\Framework installation started. Please wait...");
        $this->progressBar->advance();

        $this->line(' Publishing configs and VueJS/QuasarJS files');
        $this->executeProcess('php artisan vendor:publish --provider="Phobos\Framework\PhobosServiceProvider" --force');

        $this->line(' Installing VueJS/QuasarJS files');
        $path = base_path('frontend');
        $status = 0;
        $response = [];
        exec("cd $path && npm install", $response, $status);
        $this->progressBar->advance();

//        $this->line(" Generating users table (using Laravel's default migrations)");
//        $this->executeProcess('php artisan migrate');

        $this->line(" Creating App\PhobosUser.php");
        $this->executeProcess('php artisan phobos:publish-user-model');

        $this->line(" Creating App\UserRole.php");
        $this->executeProcess('php artisan phobos:publish-user-role-model');

        $this->line(" Creating App\Http\Controllers\AppController.php");
        $this->executeProcess('php artisan phobos:publish-app-controller');

        $this->line(" Creating App\Http\Middleware\ExtractTokenFromApi.php");
        $this->executeProcess('php artisan phobos:publish-middleware');

        $this->progressBar->finish();
        $this->info(" Phobos\Framework installation finished.");
    }

    /**
     * Run a SSH command.
     *
     * @param string $command      The SSH command that needs to be run
     * @param bool   $beforeNotice Information for the user before the command is run
     * @param bool   $afterNotice  Information for the user after the command is run
     *
     * @return mixed Command-line output
     */
    public function executeProcess($command, $beforeNotice = false, $afterNotice = false)
    {
        $this->echo('info', $beforeNotice ? ' '.$beforeNotice : $command);

        $process = new Process($command, null, null, null, $this->option('timeout'), null);
        $process->run(function ($type, $buffer) {
            if (Process::ERR === $type) {
                $this->echo('comment', $buffer);
            } else {
                $this->echo('line', $buffer);
            }
        });

        // executes after the command finishes
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if ($this->progressBar) {
            $this->progressBar->advance();
        }

        if ($afterNotice) {
            $this->echo('info', $afterNotice);
        }
    }

    /**
     * Write text to the screen for the user to see.
     *
     * @param [string] $type    line, info, comment, question, error
     * @param [string] $content
     */
    public function echo($type, $content)
    {
        if ($this->option('debug') == false) {
            return;
        }

        // skip empty lines
        if (trim($content)) {
            $this->{$type}($content);
        }
    }
}
