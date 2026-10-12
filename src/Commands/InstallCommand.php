<?php

namespace KFoobar\Theme\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use RuntimeException;

class InstallCommand extends Command
{
    protected $signature = 'theme:install {--force : Overwrite existing default theme files}';

    protected $description = 'Create default theme files without overwriting existing files';

    public function handle(): int
    {
        $files = new Filesystem;

        $paths = [
            'config/themes/default.php' => config_path('themes/default.php'),
            'resources/views/themes/default/index.blade.php' => resource_path('views/themes/default/index.blade.php'),
        ];

        foreach ($paths as $source => $destination) {
            if (is_link($destination)) {
                throw new RuntimeException("Cannot install theme file over symbolic link [{$destination}].");
            }

            if ($files->exists($destination) && !$this->option('force')) {
                continue;
            }

            $files->ensureDirectoryExists(dirname($destination));

            if (!$files->copy(__DIR__ . '/../../stubs/' . $source, $destination)) {
                throw new RuntimeException("Unable to install theme file [{$destination}].");
            }
        }

        return self::SUCCESS;
    }
}
