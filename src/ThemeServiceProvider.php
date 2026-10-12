<?php

namespace KFoobar\Theme;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use InvalidArgumentException;
use KFoobar\Theme\Commands\InstallCommand;

class ThemeServiceProvider extends BaseServiceProvider
{
    public function register(): void
    {
        $this->registerConfig();

        if (!$this->app->configurationIsCached()) {
            $config = $this->app['config']->get('theme');
            $theme = $config['theme'] ?? null;
            $default = $config['default'] ?? null;
            $settings = $this->settings($theme);

            if ($theme !== $default) {
                $settings = $this->defaults($settings, $this->settings($default));
            }

            $this->app['config']->set('theme', $this->defaults($config, $settings));
        }

        $this->app->singleton(Theme::class);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);
        }

        $this->callAfterResolving('view', function (Factory $factory): void {
            foreach (['theme', 'default'] as $namespace) {
                $factory->addNamespace($namespace, resource_path('views/themes/' . config('theme.' . $namespace)));
            }
        });

        $this->publishes([
            __DIR__ . '/../config/theme.php' => config_path('theme.php'),
        ], 'theme-config');
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/theme.php', 'theme');
    }

    private function settings(mixed $name): array
    {
        if (empty($name) || !is_file($path = config_path('themes/' . $name . '.php'))) {
            return [];
        }

        $settings = require $path;

        if (!is_array($settings)) {
            throw new InvalidArgumentException("Theme configuration [{$path}] must return an array.");
        }

        return $settings;
    }

    private function defaults(array $settings, array $defaults): array
    {
        foreach ($defaults as $key => $value) {
            if (!array_key_exists($key, $settings)) {
                $settings[$key] = $value;
            } elseif (is_array($settings[$key]) && is_array($value)
                && Arr::isAssoc($settings[$key]) && Arr::isAssoc($value)) {
                $settings[$key] = $this->defaults($settings[$key], $value);
            }
        }

        return $settings;
    }
}
