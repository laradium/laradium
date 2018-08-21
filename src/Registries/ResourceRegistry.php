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

        $this->router
            ->get($this->getRouteName('login', false), '\Netcore\Aven\Http\Controllers\Admin\LoginController@index')
            ->middleware(['web']);
        $this->router
            ->post($this->getRouteName('login', false), '\Netcore\Aven\Http\Controllers\Admin\LoginController@login')
            ->middleware(['web']);
        $this->router
            ->post($this->getRouteName('logout', false), '\Netcore\Aven\Http\Controllers\Admin\LoginController@logout')
            ->middleware(['web', 'aven']);
        $this->router
            ->get($this->getRouteName('dashboard', false),
                '\Netcore\Aven\Http\Controllers\Admin\AdminController@dashboard')
            ->middleware(['web', 'aven']);
        $this->router
            ->delete($this->getRouteName('content-block/{id}', false),
                '\Netcore\Aven\Content\Http\Controllers\Admin\PageController@contentBlockDelete')
            ->middleware(['web', 'aven']);
        $this->router
            ->get($this->getRouteName('pages/create/{channel}', false),
                '\Netcore\Aven\Content\Aven\Resources\PageResource@create')
            ->middleware(['web', 'aven']);
        $this->router
            ->get($this->getRouteName('pages/{page}/edit', false),
                '\Netcore\Aven\Content\Aven\Resources\PageResource@edit')
            ->middleware(['web', 'aven']);
        $this->router
            ->delete($this->getRouteName('resource/{id}', false),
                '\Netcore\Aven\Http\Controllers\Admin\AdminController@resourceDelete')
            ->middleware(['web', 'aven']);
        $this->router
            ->get($this->getRouteName('', false), '\Netcore\Aven\Http\Controllers\Admin\AdminController@index')
            ->middleware(['web', 'aven']);

    }

    /**
     * @param $resourceName
     * @return $this
     */
    public function register($resourceName)
    {
        $routeSlug = $this->getRouteSlug($resourceName);
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
        $explode = explode('\\',
            $resource); // we use explode because we want to remove namespace from controller path
        $resourceName = array_pop($explode); // get the name of the controller
        $name = str_replace('Resource', '', $resourceName); // remove "Resource" from name
        $name = $pieces = preg_split('/(?=[A-Z])/', $name);
        unset($name[0]); // unset empty element
        $name = str_plural(strtolower(implode('-', $name)));

        return $name;
    }
}