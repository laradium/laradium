<?php

namespace Laradium\Laradium\Repositories;

use Laradium\Laradium\Base\ApiResource;
use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\Table;
use Laradium\Laradium\Base\Validation;
use Laradium\Laradium\Helpers\BelongsTo;

class LaradiumRepository
{

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
     * @return BelongsTo
     */
    public function belongsTo()
    {
        return (new BelongsTo());
    }

    /**
     * @param $user
     * @param null $resource
     * @param string $action
     * @return bool
     */
    public function hasPermissionTo($user, $resource = null, $action = 'index')
    {
        return !method_exists($user, 'hasPermissionTo') || (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($resource, $action));
    }
}