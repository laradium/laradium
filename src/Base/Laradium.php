<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Registries\ResourceRegistry;

class Laradium {

    /**
     * @var
     */
    protected $resourceRegistry;

    /**
     * Laradium constructor.
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