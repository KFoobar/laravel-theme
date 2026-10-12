<?php

namespace KFoobar\Theme\Tests;

use Symfony\Component\Process\Process;

class BootstrapTest extends TestCase
{
    public function test_real_application_boots_and_caches_configuration_without_app_theme(): void
    {
        $autoload = var_export(dirname(__DIR__) . '/vendor/autoload.php', true);
        $this->write('bootstrap/app.php', <<<'PHPFILE'
<?php

$app = new Illuminate\Foundation\Application(dirname(__DIR__));
$app->singleton(Illuminate\Contracts\Console\Kernel::class, Illuminate\Foundation\Console\Kernel::class);
$app->singleton(Illuminate\Contracts\Debug\ExceptionHandler::class, Illuminate\Foundation\Exceptions\Handler::class);

return $app;
PHPFILE);
        $providers = array_values(array_filter(
            config('app.providers'),
            static fn (string $provider): bool => str_starts_with($provider, 'Illuminate\\'),
        ));
        $providers[] = \KFoobar\Theme\ThemeServiceProvider::class;
        $config = ['name' => 'Theme tests', 'env' => 'testing', 'debug' => true, 'providers' => $providers];
        $this->write('config/app.php', '<?php return ' . var_export($config, true) . ';');
        $this->write('config/cache.php', '<?php return ["default" => "array", "stores" => ["array" => ["driver" => "array"]]];');
        $this->write('artisan', "<?php\nrequire {$autoload};\n\$app = require __DIR__ . '/bootstrap/app.php';\n\$kernel = \$app->make(Illuminate\\Contracts\\Console\\Kernel::class);\nexit(\$kernel->handle(new Symfony\\Component\\Console\\Input\\ArgvInput, new Symfony\\Component\\Console\\Output\\ConsoleOutput));\n");
        $this->write('render.php', "<?php\nrequire {$autoload};\n\$app = require __DIR__ . '/bootstrap/app.php';\n\$app->make(Illuminate\\Contracts\\Console\\Kernel::class)->bootstrap();\necho theme('home')->render();\n");
        $this->write('config/theme.php', '<?php return ["default" => "base"];');
        $this->write('config/view.php', '<?php return ["paths" => [resource_path("views")], "compiled" => base_path("compiled")];');
        $this->write('composer.json', '{"autoload":{"psr-4":{"App\\\\":"app/"}}}');
        $this->settings('base', ['logo' => 'base.svg']);
        $this->write('resources/views/themes/base/home.blade.php', '{{ config("theme.logo") }}');

        $this->assertSame('base.svg', $this->runApplication('render.php'));
        $this->runApplication('artisan', 'config:cache');
        $this->assertFileExists($this->directory . '/bootstrap/cache/config.php');
        $this->write('config/themes/base.php', '<?php throw new RuntimeException("Must use cached config");');
        $this->assertSame('base.svg', $this->runApplication('render.php'));
    }

    private function runApplication(string ...$arguments): string
    {
        $process = new Process([PHP_BINARY, ...$arguments], $this->directory, [
            'APP_THEME' => false,
            'APP_THEME_DEFAULT' => false,
            'APP_CONFIG_CACHE' => false,
        ]);
        $process->mustRun();

        return trim($process->getOutput());
    }
}
