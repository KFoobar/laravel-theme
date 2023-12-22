<?php

namespace KFoobar\Theme;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use KFoobar\Theme\Commands\InstallCommand;

class ThemeServiceProvider extends BaseServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();

        $theme = config('theme.theme', null);
        $default = config('theme.default', null);

        if (!empty($theme) && file_exists(config_path('themes/' . $theme . '.php'))) {
            $this->mergeConfigFrom(config_path('themes/' . $theme . '.php'), 'theme');
        } elseif (!empty($default) && file_exists(config_path('themes/' . $default . '.php'))) {
            $this->mergeConfigFrom(config_path('themes/' . $default . '.php'), 'theme');
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
            ]);
        }

        View::addNamespace('theme', resource_path('views/themes/' . config('theme.theme')));
        View::addNamespace('default', resource_path('views/themes/' . config('theme.default')));

        $this->publishes([
            __DIR__ . '/../config/theme.php' => config_path('theme.php'),
        ], 'theme-config');
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/theme.php',
            'theme'
        );
    }
}
