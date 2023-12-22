<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Theme
    |--------------------------------------------------------------------------
    |
    | This value determines the "theme" your application is running.
    |
    */

    'theme' => env('APP_THEME', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Default Theme
    |--------------------------------------------------------------------------
    |
    | This value determines the default "theme" your application is running.
    | It will be used as a fallback if your selected theme is missing a
    | custom config or view file.
    |
    */

    'default' => env('APP_THEME_DEFAULT', 'default'),

];
