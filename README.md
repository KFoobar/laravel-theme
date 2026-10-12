# Laravel Theme

Lightweight theme configuration and view fallback for Laravel 12 and 13.

## Requirements

| Laravel | PHP | Testbench (development) |
| --- | --- | --- |
| 12 | 8.2–8.5 | 10 |
| 13 | 8.3–8.5 | 11 |

The package requires PHP `^8.2` and Illuminate `^12.0 || ^13.0`. Laravel 13's own PHP requirement excludes PHP 8.2.

## Installation

Install the package with Composer:

```bash
composer require kfoobar/laravel-theme:^2.0
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

Without `APP_THEME`, the existing configuration selects `default`. An explicitly empty/null selection retains the existing lookup in `resources/views/themes/`, followed by the default theme and ordinary views. Nested theme names and stringable view names remain supported. The protected `registerConfig()` provider extension hook is retained.

For [issue #1](https://github.com/KFoobar/laravel-theme/issues/1), no `.env` entry is required when the requested view exists in one of the fallback locations. Run `theme:install` if you need the starter `index` view. Views missing everywhere still throw an exception; the installer cannot create arbitrary application pages.

## Upgrading from 1.x

Version 2 requires Laravel 12 with PHP 8.2+, or Laravel 13 with PHP 8.3+. Laravel 8–11 and PHP 8.0–8.1 are not supported in 2.x. Unused `illuminate/database` and `ext-json` requirements are removed; applications must declare their own database dependencies if needed.

Existing theme configuration, view paths, nested theme names, lists and explicit empty values retain the 1.x bugfix behavior. There is no additional theme migration. Custom subclasses overriding provider `register()`/`boot()`, command `handle()` or facade `getFacadeAccessor()` must use compatible return types (`void`, `int` and `string`, respectively). The protected `registerConfig()` hook remains available.

Rebuild Laravel's configuration cache after updating the package:

```bash
php artisan config:cache
```

## Development

The regression suite uses PHPUnit 11/12 and Orchestra Testbench 10/11. GitHub Actions covers all seven combinations in the requirements table. The library no longer tracks `composer.lock`, allowing each compatibility environment to resolve appropriate dependencies.

```bash
composer update
composer validate --strict
composer test
```

To test a particular framework major:

```bash
composer update --with-all-dependencies --with="laravel/framework:^12.0" --with="orchestra/testbench:^10.0"
composer test
```

For Laravel 13 use `^13.0` and Testbench `^11.0` on PHP 8.3+. Tests use isolated application directories, including a separate application bootstrap and a real configuration-cache round trip.

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
