<?php

namespace Laradium\Laradium\Registries;

use Illuminate\Support\Collection;

class ResourceRegistry
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
     * @var Collection
     */
    protected $resources;

    /**
     * RouteRegistry constructor.
     */
    public function __construct()
    {
        $this->router = app('router');
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

            $this->registerRoute($route);
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

            $this->registerRoute($route);
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

    /**
     * @param $route
     */
    protected function registerRoute($route)
    {
        if (isset($route['name'])) {
            $this->router->{$route['method']}($route['route_slug'],
                $route['controller'])->middleware($route['middleware'])->name($route['name']);
        } else {
            $this->router->name('admin.')->group(function () use ($route) {
                if ($route['method'] === 'resource') {
                    $this->router->{$route['method']}($route['route_slug'],
                        $route['controller'])->middleware($route['middleware'])->only($route['only']);
                } else {
                    $name = str_replace('/', '.', str_replace('/admin/', '', $route['route_slug']));
                    $this->router->name($name)->{$route['method']}($route['route_slug'],
                        $route['controller'])->middleware($route['middleware']);
                }
            });
        }
    }
}