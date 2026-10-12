<?php

namespace KFoobar\Theme\Tests;

use Illuminate\Contracts\View\Factory;
use Illuminate\Support\Collection;
use InvalidArgumentException;
use KFoobar\Theme\Facades\Theme;

class ViewTest extends TestCase
{
    public function test_selected_theme_wins_and_receives_view_data(): void
    {
        $this->write('resources/views/themes/light/pages/home.blade.php', '{{ $title }} {{ $subtitle }}');
        $this->write('resources/views/themes/default/pages/home.blade.php', 'default');
        $this->write('resources/views/pages/home.blade.php', 'application');
        $this->bootTheme();

        $view = theme('pages.home', new Collection(['title' => '<selected>']), ['subtitle' => 'merged']);

        $this->assertSame('&lt;selected&gt; merged', $view->render());
        $this->assertTrue(Theme::exists('pages.home'));
        $this->assertSame('theme::pages.home', Theme::view('pages.home')->name());
    }

    public function test_missing_selected_view_falls_back_to_default(): void
    {
        $this->write('resources/views/themes/default/home.blade.php', 'default');
        $this->write('resources/views/home.blade.php', 'application');
        $this->bootTheme();

        $this->assertSame('default', theme('home')->render());
    }

    public function test_missing_theme_directories_fall_back_to_application_views(): void
    {
        $this->write('resources/views/home.blade.php', 'application');
        $this->bootTheme();

        $this->assertSame('application', Theme::view('home')->render());
        $this->assertTrue(Theme::exists('home'));
    }

    /** @dataProvider emptyThemes */
    public function test_missing_or_empty_theme_uses_configured_default(?string $selected): void
    {
        $this->app['config']->set('theme', ['theme' => $selected, 'default' => 'base']);
        $this->write('resources/views/themes/base/home.blade.php', 'base');
        $this->bootTheme();

        $this->assertSame('base', theme('home')->render());
        $this->assertSame($selected, $this->app['config']->get('theme.theme'));
    }

    public static function emptyThemes(): array
    {
        return ['null' => [null], 'empty string' => ['']];
    }

    public function test_default_configuration_works_without_published_config(): void
    {
        $this->app['config']->set('theme', []);
        $this->write('resources/views/themes/default/home.blade.php', 'default');
        $this->bootTheme();

        $this->assertSame('default', theme('home')->render());
    }

    public function test_empty_themes_preserve_the_legacy_themes_root(): void
    {
        $this->app['config']->set('theme', ['theme' => null, 'default' => null]);
        $this->write('resources/views/themes/home.blade.php', 'legacy root');
        $this->write('resources/views/home.blade.php', 'application');
        $this->bootTheme();

        $this->assertSame('legacy root', theme('home')->render());
    }

    public function test_explicit_namespaces_are_preserved(): void
    {
        $this->write('resources/views/vendor/home.blade.php', 'namespaced');
        $this->app->make(Factory::class)->addNamespace('other', $this->directory . '/resources/views/vendor');
        $this->bootTheme();

        $this->assertSame('namespaced', theme('other::home')->render());
        $this->assertTrue(Theme::exists('other::home'));
    }


    public function test_nested_theme_names_remain_supported(): void
    {
        config()->set('theme.theme', 'brand/dark');
        $this->write('resources/views/themes/brand/dark/home.blade.php', 'nested theme');
        $this->bootTheme();

        $this->assertSame('nested theme', theme('home')->render());
    }

    public function test_stringable_view_names_remain_supported(): void
    {
        $this->write('resources/views/themes/light/home.blade.php', 'stringable');
        $this->bootTheme();
        $view = new class implements \Stringable {
            public function __toString(): string
            {
                return 'home';
            }
        };

        $this->assertSame('stringable', theme($view)->render());
    }

    public function test_no_view_returns_laravel_factory(): void
    {
        $this->bootTheme();

        $this->assertSame($this->app->make(Factory::class), theme());
        $this->assertSame(theme(), theme(null));
        $this->assertSame(theme(), Theme::view());
    }

    public function test_a_genuinely_missing_view_still_throws(): void
    {
        $this->bootTheme();
        $this->assertFalse(Theme::exists('missing'));
        $this->expectException(InvalidArgumentException::class);

        theme('missing');
    }
}
