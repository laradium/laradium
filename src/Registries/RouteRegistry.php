<?php

namespace Laradium\Laradium\Registries;

use Illuminate\Support\Collection;
use Illuminate\Routing\Router;

class RouteRegistry
{

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    private $router;

    /**
     * RouteRegistry constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param $route
     */
    public function register($route, $shared = false)
    {
        if (isset($route['name'])) {
            $this->router->{$route['method']}($route['route_slug'],
                $route['controller'])->middleware($route['middleware'])->name($route['name']);

            return;
        }

        if ($shared) {
            if ($route['method'] === 'resource') {
                $this->router->{$route['method']}($route['route_slug'],
                    $route['controller'])->middleware($route['middleware'])->only($route['only']);

                return;
            }

            $name = str_replace('/', '.', str_replace('/admin/', '', $route['route_slug']));
            $this->router->name($name)->{$route['method']}($route['route_slug'],
                $route['controller'])->middleware($route['middleware']);

            return;
        }

        $this->router->name('admin.')->group(function () use ($route) {
            if ($route['method'] === 'resource') {
                $this->router->{$route['method']}($route['route_slug'],
                    $route['controller'])->middleware($route['middleware'])->only($route['only']);

                return;
            }
            
            $name = str_replace('/', '.', str_replace('/admin/', '', $route['route_slug']));
            $this->router->name($name)->{$route['method']}($route['route_slug'],
                $route['controller'])->middleware($route['middleware']);
        });
    }
}