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

    /**
     * @return array
     */
    public function resources()
    {
        $resourceList = [];
        $baseResourcePath = base_path('vendor/laradium/laradium/src/Base/Resources');
        $contentResourcePath = base_path('vendor/laradium/laradium-content/src/Base/Resources');
        if (file_exists($baseResourcePath)) {
            foreach (\File::allFiles($baseResourcePath) as $path) {
                $resource = $path->getPathname();
                $baseName = basename($resource, '.php');
                $resource = 'Laradium\\Laradium\\Base\\Resources\\' . $baseName;
                $resourceList[] = $resource;
            }
            if (file_exists($contentResourcePath)) {
                foreach (\File::allFiles($contentResourcePath) as $path) {
                    $resource = $path->getPathname();
                    $baseName = basename($resource, '.php');
                    $resource = 'Laradium\\Laradium\\Content\\Base\\Resources\\' . $baseName;
                    $resourceList[] = $resource;
                }
            }
        }

        $resources = config('laradium.resource_path', 'App\\Laradium\\Resources');
        $namespace = app()->getNamespace();
        $resourcePath = str_replace($namespace, '', $resources);
        $resourcePath = str_replace('\\', '/', $resourcePath);
        $resourcePath = app_path($resourcePath);
        if (file_exists($resourcePath)) {
            foreach (\File::allFiles($resourcePath) as $path) {
                $resource = $path->getPathname();
                $baseName = basename($resource, '.php');
                $resource = $resources . '\\' . $baseName;
                $resourceList[] = $resource;
            }
        }

        return $resourceList;
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->resourceRegistry->all();
    }
}