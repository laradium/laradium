<?php

namespace Laradium\Laradium\Traits;

use Laradium\Laradium\Base\Fields\Hidden;

trait Nestable
{

    /**
     * @var bool
     */
    private $nestable = false;

    /**
     * @return $this
     */
    public function nestable()
    {
        $this->nestable = true;

        return $this;
    }

    /**
     * @return bool
     */
    public function isNestable()
    {
        return $this->nestable;
    }

    private function getNestedEntries()
    {
        dd();
    }
}