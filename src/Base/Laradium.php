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
        $cmsResources = [];
        $projectResources = [];

        // Project resources
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
                $projectResources[] = $resource;
            }
        }

        // CMS resources
        $baseResourcePath = base_path('vendor/laradium/laradium/src/Base/Resources');
        $contentResourcePath = base_path('vendor/laradium/laradium-content/src/Base/Resources');
        if (file_exists($baseResourcePath)) {
            foreach (\File::allFiles($baseResourcePath) as $path) {
                $resource = $path->getPathname();
                $baseName = basename($resource, '.php');
                $resource = 'Laradium\\Laradium\\Base\\Resources\\' . $baseName;

                // Check if there is a overridden resource in the project
                if ($this->resourceExists($projectResources, $baseName)) {
                    continue;
                }

                $cmsResources[] = $resource;
            }
        }

        if (file_exists($contentResourcePath)) {
            foreach (\File::allFiles($contentResourcePath) as $path) {
                $resource = $path->getPathname();
                $baseName = basename($resource, '.php');
                $resource = 'Laradium\\Laradium\\Content\\Base\\Resources\\' . $baseName;

                // Check if there is a overridden resource in the project
                if ($this->resourceExists($projectResources, $baseName)) {
                    continue;
                }

                $cmsResources[] = $resource;
            }
        }

        return array_merge($cmsResources, $projectResources);
    }

    /**
     * @return array
     */
    public function apiResources(): array
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

    /**
     * @param $resources
     * @param $resource
     * @return bool
     */
    protected function resourceExists($resources, $resource): bool
    {
        foreach ($resources as $res) {
            $className = array_last(explode('\\', $res));
            if ($className === $resource) {
                return true;
            }
        }

        return false;
    }
}