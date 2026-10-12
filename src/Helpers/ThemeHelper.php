<?php

use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use KFoobar\Theme\Theme;

if (!function_exists('theme')) {
    function theme($view = null, $data = [], $mergeData = []): View|Factory
    {
        return app(Theme::class)->view($view, $data, $mergeData);
    }
}
