<?php

namespace Laradium\Laradium\Registries;

use Illuminate\Support\Collection;
use Illuminate\Routing\Router;

class RouteRegistry
{

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    private $router;

    /**
     * @var array
     */
    private $predefinedRoutes = [
        [
            'method' => 'get',
            'route_slug' => '/admin/datatable',
            'controller' => '\Laradium\Laradium\Base\Table@test',
//            'controller' => '\Laradium\Laradium\Http\Controllers\Admin\DatatableController@index',
            'middleware' => ['web', 'laradium'],
        ]
    ];

    /**
     * RouteRegistry constructor.
     * @param Router $router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;

        foreach($this->predefinedRoutes as $route) {
            $this->register($route);
        }
    }

    /**
     * @param $route
     */
    public function register($route)
    {
        if (isset($route['name'])) {
            $this->router->{$route['method']}($route['route_slug'],
                $route['controller'])->middleware($route['middleware'])->name($route['name']);

            return;
        }

        $this->router->name('admin.')->group(function () use ($route) {
            if ($route['method'] === 'resource') {
                $this->router->{$route['method']}($route['route_slug'],
                    $route['controller'])->middleware($route['middleware'])->only($route['only']);

                return false;
            }
            $name = str_replace('/', '.', str_replace('/admin/', '', $route['route_slug']));
            $this->router->name($name)->{$route['method']}($route['route_slug'],
                $route['controller'])->middleware($route['middleware']);
        });
    }
}