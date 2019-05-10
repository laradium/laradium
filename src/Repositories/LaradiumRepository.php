<?php

namespace Laradium\Laradium\Repositories;

use Laradium\Laradium\Base\ApiResource;
use Laradium\Laradium\Base\Charts;
use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\Table;
use Laradium\Laradium\Base\Validation;
use Laradium\Laradium\System\Repositories\SystemRepository;

class LaradiumRepository
{

    /**
     * LaradiumRepository constructor.
     */
    public function __construct()
    {
        $systemRepo = 'Laradium\\Laradium\\System\\Repositories\\SystemRepository';

        $this->systemRepo = class_exists($systemRepo) ? new $systemRepo() : null;
    }

    /**
     * @param \Closure $closure
     * @return \Laradium\Laradium\Base\Resource
     */
    public function resource(\Closure $closure): \Laradium\Laradium\Base\Resource
    {
        return (new Resource)->make($closure);
    }

    /**
     * @param \Closure $closure
     * @return \Laradium\Laradium\Base\Table
     */
    public function table(\Closure $closure): \Laradium\Laradium\Base\Table
    {
        return (new Table)->make($closure);
    }

    /**
     * @param \Closure $closure
     * @return \Laradium\Laradium\Base\ApiResource
     */
    public function apiResource(\Closure $closure): \Laradium\Laradium\Base\ApiResource
    {
        return (new ApiResource)->make($closure);
    }

    /**
     * @param \Closure $closure
     * @return \Laradium\Laradium\Base\Validation
     */
    public function validation(\Closure $closure): \Laradium\Laradium\Base\Validation
    {
        return (new Validation)->make($closure);
    }

    /**
     * @return Charts
     */
    public function charts(): \Laradium\Laradium\Base\Charts
    {
        return (new Charts());
    }

    /**
     * @param $user
     * @param null $resource
     * @param null $route
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
     * @return SystemRepository|null
     */
    public function system(): ?SystemRepository
    {
        return $this->systemRepo;
    }
}
