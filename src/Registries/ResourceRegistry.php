<?php

namespace Netcore\Aven\Registries;

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
        $routeSlug = $this->getRouteSlug($resourceName);
        if(!$routeSlug) {
            return $this;
        }
        $this->routeSlug = $routeSlug;
        $this->namespace = $resourceName;
        $routeList = [
            [
                'method'     => 'get',
                'route_slug' => $this->getRouteName('data-table'),
                'controller' => $this->getRouteController('dataTable'),
                'middleware' => ['web', 'aven']
            ],
            [
                'method'     => 'post',
                'route_slug' => $this->getRouteName('editable'),
                'controller' => $this->getRouteController('editable'),
                'middleware' => ['web', 'aven']
            ],
            [
                'method'     => 'get',
                'route_slug' => $this->getRouteName('get-form/{id?}'),
                'controller' => $this->getRouteController('getForm'),
                'middleware' => ['web', 'aven'],
                'name'       => 'admin.' . $routeSlug . '.form'
            ],
            [
                'method'     => 'resource',
                'route_slug' => $this->getRouteName(),
                'controller' => $this->getRouteController(),
                'middleware' => ['web', 'aven'],
            ],
        ];

        foreach ($routeList as $route) {
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
     * @param $resource
     * @return string
     */
    protected function getRouteSlug($resource): string
    {
        if(class_exists($resource)) {
            $r = new $resource;
            $model = $r->model();
            $model = new $model;

            $name = str_replace('_', '-', $model->getTable());

            return $name;
        }

        return false;
    }
}