<?php

namespace Laradium\Laradium\Repositories;

use Laradium\Laradium\Base\ApiResource;
use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\Table;
use Laradium\Laradium\Base\Validation;

class LaradiumRepository
{

    /**
     * @var SystemRepository
     */
    private $systemRepo;

    /**
     * LaradiumRepository constructor.
     */
    public function __construct()
    {
        $this->systemRepo = new SystemRepository();
    }

    /**
     * @param \Closure $closure
     * @return Resource
     */
    public function resource(\Closure $closure): Resource
    {
        return (new Resource)->make($closure);
    }

    /**
     * @param \Closure $closure
     * @return Table
     */
    public function table(\Closure $closure): Table
    {
        return (new Table)->make($closure);
    }

    /**
     * @param \Closure $closure
     * @return ApiResource
     */
    public function apiResource(\Closure $closure): ApiResource
    {
        return (new ApiResource)->make($closure);
    }

    /**
     * @param \Closure $closure
     * @return Validation
     */
    public function validation(\Closure $closure): Validation
    {
        return (new Validation)->make($closure);
    }

    /**
     * @param $user
     * @param null $resource
     * @param string $action
     * @return bool
     */
    public function hasPermissionTo($user, $permission)
    {
        if (config('laradium.disable_permissions')) {
            return true;
        }

        return !method_exists($user, 'hasPermissionTo') || (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($permission));
    }

    /**
     * @return SystemRepository
     */
    public function system(): SystemRepository
    {
        return $this->systemRepo;
    }
}
