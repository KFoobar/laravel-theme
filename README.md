# Laravel Theme

Lightweight package for Laravel 8/9/10 that adds simple support for themes.

## Installation

You can install the package with Composer:

```bash
composer require kfoobar/laravel-theme 
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
/resources/views/theme/default/index.blade.php
/resources/views/index.blade.php
```

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
