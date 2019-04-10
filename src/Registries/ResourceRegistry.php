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

        $routeName = str_slug($this->resource->getResource()->getName());
        $routeSlug = $this->resource->getResource()->getSlug();
        $this->resources->push($resourceName);

        if (!$routeSlug) {
            return $this;
        }

        $this->routeSlug = $routeSlug;
        $this->namespace = $resourceName;

        // Add custom routes
        foreach ($this->resource->getResource()->getCustomRoutes() as $name => $route) {
            $route = [
                'method'     => $route['method'],
                'name'       => $route['name'] ?? $routeName . '.' . $name,
                'route_slug' => $this->getRouteName(isset($route['params']) ? $route['params'] . '/' . kebab_case($name) : kebab_case($name)),
                'controller' => $this->getRouteController($name),
                'middleware' => $route['middleware'] ?? []
            ];

            $this->routeRegistry->resource($this->resource)->register($route);
        }

        $routeList = [
            [
                'method'     => 'get',
                'route_slug' => $this->getRouteName('form/{resource?}'),
                'controller' => $this->getRouteController('form'),
                'name'       => $this->getName($routeName . '.form')
            ],
            [
                'method'     => 'get',
                'route_slug' => $this->getRouteName('data-table'),
                'controller' => $this->getRouteController('dataTable'),
                'name'       => $this->getName($routeName . '.data-table')
            ],
            [
                'method'     => 'post',
                'route_slug' => $this->getRouteName('editable/{locale?}'),
                'controller' => $this->getRouteController('editable'),
                'name'       => $this->getName($routeName . '.editable')
            ],
            [
                'method'     => 'post',
                'route_slug' => $this->getRouteName('toggle/{id?}'),
                'controller' => $this->getRouteController('toggle'),
                'name'       => $this->getName($routeName . '.toggle')
            ],
            // Import
            [
                'method'     => 'post',
                'route_slug' => $this->getRouteName('import'),
                'controller' => $this->getRouteController('import'),
                'name'       => $this->getName($routeName . '.import')
            ],
            // Export
            [
                'method'     => 'get',
                'route_slug' => $this->getRouteName('export'),
                'controller' => $this->getRouteController('export'),
                'name'       => $this->getName($routeName . '.export')
            ],
            [
                'method'     => 'resource',
                'route_slug' => $this->getRouteName(),
                'controller' => $this->getRouteController(),
                'only'       => $this->resource->getResource()->getActions(),
            ],
        ];

        foreach ($routeList as $route) {
            if (!$this->isAllowed($route, $this->resource)) {
                continue;
            }

            $this->routeRegistry->resource($this->resource)->register($route);
        }

        return $this;
    }

    /**
     * @param string $value
     * @return string
     */
    private function getName(string $value)
    {
        return $value;
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->resources;
    }

    /**
     * @param $route
     * @param $resource
     * @return bool
     */
    protected function isAllowed($route, $resource)
    {
        $routeName = strtolower($resource->getResource()->getName());
        $actions = $resource->getResource()->getActions();

        if (!isset($route['name'])) {
            return true;
        }

        $methodName = array_last(explode('.', $route['name']));

        if (
            $route['name'] === $this->getName($routeName . '.import') &&
            !method_exists($resource, 'import')
        ) {
            return false;
        }

        if (
            $route['name'] === $this->getName($routeName . '.export') &&
            !method_exists($resource, 'export')
        ) {
            return false;
        }

        if (!in_array($methodName, $actions)) {
            return false;
        }

        return true;
    }

    /**
     * @param string $uri
     * @param bool $routeSlug
     * @return string
     */
    protected function getRouteName($uri = '', $routeSlug = true)
    {
        if ($this->resource->getResource()->isShared()) {
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