<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use KFoobar\Theme\Theme;

if (!function_exists('theme')) {
    function theme(mixed $view = null, Arrayable|array $data = [], array $mergeData = []): View|Factory
    {
        return app(Theme::class)->view($view, $data, $mergeData);
    }
}
