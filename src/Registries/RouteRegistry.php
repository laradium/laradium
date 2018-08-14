<?php

namespace Netcore\Aven\Registries;

use Illuminate\Support\Collection;

class RouteRegistry
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
     * RouteRegistry constructor.
     */
    public function __construct()
    {
        $this->router = app('router');
        $this->routes = new Collection;

    }

    /**
     * @param $resourceName
     * @return $this
     */
    public function register($resourceName)
    {
        $routeSlug = $this->getRouteSlug($resourceName);
        $this->routes->push([
            '/admin/' . $resourceName                 => $this->router->resource('/admin/' . $routeSlug,
                '\\' . $resourceName)->middleware(['web', 'aven']),
            '/admin/' . $resourceName . '/data-table' => $this->router->get('/admin/' . $routeSlug . '/data-table',
                '\\' . $resourceName . '@dataTable')->middleware(['web', 'aven']),
            '/admin/' . $resourceName . '/editable'   => $this->router->post('/admin/' . $routeSlug . '/editable',
                '\\' . $resourceName . '@editable')->middleware(['web', 'aven']),
            '/admin/login'                            => $this->router->get('/admin/login',
                '\Netcore\Aven\Http\Controllers\Admin\LoginController@index'
            )->middleware('web'),
            '/admin/login/post'                       => $this->router->post('/admin/login',
                '\Netcore\Aven\Http\Controllers\Admin\LoginController@login'
            )->middleware('web'),
            '/admin/logout'                           => $this->router->post('/admin/logout',
                '\Netcore\Aven\Http\Controllers\Admin\LoginController@logout'
            )->middleware('web'),
            '/admin/dashboard'                            => $this->router->get('/admin/dashboard',
                '\Netcore\Aven\Http\Controllers\Admin\AdminController@index'
            )->middleware('web'),
        ]);

        return $this;
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