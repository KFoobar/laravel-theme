<?php

namespace KFoobar\Theme;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;

class Theme
{
    public function __construct(private Factory $factory)
    {
    }

    public function view(mixed $view = null, Arrayable|array $data = [], array $mergeData = []): View|Factory
    {
        if ($view === null) {
            return $this->factory;
        }

        return $this->factory->first($this->views((string) $view), $data, $mergeData);
    }

    public function exists(string $view): bool
    {
        foreach ($this->views($view) as $candidate) {
            if ($this->factory->exists($candidate)) {
                return true;
            }
        }

        return false;
    }

    private function views(string $view): array
    {
        if (str_contains($view, '::')) {
            return [$view];
        }

        return ['theme::' . $view, 'default::' . $view, $view];
    }
}
