<?php

namespace Laradium\Laradium\Repositories;

use Laradium\Laradium\Base\Resource;
use Laradium\Laradium\Base\Table;

class LaradiumRepository {

    /**
     * @param \Closure $closure
     * @return Resource
     */
    public function resource(\Closure $closure)
    {
        return (new Resource)->make($closure);
    }

    /**
     * @param \Closure $closure
     * @return Table
     */
    public function table(\Closure $closure)
    {
        return (new Table)->make($closure);
    }
}