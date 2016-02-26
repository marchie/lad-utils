<?php

namespace Tests;

use Illuminate\Support\Facades\Artisan;

class AzureConfigCacheTests extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        if (!file_exists(base_path('.env'))) {
            copy(base_path('.env.example'), base_path('.env'));
            Artisan::call('key:generate');
            rename(base_path('.env'), './.env.test');
        }
    }

    public function tearDown()
    {
        if (file_exists('./.env.test')) {
            unlink('./.env.test');
        }
        if (file_exists(base_path('.env')))
        {
            unlink(base_path('.env'));
        }
        if (file_exists(base_path('bootstrap/cache/config.php')))
        {
            unlink(base_path('bootstrap/cache/config.php'));
        }
        parent::tearDown();
    }

    /** @test */
    public function exception_is_thrown_if_dotenv_is_present()
    {
        copy('./.env.test', base_path('.env'));
        $this->setExpectedException('ErrorException');
        Artisan::call('azure:config-cache', ['dotenvpath' => './.env.test']);
    }

    /** @test */
    public function exception_is_thrown_if_provided_dotenv_is_not_present()
    {
        $this->setExpectedException('InvalidArgumentException');
        Artisan::call('azure:config-cache', ['dotenvpath' => './nosuchfile']);
    }

    /** @test */
    public function see_config_is_cached()
    {
        $this->assertFileNotExists(base_path('.env'));
        $this->assertFileNotExists(base_path('bootstrap/cache/config.php'));
        Artisan::call('azure:config-cache', ['dotenvpath' => './.env.test']);
        $this->assertFileExists(base_path('bootstrap/cache/config.php'));
        $this->assertFileNotExists(base_path('.env'));
    }
}