<?php

namespace Laradium\Laradium\Registries;

use Illuminate\Routing\Router;

class RouteRegistry
{

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    private $router;

    /**
     * @var bool
     */
    private $shared = false;

    /**
     * @var
     */
    private $resource;

    /**
     * RouteRegistry constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param $value
     * @return $this
     */
    public function shared($value): self
    {
        $this->shared = $value;

        return $this;
    }

    /**
     * @param $resource
     * @return $this
     */
    public function resource($resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * @param $route
     */
    public function register($route): void
    {
        $name = $this->getName($this->resource->getPrefix());

        $this->router->name($name ? $name . '.' : '')->group(function () use ($route) {
            $this->registerRoute($route);
        });
    }

    /**
     * @param $route
     */
    private function registerRoute($route): void
    {
        $router = $this->router->{$route['method']}($route['route_slug'], $route['controller']);

        if (isset($route['name'])) {
            $router = $router->name($route['name']);
        }

        if (isset($route['only'])) {
            $router = $router->only($route['only']);
        }
    }

    /**
     * @param $prefix
     * @return string
     */
    private function getName($prefix)
    {
        if ($this->resource->isShared()) {
            return $prefix ?? '';
        }

        return $prefix ? 'admin.' . $prefix : 'admin';
    }
}