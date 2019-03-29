<?php

namespace Laradium\Laradium\Base;

use Illuminate\Database\Eloquent\Model;

class Resource
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
     * @var string
     */
    private $prefix;

    /**
     * @var array
     */
    private $requestParams = [];

    /**
     * @var
     */
    protected $where;

    /**
     * @var array
     */
    protected $css = [];

    /**
     * @var array
     */
    protected $js = [];

    /**
     * @var array
     */
    protected $jsBeforeSource = [];

    /**
     * Resource constructor.
     */
    public function __construct()
    {
        $this->fieldSet = new FieldSet();
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
     * @param $value
     * @return $this
     */
    public function slug($value)
    {
        $this->slug = $value;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function prefix($value)
    {
        $this->prefix = $value;

        return $this;
    }

    /**
     * @return $this
     */
    public function build()
    {
        $closure = $this->closure;
        $fieldSet = $this->fieldSet->model($this->model);
        $closure($fieldSet);

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
     * @return FieldSet
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
     * @param $value
     * @return $this
     */
    public function model($value)
    {
        $this->model = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        if (!$this->slug && $this->name) {
            $this->slug = strtolower(str_replace(' ', '-', $this->name));
        }

        if (!$this->slug && !$this->name) {
            $this->slug = str_replace('_', '-', $this->model->getTable());
        }

        if ($this->prefix) {
            $this->slug = $this->prefix . '/' . $this->slug;
        }

        return $this->slug;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if (!$this->name && !$this->slug) {
            return ucfirst(str_replace('_', ' ', $this->model->getTable()));
        }

        if (!$this->name && $this->slug) {
            return ucfirst(str_replace('-', ' ', $this->slug));
        }

        return ucfirst($this->name);
    }


    /**
     * @param string $action
     * @return string
     */
    public function getRoute($action = 'index')
    {
        return 'admin.' . $this->getSlug() . '.' . $action;
    }

    /**
     * @param $value
     * @return $this
     */
    public function requestParams($value)
    {
        $this->requestParams = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getRequestQuery()
    {
        return $this->requestParams ? '?' . http_build_query($this->requestParams) : '';
    }

    /**
     * @param \Closure $closure
     * @return $this
     */
    public function where(\Closure $closure)
    {
        $this->where = $closure;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getWhere()
    {
        return $this->where;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function css($assets = []): self
    {
        $this->css = $assets;

        return $this;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function js($assets = []): self
    {
        $this->js = $assets;

        return $this;
    }

    /**
     * @param array $assets
     * @return $this
     */
    public function jsBeforeSource($assets = []): self
    {
        $this->jsBeforeSource = $assets;

        return $this;
    }

    /**
     * @return array
     */
    public function getCss(): array
    {
        return $this->css;
    }

    /**
     * @return array
     */
    public function getJs(): array
    {
        return $this->js;
    }

    /**
     * @return array
     */
    public function getJsBeforeSource(): array
    {
        return $this->jsBeforeSource;
    }
}