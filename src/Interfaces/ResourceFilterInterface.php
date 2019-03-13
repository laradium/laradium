<?php

namespace Laradium\Laradium\Interfaces;

interface ResourceFilterInterface
{
    /**
     * @param $query
     * @return mixed
     */
    public function filter($query);
}
