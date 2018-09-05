<?php

namespace Laradium\Laradium\Registries;

use Illuminate\Support\Collection;
use Laradium\Laradium\Base\ValidationField;

class ValidationFieldRegistry
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
     * @return $this
     */
    public function register($name)
    {
        $this->fields->put($name);

        return $this;
    }

    /**
     * @return Collection
     */
    public function all()
    {
        return $this->fields;
    }
}