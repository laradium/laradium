<?php

namespace Netcore\Aven\Aven;

use Netcore\Aven\Registries\RouteRegistry;

class Aven {

    protected $routeRegistry;

    /**
     * Aven constructor.
     */
    public function __construct()
    {
        $this->routeRegistry = app(RouteRegistry::class);
    }

    /**
     * @param $resource
     * @return mixed
     */
    public function register($resource)
    {
        return $this->routeRegistry->register($resource);
    }
}