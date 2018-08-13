<?php

namespace Netcore\Aven\Registries;

use Illuminate\Support\Collection;

class FieldRegistry
{

    /**
     * @var Collection
     */
    protected $fields;

    /**
     * FieldRegistry constructor.
     */
    public function __construct()
    {
        $this->fields = new Collection;
    }


    /**
     * @param $name
     * @param $class
     * @return $this
     */
    public function register($name, $class)
    {
        $this->fields->put($name, $class);

        return $this;
    }

    /**
     * @param $name
     * @return mixed
     */
    public function getClassByName($name)
    {
        return $this->all()->get($name);
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->fields;
    }
}