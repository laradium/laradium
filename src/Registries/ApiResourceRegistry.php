<?php

namespace Netcore\Aven\Registries;

use Illuminate\Support\Collection;
use Netcore\Aven\Traits\ApiResourceTrait;

class ApiResourceRegistry
{

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $router;

    /**
     * @var Collection
     */
    protected $routes;

    /**
     * @var string
     */
    protected $routeSlug;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * RouteRegistry constructor.
     */
    public function __construct()
    {
        $this->router = app('router');
    }

    /**
     * @param $resourceName
     * @return $this
     */
    public function register($resourceName)
    {
        $resource = new $resourceName;
        $routeSlug = $this->getRouteSlug($resourceName);

        if (!$routeSlug || !in_array(ApiResourceTrait::class, class_uses($resource))) {
            return $this;
        }

        $this->routeSlug = $routeSlug;
        $this->namespace = $resourceName;

        $routeList = [
            [
                'method' => 'get',
                'route_slug' => $this->getRouteName(),
                'controller' => $this->getRouteController('apiIndex'),
                'middleware' => ['web'],
                'prefix' => 'api'
            ],
            [
                'method' => 'post',
                'route_slug' => $this->getRouteName(),
                'controller' => $this->getRouteController('apiStore'),
                'middleware' => ['web', 'auth'],
                'prefix' => 'api'
            ],
            [
                'method' => 'get',
                'route_slug' => $this->getRouteName('{id}'),
                'controller' => $this->getRouteController('apiShow'),
                'middleware' => ['web'],
                'prefix' => 'api'
            ],
            [
                'method' => 'put',
                'route_slug' => $this->getRouteName('{id}'),
                'controller' => $this->getRouteController('apiUpdate'),
                'middleware' => ['web', 'auth'],
                'prefix' => 'api'
            ],
            [
                'method' => 'delete',
                'route_slug' => $this->getRouteName(),
                'controller' => $this->getRouteController('apiDelete'),
                'middleware' => ['web', 'auth'],
                'prefix' => 'api'
            ],
        ];

        foreach ($routeList as $route) {
            // Disable route if it is removed from resource
            $routeMethod = explode('@', $route['controller'])[1];
            if (!in_array($routeMethod, $resource->availableRoutes)) {
                continue;
            }

            if (isset($route['name'])) {
                $this->router->{$route['method']}($route['route_slug'],
                    $route['controller'])->middleware($route['middleware'])->name($route['name']);
            } else {
                $this->router->{$route['method']}($route['route_slug'],
                    $route['controller'])->middleware($route['middleware']);
            }
        }

        return $this;
    }

    /**
     * @param string $uri
     * @param bool $routeSlug
     * @return string
     */
    protected function getRouteName($uri = '', $routeSlug = true)
    {
        return '/api' . ($routeSlug ? '/' . $this->routeSlug : '') . ($uri ? '/' . $uri : '');
    }

    /**
     * @param null $method
     * @return string
     */
    protected function getRouteController($method = null)
    {
        return '\\' . $this->namespace . ($method ? '@' . $method : '');
    }

    /**
     * @param $resource
     * @return string
     */
    protected function getRouteSlug($resource): string
    {
        if (class_exists($resource)) {
            $r = new $resource;
            $model = $r->model();
            $model = new $model;

            $name = str_replace('_', '-', $model->getTable());

            return $name;
        }

        return false;
    }
}