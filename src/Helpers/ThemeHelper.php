<?php

use Illuminate\Contracts\View\Factory as ViewFactory;

if (!function_exists('theme')) {
    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string|null  $view
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $data
     * @param  array  $mergeData
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    function theme($view = null, $data = [], $mergeData = [])
    {
        $factory = app(ViewFactory::class);

        if (func_num_args() === 0) {
            return $factory;
        }

        $views = [
            'theme::' . $view,
            'default::' . $view,
            $view,
        ];

        return $factory->first($views, $data, $mergeData);
    }
}
