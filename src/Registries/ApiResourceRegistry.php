<?php

namespace Laradium\Laradium\Registries;

use Illuminate\Support\Collection;
use Laradium\Laradium\Traits\ApiResourceTrait;

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
                'controller' => $this->getRouteController('index'),
                'middleware' => $middleware['index'] ?? ['web']
            ],
            [
                'method' => 'get',
                'name' => 'create',
                'route_slug' => $this->getRoutePath('create'),
                'controller' => $this->getRouteController('create'),
                'middleware' => $middleware['create'] ?? ['web', 'auth']
            ],
            [
                'method' => 'post',
                'name' => 'store',
                'route_slug' => $this->getRoutePath(),
                'controller' => $this->getRouteController('store'),
                'middleware' => $middleware['store'] ?? ['web', 'auth']
            ],
            [
                'method' => 'get',
                'name' => 'show',
                'route_slug' => $this->getRoutePath('{id}'),
                'controller' => $this->getRouteController('show'),
                'middleware' => $middleware['show'] ?? ['web', 'auth']
            ],
            [
                'method' => 'get',
                'name' => 'edit',
                'route_slug' => $this->getRoutePath('{id}/edit'),
                'controller' => $this->getRouteController('edit'),
                'middleware' => $middleware['edit'] ?? ['web', 'auth']
            ],
            [
                'method' => 'put',
                'name' => 'update',
                'route_slug' => $this->getRoutePath('{id}'),
                'controller' => $this->getRouteController('update'),
                'middleware' => $middleware['update'] ?? ['web', 'auth']
            ],
            [
                'method' => 'delete',
                'name' => 'delete',
                'route_slug' => $this->getRoutePath('{id}'),
                'controller' => $this->getRouteController('delete'),
                'middleware' => $middleware['delete'] ?? ['web', 'auth']
            ],
        ];

        // Add custom routes
        foreach ($api->getCustomRoutes() as $name => $route) {
            $route = [
                'method' => $route['method'],
                'name' => $name,
                'route_slug' => $this->getRoutePath($route['params'] . '/' . kebab_case($name)),
                'controller' => $this->getRouteController($name),
                'middleware' => $middleware[$name] ?? ['web', 'auth'],
                'prefix' => 'api'
            ];

            $this->registerRoute($route, $routeSlug);
        }

        foreach ($routeList as $route) {
            // Disable route if it is removed from resource
            $routeMethod = $this->getRouteMethodName($route);
            if (!in_array($routeMethod, $routes)) {
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

        return $routeMethod;
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