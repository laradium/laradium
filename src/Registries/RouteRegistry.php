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
            $resourceName                 => $this->router->resource($routeSlug,
                '\\' . $resourceName)->middleware('web'),
            $resourceName . '/data-table' => $this->router->get($routeSlug . '/data-table',
                '\\' . $resourceName . '@dataTable'),
            $resourceName . '/editable'   => $this->router->post($routeSlug . '/editable',
                '\\' . $resourceName . '@editable'),
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