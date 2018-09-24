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

    /**
     * @return array
     */
    public function resources(): array
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
            foreach (\File::files($resourcePath) as $path) {
                $resource = $path->getPathname();
                $baseName = basename($resource, '.php');
                $resource = $resources . '\\' . $baseName;
                $resourceList[] = $resource;
            }
        }

        return $resourceList;
    }

    /**
     * @return array
     */
    private function apiResources(): array
    {
        $resourceList = [];
        $resources = config('laradium.resource_path', 'App\\Laradium\\Resources\\Api') . '\\Api';
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