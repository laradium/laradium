<?php

namespace Laradium\Laradium\Base;

use Illuminate\Support\Collection;
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
     * @var array
     */
    protected $baseResources = [
        'vendor/laradium/laradium/src/Base/Resources'            => 'Laradium\\Laradium\\Base\\Resources\\',
        'vendor/laradium/laradium-content/src/Base/Resources'    => 'Laradium\\Laradium\\Content\\Base\\Resources\\',
        'vendor/laradium/laradium-system/src/Base/Resources'     => 'Laradium\\Laradium\\System\\Base\\Resources\\',
        'vendor/laradium/laradium-permission/src/Base/Resources' => 'Laradium\\Laradium\\Permission\\Base\\Resources\\',
    ];

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
        $baseResources = [];
        $projectResources = $this->getResourcesFromPath(config('laradium.resource_path', 'App\\Laradium\\Resources'));
        $sharedResources = $this->getResourcesFromPath(config('laradium.shared_resource_path', 'App\\Laradium\\Resources\Shared'));

        // CMS resources
        foreach ($this->baseResources as $path => $namespace) {
            $resourcesPath = base_path($path);

            if (file_exists($resourcesPath)) {
                foreach (\File::allFiles($resourcesPath) as $resourcePath) {
                    $resource = $resourcePath->getPathname();
                    $baseName = basename($resource, '.php');
                    $resource = $namespace . $baseName;

                    // Check if there is a overridden resource in the project
                    if ($this->resourceExists($projectResources, $baseName) || str_contains($baseName, 'Api')) {
                        continue;
                    }

                    $baseResources[] = $resource;
                }
            }
        }

        return array_merge($baseResources, $projectResources, $sharedResources);
    }

    /**
     * @return array
     */
    public function apiResources(): array
    {
        $baseResources = [];
        $projectResources = $this->getResourcesFromPath(config('laradium.api_resource_path', 'App\\Laradium\\Resources\\Api'));

        // CMS resources
        foreach ($this->baseResources as $path => $namespace) {
            $resourcesPath = base_path($path . '/Api');

            if (file_exists($resourcesPath)) {
                foreach (\File::allFiles($resourcesPath) as $resourcePath) {
                    $resource = $resourcePath->getPathname();
                    $baseName = basename($resource, '.php');
                    $resource = $namespace . 'Api\\' . $baseName;

                    // Check if there is a overridden resource in the project
                    if ($this->resourceExists($projectResources, $baseName)) {
                        continue;
                    }

                    $baseResources[] = $resource;
                }
            }
        }

        return array_merge($baseResources, $projectResources);
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->resourceRegistry->all();
    }

    /**
     * @param $path
     * @return array
     */
    private function getResourcesFromPath($path)
    {
        $resources = [];

        $namespace = app()->getNamespace();
        $resourcePath = str_replace($namespace, '', $path);
        $resourcePath = str_replace('\\', '/', $resourcePath);
        $resourcePath = app_path($resourcePath);
        if (file_exists($resourcePath)) {
            foreach (\File::files($resourcePath) as $filePath) {
                $resource = $filePath->getPathname();
                $baseName = basename($resource, '.php');
                $resource = $path . '\\' . $baseName;
                $r = new $resource;
                if (!class_exists($r->resourceName())) {
                    continue;
                }
                $resources[] = $resource;
            }
        }

        return $resources;
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
