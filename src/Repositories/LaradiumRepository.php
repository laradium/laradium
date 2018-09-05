<?php

namespace Laradium\Laradium\Repositories;

use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\Table;

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
}