<?php
namespace Tests;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;

class AzureOptimizeClassesTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        if (file_exists(base_path('bootstrap/cache/compiled.php')))
        {
            unlink(base_path('bootstrap/cache/compiled.php'));
        }
        $this->app['path.base'] = realpath('./');
    }

    public function tearDown()
    {
        if (file_exists(base_path('bootstrap/cache/compiled.php')))
        {
            unlink(base_path('bootstrap/cache/compiled.php'));
        }
        parent::tearDown();
    }

    /** @test */
    public function optimized_class_is_not_created_in_debug_environment()
    {
        $this->assertFileNotExists(base_path('bootstrap/cache/compiled.php'));
        Config::set('app.debug', true);
        Artisan::call('azure:optimize-classes');
        $this->assertFileNotExists(base_path('bootstrap/cache/compiled.php'));
    }

    /** @test */
    public function optimized_class_is_created_in_production_environment()
    {
        $this->assertFileNotExists(base_path('bootstrap/cache/compiled.php'));
        Config::set('app.debug', false);
        Artisan::call('azure:optimize-classes');
        $this->assertFileExists(base_path('bootstrap/cache/compiled.php'));
    }
}