# Laravel Theme

Lightweight package for Laravel 8/9/10 that adds simple support for themes.

This is the **1.x maintenance branch**. PHP `^8` and Laravel 8–10 remain supported; Laravel 9 requires PHP 8.0.2+ and Laravel 10 requires PHP 8.1+. The fixes on this branch do not require upgrading your application to Laravel 12 or 13.

## Installation

You can install the package with Composer:

```bash
composer require kfoobar/laravel-theme:^1.0
```

The package will automatically register itself.

### Publish config file (optional)

Run the following command to publish the config file:

```bash
php artisan vendor:publish --tag theme-config
```

### Add default folders (optional)

The package will automatically load config files from `config/themes/*` and view files `resources/views/themes/*`. 
To create those folders, run the following command:

```bash
php artisan theme:install
```

The installer creates `config/themes/default.php` and `resources/views/themes/default/index.blade.php`. Existing files are preserved, and missing files are added. To deliberately replace the two starter files, run `php artisan theme:install --force`. Other files are preserved; symbolic link file destinations are rejected. The installer always scaffolds the theme named `default` and does not clear configuration caches.

## How does it work?

The package helps you load config files and display view files based on the theme you have set.

You set which theme you want to use in your `.env` file:

```env
APP_THEME=light
```

### Config files

The package will help you manage multiple config files for your themes. Based on the theme you have set, the settings will be automatically merged into the `config/theme.php` file.

In the background, the package will load `config/themes/light.php` and merge it into `config/theme.php`. This will enable you to use the same config keys no matter what theme you have set: 

```php
config('theme.logo')
```

Selected theme settings take precedence over default theme settings. Explicit settings in `config/theme.php` have highest priority. Missing keys are filled recursively in associative settings, while existing scalar values, `null`, `false`, empty arrays and indexed lists are preserved. Each theme configuration file must return an array.

The existing theme selections and published configuration file remain unchanged. Merged settings participate in Laravel's configuration cache; run `php artisan config:cache` after changing theme settings or selections.

### View files

The package also help you load the correct view files based on the theme you have set, by using `theme()` instead of `view()` in your controllers:

```php
public function index()
{
    return theme('index');
}
```

When using the `theme()` function, the package will try to load the view file from the following paths and order:

```
/resources/views/themes/light/index.blade.php
/resources/views/themes/default/index.blade.php
/resources/views/index.blade.php
```

The helper also accepts an `Arrayable` data object and an optional third array of merge data. `theme()` or `theme(null)` returns Laravel's view factory. Explicit namespaces such as `theme('other::home')` use their registered namespace directly.

The repaired facade uses the same fallback chain:

```php
use KFoobar\Theme\Facades\Theme;

return Theme::view('index', ['title' => 'Home']);
```

Use `Theme::exists('index')` to check all candidates. Factory methods, Blade includes and layouts use Laravel's normal lookup rules.

Without `APP_THEME`, the existing configuration selects `default`. An explicitly empty/null selection retains the 1.x lookup in `resources/views/themes/`, followed by the default theme and ordinary views. Nested theme names and stringable view names remain supported. The protected `registerConfig()` provider extension hook is retained.

For [issue #1](https://github.com/KFoobar/laravel-theme/issues/1), no `.env` entry is required when the requested view exists in one of the fallback locations. Run `theme:install` if you need the starter `index` view. Views missing everywhere still throw an exception; the installer cannot create arbitrary application pages.

## Development

The regression suite uses PHPUnit 9 and Orchestra Testbench 6, 7 or 8. CI covers Laravel 8 on PHP 8.0–8.1, Laravel 9 on PHP 8.0–8.2 and Laravel 10 on PHP 8.1–8.3.

```bash
composer install
composer validate --strict
composer test
```

The tests run in isolated application directories. Older framework versions have known dependency advisories, so the CI dependency-resolution step uses `COMPOSER_POLICY_ADVISORIES_BLOCK=0` solely for compatibility testing. This is not part of the package's Composer configuration or a recommendation for applications. Local matrix runs use the same temporary environment setting with `composer update --with-all-dependencies --with="laravel/framework:^8.0" --with="orchestra/testbench:^6.0"`; select the matching framework/Testbench major for other rows.

## Use with Tailwind CSS

This package has no support for Tailwind CSS by it self, but we recommend using this package:

```bash
npm install -D tw-colors
```

Full documention can be found here: [https://github.com/L-Blondy/tw-colors](https://github.com/L-Blondy/tw-colors)

## Contributing

Contributions are welcome!

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
