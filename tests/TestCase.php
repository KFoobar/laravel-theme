<?php

namespace KFoobar\Theme\Tests;

use Illuminate\Filesystem\Filesystem;
use KFoobar\Theme\ThemeServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected string $directory;

    protected function getEnvironmentSetUp($app): void
    {
        $this->directory = __DIR__ . '/../work/tests/' . bin2hex(random_bytes(8));

        foreach (['config', 'resources/views', 'bootstrap/cache', 'compiled'] as $path) {
            (new Filesystem)->ensureDirectoryExists($this->directory . '/' . $path);
        }

        $app->setBasePath($this->directory);
        $app['config']->set('view.paths', [$this->directory . '/resources/views']);
        $app['config']->set('view.compiled', $this->directory . '/compiled');
        $app['config']->set('theme', ['theme' => 'light', 'default' => 'default']);
    }

    protected function bootTheme(): void
    {
        $this->app->register(ThemeServiceProvider::class, true);
    }

    protected function write(string $path, string $contents): void
    {
        $path = $this->directory . '/' . $path;
        (new Filesystem)->ensureDirectoryExists(dirname($path));
        file_put_contents($path, $contents);
    }

    protected function settings(string $name, array $settings): void
    {
        $this->write('config/themes/' . $name . '.php', '<?php return ' . var_export($settings, true) . ';');
    }

    protected function tearDown(): void
    {
        try {
            parent::tearDown();
        } finally {
            if (isset($this->directory)) {
                (new Filesystem)->deleteDirectory($this->directory);
            }
        }
    }
}
