<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;

class ApiResource
{

    /**
     * @var FieldSet
     */
    protected $fieldSet;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var
     */
    protected $closure;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $slug;

    /**
     * Resource constructor.
     */
    public function __construct()
    {
        $this->fieldSet = new ApiFieldSet();
    }

    /**
     * @return $this
     */
    public function build()
    {
        $closure = $this->closure;
        $fieldSet = $this->fieldSet->setModel($this->getModel());
        $closure($fieldSet);

        return $this;
    }

    /**
     * @return DataSet
     */
    public function fieldSet()
    {
        return $this->fieldSet;
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function make(\Closure $closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * @return mixed
     */
    public function closure()
    {
        return $this->closure;
    }

    /**
     * @param Model $model
     * @return $this
     */
    public function model(Model $model)
    {
        $this->model = $model;

        return $this;
    }

    /**
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param $value
     * @return $this
     */
    public function name($value)
    {
        $this->name = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (!$this->name && !$this->slug) {
            return ucfirst(str_replace('_', ' ', $this->model->getTable()));
        } else {
            if (!$this->name && $this->slug) {
                return ucfirst(str_replace('-', ' ', $this->slug));
            }
        }

        return ucfirst($this->name);
    }

    /**
     * @param $value
     * @return $this
     */
    public function slug($value)
    {
        $this->slug = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        if (!$this->slug && $this->name) {
            $this->slug = strtolower(str_replace(' ', '-', $this->name));

            return $this->slug;
        } else {
            if (!$this->slug && !$this->name) {
                $this->name = str_replace('_', '-', $this->model->getTable());

                return $this->name;
            }
        }

        return $this->slug;
    }
}