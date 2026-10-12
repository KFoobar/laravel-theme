<?php

namespace KFoobar\Theme\Tests;

use InvalidArgumentException;

class ConfigurationTest extends TestCase
{
    public function test_configuration_merges_default_selected_and_application_settings_recursively(): void
    {
        $this->settings('default', [
            'logo' => 'default.svg',
            'colors' => ['primary' => 'black', 'secondary' => 'gray', 'accent' => 'green'],
            'nullable' => 'default',
            'enabled' => true,
        ]);
        $this->settings('light', [
            'colors' => ['primary' => 'white', 'accent' => 'blue'],
            'nullable' => null,
            'enabled' => false,
            'theme' => 'incorrect',
            'default' => 'incorrect',
        ]);
        $this->app['config']->set('theme.colors.accent', 'red');
        $this->bootTheme();

        $this->assertSame('default.svg', config('theme.logo'));
        $this->assertSame(['accent' => 'red', 'primary' => 'white', 'secondary' => 'gray'], config('theme.colors'));
        $this->assertNull(config('theme.nullable'));
        $this->assertFalse(config('theme.enabled'));
        $this->assertSame('light', config('theme.theme'));
        $this->assertSame('default', config('theme.default'));
    }

    public function test_missing_selected_config_keeps_default_settings(): void
    {
        $this->settings('default', ['logo' => 'default.svg']);
        $this->bootTheme();

        $this->assertSame('default.svg', config('theme.logo'));
    }

    public function test_selected_config_does_not_require_default_config(): void
    {
        $this->settings('light', ['logo' => 'light.svg']);
        $this->bootTheme();

        $this->assertSame('light.svg', config('theme.logo'));
    }

    public function test_cached_settings_are_used_without_reading_theme_files_again(): void
    {
        $this->settings('default', ['logo' => 'default.svg']);
        $this->settings('light', ['colors' => ['primary' => 'white']]);
        $this->write('resources/views/themes/light/home.blade.php', '{{ config("theme.logo") }}');
        $this->bootTheme();
        $cached = config()->all();
        $this->write('bootstrap/cache/config.php', '<?php return ' . var_export($cached, true) . ';');
        $this->write('config/themes/light.php', '<?php throw new RuntimeException("Must not load cached config again");');
        config()->set(require $this->app->getCachedConfigPath());
        $this->app->forgetInstance('config_loaded_from_cache');
        $this->bootTheme();

        $this->assertTrue($this->app->configurationIsCached());
        $this->assertSame($cached['theme'], config('theme'));
        $this->assertSame('default.svg', theme('home')->render());
    }

    public function test_existing_lists_empty_arrays_and_null_values_are_preserved(): void
    {
        $this->settings('default', [
            'menu' => ['home', 'about'],
            'empty' => ['enabled' => true],
            'nullable' => ['enabled' => true],
            'application' => ['default'],
            'zero' => 10,
        ]);
        $this->settings('light', [
            'menu' => ['custom'],
            'empty' => [],
            'nullable' => null,
            'application' => ['selected'],
            'zero' => 0,
        ]);
        config()->set('theme.application', []);
        $this->bootTheme();

        $this->assertSame(['custom'], config('theme.menu'));
        $this->assertSame([], config('theme.empty'));
        $this->assertNull(config('theme.nullable'));
        $this->assertSame([], config('theme.application'));
        $this->assertSame(0, config('theme.zero'));
    }

    public function test_register_config_remains_an_extension_hook(): void
    {
        $provider = new class($this->app) extends \KFoobar\Theme\ThemeServiceProvider {
            protected function registerConfig(): void
            {
                parent::registerConfig();
                $this->app['config']->set('theme.custom_provider', true);
            }
        };
        $this->app->register($provider);

        $this->assertTrue(config('theme.custom_provider'));
    }

    public function test_invalid_configuration_fails_clearly(): void
    {
        $this->write('config/themes/light.php', '<?php return "invalid";');
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('must return an array');

        $this->bootTheme();
    }
}
