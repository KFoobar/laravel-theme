<?php

namespace KFoobar\Theme\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create default folders for themes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->components->info('Installing assets for custom thems!');

        $this->folders();
        $this->files();

        $this->components->info('Installing assets for custom thems successfully!');
    }

    /**
     * Add default folders.
     *
     * @return void
     */
    private function folders()
    {
        $this->components->info('Creating directories...');

        (new Filesystem)->ensureDirectoryExists(config_path('themes'));
        (new Filesystem)->ensureDirectoryExists(resource_path('views/themes/default'));
    }

    /**
     * Add default files.
     *
     * @return void
     */
    private function files()
    {
        $this->components->info('Adding files...');

        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/config/themes', config_path('themes'));
        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs/resources/views/themes/default', resource_path('views/themes/default'));
    }
}
