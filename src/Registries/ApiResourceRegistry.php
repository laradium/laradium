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

        if (!$routeSlug || !method_exists($resource, 'api')) {
            return $this;
        }

        $this->routeSlug = $routeSlug;
        $this->namespace = $resourceName;
        $api = $resource->api();

        $middleware = $api->getMiddleware();
        $routes = $api->getRoutes();

        $routeList = [
            [
                'method' => 'get',
                'name' => 'index',
                'route_slug' => $this->getRoutePath(),
                'controller' => $this->getRouteController('apiIndex'),
                'middleware' => $middleware['index'] ?? ['web', 'auth'],
                'prefix' => 'api',
            ],
            [
                'method' => 'post',
                'name' => 'store',
                'route_slug' => $this->getRoutePath(),
                'controller' => $this->getRouteController('apiStore'),
                'middleware' => $middleware['store'] ?? ['web', 'auth'],
                'prefix' => 'api'
            ],
            [
                'method' => 'get',
                'name' => 'show',
                'route_slug' => $this->getRoutePath('{id}'),
                'controller' => $this->getRouteController('apiShow'),
                'middleware' => $middleware['show'] ?? ['web', 'auth'],
                'prefix' => 'api'
            ],
            [
                'method' => 'put',
                'name' => 'update',
                'route_slug' => $this->getRoutePath('{id}'),
                'controller' => $this->getRouteController('apiUpdate'),
                'middleware' => $middleware['update'] ?? ['web', 'auth'],
                'prefix' => 'api'
            ],
            [
                'method' => 'delete',
                'name' => 'delete',
                'route_slug' => $this->getRoutePath(),
                'controller' => $this->getRouteController('apiDelete'),
                'middleware' => $middleware['delete'] ?? ['web', 'auth'],
                'prefix' => 'api'
            ],
        ];

        // Add custom routes
        foreach ($api->getCustomRoutes() as $name => $route) {
            $route = [
                'method' => $route['method'],
                'name' => $name,
                'route_slug' => $this->getRoutePath($route['params']),
                'controller' => $this->getRouteController($name),
                'middleware' => in_array($name, $middleware) ? $middleware[$name] : ['web', 'auth'],
                'prefix' => 'api'
            ];

            $this->registerRoute($route, $routeSlug);
        }

        foreach ($routeList as $route) {
            // Disable route if it is removed from resource
            $routeMethod = $this->getRouteMethodName($route);
            if (!in_array($routeMethod, array_keys($routes))) {
                continue;
            }

            $this->registerRoute($route, $routeSlug);
        }

        return $this;
    }

    /**
     * @param string $uri
     * @param bool $routeSlug
     * @return string
     */
    protected function getRoutePath($uri = '', $routeSlug = true)
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

    /**
     * @param $route
     * @return string
     */
    protected function getRouteMethodName($route)
    {
        $routeMethod = explode('@', $route['controller'])[1];
        $routeMethod = explode('api', $routeMethod);

        return isset($routeMethod[1]) ? strtolower($routeMethod[1]) : $routeMethod;
    }

    /**
     * @param $resource
     * @return array
     */
    protected function registerRoute($route, $routeSlug)
    {
        $route['middleware'] = $routes[$route['name']]['middleware'] ?? $route['middleware'];
        $route['name'] = $routeSlug . '.' . $route['name'];

        $this->router->{$route['method']}($route['route_slug'], $route['controller'])
            ->middleware($route['middleware'])
            ->name('api.' . $route['name']);
    }
}