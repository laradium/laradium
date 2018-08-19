<?php

namespace Netcore\Aven\Aven;

use Netcore\Aven\Registries\ResourceRegistry;

class Aven {

    /**
     * @var
     */
    protected $resourceRegistry;

    /**
     * Aven constructor.
     */
    public function __construct()
    {
        $this->resourceRegistry = app(ResourceRegistry::class);
    }

    /**
     * @param $resource
     * @return mixed
     */
    public function register($resource)
    {
        return $this->resourceRegistry->register($resource);
    }
}