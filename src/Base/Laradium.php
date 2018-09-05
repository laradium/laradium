<?php

namespace Laradium\Laradium\Base;

use Laradium\Laradium\Registries\ApiResourceRegistry;
use Laradium\Laradium\Registries\ResourceRegistry;

class Laradium
{

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $resourceRegistry;

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $apiResourceRegistry;

    /**
     * Laradium constructor.
     */
    public function __construct()
    {
        $this->resourceRegistry = app(ResourceRegistry::class);
        $this->apiResourceRegistry = app(ApiResourceRegistry::class);
    }

    /**
     * @param $resource
     * @return mixed
     */
    public function register($resource)
    {
        return $this->resourceRegistry->register($resource);
    }

    /**
     * @param $resource
     * @return mixed
     */
    public function registerApi($resource)
    {
        return $this->apiResourceRegistry->register($resource);
    }
}