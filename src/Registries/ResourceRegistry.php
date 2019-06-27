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

        $routeName = str_slug($this->resource->getBaseResource()->getName());
        $routeSlug = $this->resource->getBaseResource()->getSlug();
        $this->resources->push($resourceName);

        if (!$routeSlug) {
            return $this;
        }

        $this->routeSlug = $routeSlug;
        $this->namespace = $resourceName;

        $routeList = [
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
            [
                'method'     => 'get',
                'route_slug' => $this->getRouteName('get-form/{id?}'),
                'controller' => $this->getRouteController('getForm'),
                'name'       => $this->getName($routeName . '.form')
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
                'only'       => $this->resource->getActions(),
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
        $routeName = strtolower($resource->getBaseResource()->getName());
        $actions = $this->resource->getActions();

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