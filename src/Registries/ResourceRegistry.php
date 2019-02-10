<?php

namespace Laradium\Laradium\Registries;

use Illuminate\Support\Collection;

class ResourceRegistry
{

    /**
     * @var string
     */
    protected $routeSlug;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var Collection
     */
    protected $resources;

    /**
     * @var RouteRegistry
     */
    private $routeRegistry;

    /**
     * @var AbstractResource
     */
    private $resource;

    /**
     * RouteRegistry constructor.
     * @param RouteRegistry $routeRegistry
     */
    public function __construct(RouteRegistry $routeRegistry)
    {
        $this->routeRegistry = $routeRegistry;
        $this->resources = new Collection;
    }

    /**
     * @param $resourceName
     * @return $this
     */
    public function register($resourceName)
    {
        $this->resource = new $resourceName;

        $routeSlug = $this->resource->getBaseResource()->getSlug();
        $this->resources->push($resourceName);

        if (!$routeSlug) {
            return $this;
        }

        $this->routeSlug = $routeSlug;
        $this->namespace = $resourceName;

        // Add custom routes
        foreach ($this->resource->getCustomRoutes() as $name => $route) {
            $route = [
                'method' => $route['method'],
                'name' => $route['name'] ?? $name,
                'route_slug' => $this->getRouteName(isset($route['params']) ? $route['params'] . '/' . kebab_case($name) : kebab_case($name)),
                'controller' => $this->getRouteController($name),
                'middleware' => $route['middleware'] ?? $this->resource->getResourceMiddleware()
            ];

            $this->routeRegistry->register($route);
        }

        $routeList = [
            [
                'method' => 'get',
                'route_slug' => $this->getRouteName('data-table'),
                'controller' => $this->getRouteController('dataTable'),
                'middleware' => $this->resource->getResourceMiddleware()
            ],
            [
                'method' => 'post',
                'route_slug' => $this->getRouteName('editable/{locale?}'),
                'controller' => $this->getRouteController('editable'),
                'middleware' => $this->resource->getResourceMiddleware(),
                'name' => $this->getName($routeSlug . '.editable')
            ],
            [
                'method' => 'post',
                'route_slug' => $this->getRouteName('toggle/{id?}'),
                'controller' => $this->getRouteController('toggle'),
                'middleware' => $this->resource->getResourceMiddleware(),
                'name' => $this->getName($routeSlug . '.toggle')
            ],
            [
                'method' => 'get',
                'route_slug' => $this->getRouteName('get-form/{id?}'),
                'controller' => $this->getRouteController('getForm'),
                'middleware' => $this->resource->getResourceMiddleware(),
                'name' => $this->getName($routeSlug . '.form')
            ],
            // Import
            [
                'method' => 'post',
                'route_slug' => $this->getRouteName('import'),
                'controller' => $this->getRouteController('import'),
                'middleware' => $this->resource->getResourceMiddleware(),
                'name' => $this->getName($routeSlug . '.import')
            ],
            // Export
            [
                'method' => 'get',
                'route_slug' => $this->getRouteName('export'),
                'controller' => $this->getRouteController('export'),
                'middleware' => $this->resource->getResourceMiddleware(),
                'name' => $this->getName($routeSlug . '.export')
            ],
            [
                'method' => 'resource',
                'route_slug' => $this->getRouteName(),
                'controller' => $this->getRouteController(),
                'middleware' => $this->resource->getResourceMiddleware(),
                'only' => $this->resource->getActions()
            ],
        ];

        foreach ($routeList as $route) {
            if (
                isset($route['name']) &&
                $route['name'] === $this->getName($routeSlug . '.import') &&
                !method_exists($this->resource, 'import')
            ) {
                continue;
            }

            if (
                isset($route['name']) &&
                $route['name'] === $this->getName($routeSlug . '.export') &&
                !method_exists($this->resource, 'export')
            ) {
                continue;
            }

            $this->routeRegistry->register($route);
        }

        return $this;
    }

    /**
     * @param string $value
     * @return string
     */
    private function getName(string $value)
    {
        if ($this->resource->isShared()) {
            return $value;
        }

        return 'admin.' . $value;
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->resources;
    }

    /**
     * @param string $uri
     * @param bool $routeSlug
     * @return string
     */
    protected function getRouteName($uri = '', $routeSlug = true)
    {
        if ($this->resource->isShared()) {
            return ($routeSlug ? '/' . $this->routeSlug : '') . ($uri ? '/' . $uri : '');
        }

        return '/admin' . ($routeSlug ? '/' . $this->routeSlug : '') . ($uri ? '/' . $uri : '');
    }

    /**
     * @param null $method
     * @return string
     */
    protected function getRouteController($method = null)
    {
        return '\\' . $this->namespace . ($method ? '@' . $method : '');
    }
}