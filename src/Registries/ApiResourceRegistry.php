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

        $this->routeSlug = $routeSlug;
        $this->namespace = $resourceName;
        $api = $resource->api();

        // Add custom routes
        foreach ($api->getCustomRoutes() as $name => $route) {
            $route = [
                'method'     => $route['method'],
                'name'       => $name,
                'route_slug' => $this->getRoutePath(isset($route['params']) ? $route['params'] . '/' . kebab_case($name) : kebab_case($name)),
                'controller' => $this->getRouteController($name),
                'middleware' => ['auth:api'],
                'prefix'     => 'api'
            ];

            $this->registerRoute($route, $routeSlug);
        }

        // Register api resource route
        $route = [
            'method'     => 'resource',
            'route_slug' => $this->getRoutePath(),
            'controller' => $this->getRouteController(),
            'middleware' => ['auth:api'],
            'only'       => $resource->getActions()
        ];

        $this->registerRoute($route, $routeSlug);

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
        if (isset($route['name'])) {
            $route['middleware'] = $routes[$route['name']]['middleware'] ?? $route['middleware'];
            $route['name'] = $routeSlug . '.' . $route['name'];

            $this->router->{$route['method']}($route['route_slug'], $route['controller'])
                ->middleware($route['middleware'])
                ->name('api.' . $route['name']);
        } else {
            if ($route['method'] === 'resource') {
                $this->router->{$route['method']}($route['route_slug'], $route['controller'], ['names' => 'api.' . $routeSlug])
                    ->middleware($route['middleware'])->only($route['only']);
            } else {
                $this->router->{$route['method']}($route['route_slug'], $route['controller'])
                    ->middleware($route['middleware']);
            }
        }
    }
}