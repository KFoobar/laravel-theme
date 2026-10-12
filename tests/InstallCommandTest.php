<?php

namespace KFoobar\Theme\Tests;

use RuntimeException;

class InstallCommandTest extends TestCase
{
    public function test_install_creates_default_theme_and_works_without_a_selected_theme(): void
    {
        config()->set('theme.theme', null);
        $this->bootTheme();
        $this->artisan('theme:install')->assertSuccessful();

        $this->assertFileExists($this->directory . '/config/themes/default.php');
        $this->assertSame('<div>index.blade.php</div>', trim(theme('index')->render()));
    }

    public function test_repeated_install_preserves_custom_files_and_adds_missing_files(): void
    {
        $this->write('config/themes/default.php', '<?php return ["custom" => true];');
        $this->bootTheme();
        $this->artisan('theme:install')->assertSuccessful();
        $this->assertSame('<?php return ["custom" => true];', file_get_contents(config_path('themes/default.php')));
        $this->write('resources/views/themes/default/index.blade.php', 'custom view');
        $this->artisan('theme:install')->assertSuccessful();

        $this->assertSame('custom view', theme('index')->render());
        $this->assertSame('<?php return ["custom" => true];', file_get_contents(config_path('themes/default.php')));
    }

    public function test_force_replaces_only_the_shipped_files(): void
    {
        $this->write('config/themes/default.php', '<?php return ["custom" => true];');
        $this->write('resources/views/themes/default/index.blade.php', 'custom view');
        $this->write('resources/views/themes/default/extra.blade.php', 'keep');
        $this->bootTheme();
        $this->artisan('theme:install', ['--force' => true])->assertSuccessful();

        $this->assertSame(['foo' => 'bar'], require config_path('themes/default.php'));
        $this->assertSame('<div>index.blade.php</div>', trim(theme('index')->render()));
        $this->assertSame('keep', theme('extra')->render());
    }

    public function test_install_refuses_to_overwrite_symbolic_links_even_with_force(): void
    {
        $this->write('config/outside.php', '<?php return [];');
        $this->write('config/themes/placeholder.php', '<?php return [];');
        symlink(config_path('outside.php'), config_path('themes/default.php'));
        $this->bootTheme();

        try {
            $this->artisan('theme:install', ['--force' => true])->run();
            $this->fail('Expected installation to reject a symbolic link.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('symbolic link', $exception->getMessage());
            $this->assertSame('<?php return [];', file_get_contents(config_path('outside.php')));
        }
    }
}
