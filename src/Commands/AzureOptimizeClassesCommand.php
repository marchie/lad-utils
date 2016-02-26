<?php

namespace Marchie\LaravelAzureDeploymentUtilities\Commands;

use Illuminate\Foundation\Console\OptimizeCommand;
use Illuminate\Support\Composer;

class AzureOptimizeClassesCommand extends OptimizeCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'azure:optimize-classes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute application optimization functions without executing "composer dump-autoload -o"';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Composer $composer)
    {
        parent::__construct($composer);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (! $this->laravel['config']['app.debug'])
        {
            $this->info('Compiling common classes');
            $this->compileClasses();
        }
        else
        {
            $this->warn('Debug mode is active: classes will not be compiled');
            $this->call('clear-compiled');
        }
    }
}
