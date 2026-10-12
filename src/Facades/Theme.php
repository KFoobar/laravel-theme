<?php

namespace KFoobar\Theme\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory view(mixed $view = null, \Illuminate\Contracts\Support\Arrayable|array $data = [], array $mergeData = [])
 * @method static bool exists(string $view)
 *
 * @see \KFoobar\Theme\Theme
 */
class Theme extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \KFoobar\Theme\Theme::class;
    }
}
