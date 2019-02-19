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
     * @param $route
     */
    public function register($route): void
    {
        if (isset($route['name'])) {
            $this->router->{$route['method']}($route['route_slug'],
                $route['controller'])->middleware($route['middleware'])->name($route['name']);
            return;
        }

        if ($this->shared) {
            $this->registerRoute($route);

            return;
        }

        $this->router->name('admin.')->group(function () use ($route) {
            $this->registerRoute($route);
        });
    }

    /**
     * @param $route
     */
    private function registerRoute($route): void
    {
        if ($route['method'] === 'resource') {
            $this->router->{$route['method']}($route['route_slug'],
                $route['controller'])->middleware($route['middleware'])->only($route['only']);

            return;
        }

        $name = str_replace(['/admin/', '/'], ['', '.'], $route['route_slug']);
        $this->router->name($name)->{$route['method']}($route['route_slug'],
            $route['controller'])->middleware($route['middleware']);
    }
}