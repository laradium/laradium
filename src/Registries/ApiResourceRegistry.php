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
    public function register($resourceName): self
    {
        $resource = new $resourceName;
        $routeSlug = $resource->getBaseResource()->getSlug();

        $this->routeSlug = $routeSlug;
        $this->namespace = $resourceName;

        // Add custom routes
        foreach ($resource->getCustomRoutes() as $name => $route) {
            $routeName = in_array($name, ['index', 'show']) ? '/' : '/' . kebab_case($name);
            $routePath = isset($route['params']) ? $route['params'] . $routeName : $routeName;

            $route = [
                'method'     => $route['method'],
                'name'       => $name,
                'route_slug' => $this->getRoutePath($routePath),
                'controller' => $this->getRouteController($name),
                'middleware' => $route['middleware'] ?? ['api'],
                'prefix'     => 'api',
                'where'      => $route['where'] ?? null
            ];

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
            $model = $r->getModel();
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
        return explode('@', $route['controller'])[1];
    }

    /**
     * @param $route
     * @param $routeSlug
     */
    protected function registerRoute($route, $routeSlug)
    {
        if ($route['method'] === 'resource') {
            $this->router->{$route['method']}($route['route_slug'], $route['controller'], ['names' => 'api.' . $routeSlug])
                ->middleware($route['middleware']);
        } else {
            $router = $this->router->{$route['method']}($route['route_slug'], $route['controller']);

            if ($route['where']) {
                foreach ($route['where'] as $key => $value) {
                    $router->where($key, $value);
                }
            }

            if (isset($route['name'])) {
                $router->name('api.' . $routeSlug . '.' . $route['name']);
            }

            $router->middleware($route['middleware']);
        }
    }
}