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
        $resource = new $resourceName;
        $routeSlug = $resource->getBaseResource()->getSlug();
        $this->resources->push($resourceName);

        if (!$routeSlug) {
            return $this;
        }

        $this->routeSlug = $routeSlug;
        $this->namespace = $resourceName;

        // Add custom routes
        foreach ($resource->getCustomRoutes() as $name => $route) {
            $route = [
                'method'     => $route['method'],
                'name'       => $route['name'] ?? $name,
                'route_slug' => $this->getRouteName(isset($route['params']) ? $route['params'] . '/' . kebab_case($name) : kebab_case($name)),
                'controller' => $this->getRouteController($name),
                'middleware' => $route['middleware'] ?? ['web', 'laradium'],
            ];

            $this->routeRegistry->register($route);
        }

        $routeList = [
            [
                'method'     => 'get',
                'route_slug' => $this->getRouteName('data-table'),
                'controller' => $this->getRouteController('dataTable'),
                'middleware' => ['web', 'laradium']
            ],
            [
                'method'     => 'post',
                'route_slug' => $this->getRouteName('editable/{locale?}'),
                'controller' => $this->getRouteController('editable'),
                'middleware' => ['web', 'laradium'],
                'name'       => 'admin.' . $routeSlug . '.editable'
            ],
            [
                'method'     => 'post',
                'route_slug' => $this->getRouteName('toggle/{id?}'),
                'controller' => $this->getRouteController('toggle'),
                'middleware' => ['web', 'laradium'],
                'name'       => 'admin.' . $routeSlug . '.toggle'
            ],
            [
                'method'     => 'get',
                'route_slug' => $this->getRouteName('get-form/{id?}'),
                'controller' => $this->getRouteController('getForm'),
                'middleware' => ['web', 'laradium'],
                'name'       => 'admin.' . $routeSlug . '.form'
            ],
            // Import
            [
                'method'     => 'post',
                'route_slug' => $this->getRouteName('import'),
                'controller' => $this->getRouteController('import'),
                'middleware' => ['web', 'laradium'],
                'name'       => 'admin.' . $routeSlug . '.import'
            ],
            // Export
            [
                'method'     => 'get',
                'route_slug' => $this->getRouteName('export'),
                'controller' => $this->getRouteController('export'),
                'middleware' => ['web', 'laradium'],
                'name'       => 'admin.' . $routeSlug . '.export'
            ],
            [
                'method'     => 'resource',
                'route_slug' => $this->getRouteName(),
                'controller' => $this->getRouteController(),
                'middleware' => ['web', 'laradium'],
                'only'       => $resource->getActions()
            ],
        ];

        foreach ($routeList as $route) {
            if (isset($route['name']) && $route['name'] === 'admin.' . $routeSlug . '.import' && !method_exists($resource,
                    'import')) {
                continue;
            }

            if (isset($route['name']) && $route['name'] === 'admin.' . $routeSlug . '.export' && !method_exists($resource,
                    'export')) {
                continue;
            }

            $this->routeRegistry->register($route);
        }

        return $this;
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