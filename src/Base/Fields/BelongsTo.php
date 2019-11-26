<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;

class BelongsTo extends Field
{

    /**
     * @var
     */
    protected $relationModel;

    /**
     * @var
     */
    protected $relation;

    /**
     * @var mixed
     */
    protected $relationName;

    /**
     * @var
     */
    protected $title = 'name';

    /**
     * @var bool
     */
    protected $nullable = false;

    /**
     * @var
     */
    protected $where;

    /**
     * @var bool
     */
    protected $hasPlaceholder = false;

    /**
     * @var string
     */
    protected $placeholder = '- Select -';

    /**
     * BelongsTo constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->relationName = array_first($parameters);
    }

    /**
     * @param array $attributes
     * @return $this|Field
     */
    public function build($attributes = [])
    {
        $model = $this->getModel();
        $this->relation = $model->{$this->relationName}();
        $this->relationModel = $this->relation->getRelated();
        $this->label($this->getLabel() ?? ucfirst($this->relation->getRelation()));
        $this->fieldName($this->relation->getForeignKeyName());

        parent::build($attributes);

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        $options = $this->relationModel;
        if ($where = $this->getWhere()) {
            $options = $options->where($where);
        }

        if (method_exists($this->relationModel, 'translations')) {
            $options = $options->get()->pluck($this->getTitle(), 'id');
            if ($placeholder = $this->getPlaceholder()) {
                $options = $options->prepend($placeholder, '');
            }

            return $options->toArray();
        }

        $options = $options->pluck($this->getTitle(), 'id');
        if ($placeholder = $this->getPlaceholder()) {
            $options = $options->prepend($placeholder, '');
        }

        return $options->toArray();
    }

    /**
     * @param $value
     * @return $this
     */
    public function title($value)
    {
        $this->title = $value;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return $this
     */
    public function nullable()
    {
        $this->nullable = true;

        return $this;
    }

    /**
     * @param $value
     * @return $this
     */
    public function placeholder($value)
    {
        $this->placeholder = $value;
        $this->hasPlaceholder = true;

        return $this;
    }

    /**
     * @return string
     */
    public function getPlaceholder()
    {
        return $this->hasPlaceholder ? $this->placeholder : '';
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['options'] = collect($this->getOptions())->map(function ($text, $value) {
            return [
                'value'    => $value,
                'text'     => $text,
                'selected' => $this->getValue() == $value,
            ];
        })->toArray();

        return $data;
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
}
