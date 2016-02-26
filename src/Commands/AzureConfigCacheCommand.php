<?php

namespace Marchie\LaravelAzureDeploymentUtilities\Commands;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Foundation\Console\ConfigCacheCommand;

class AzureConfigCacheCommand extends ConfigCacheCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'azure:config-cache {dotenvpath : The path of the .env file (e.g. %HOME%\site\.env)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a cache file for faster configuration loading with custom .env path';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem)
    {
        parent::__construct($filesystem);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->checkForExistingDotEnvFile();

        $this->checkProvidedDotEnvFileExists();

        $this->copyProvidedDotEnvFile();

        $this->generateCachedConfigFile();

        $this->removeProvidedDotEnvFile();
    }

    protected function checkForExistingDotEnvFile()
    {
        if (file_exists(base_path('.env'))) {
            throw new \ErrorException('The .env file already exists in the repository. Please remove this file from your repository or use the standard Laravel config:cache command');
        }
    }

    protected function checkProvidedDotEnvFileExists()
    {
        if (! file_exists($this->argument('dotenvpath'))) {
            throw new \InvalidArgumentException('The file "' . $this->argument('dotenvpath') . '" does not exist');
        }
    }

    protected function copyProvidedDotEnvFile()
    {
        copy($this->argument('dotenvpath'), base_path('.env'));
    }

    protected function generateCachedConfigFile()
    {
        $this->fire();
    }

    protected function removeProvidedDotEnvFile()
    {
        unlink(base_path('.env'));
    }
}
