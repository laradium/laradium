<?php

namespace Laradium\Laradium\Traits;

trait Relation
{

    /**
     * @var string
     */
    private $relationName;

    /**
     * @return mixed
     */
    public function getRelationCollection()
    {
        return $this->getModel()->{$this->relationName};
    }

    /**
     * @return mixed
     */
    public function getRelationBaseModel()
    {
        return $this->getModel()->{$this->relationName}()->getModel();
    }
}