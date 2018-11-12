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
     * BelongsTo constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->relationName = array_first($parameters);
        $this->relation = $model->{$this->relationName}();
        $this->relationModel = $this->relation->getRelated();
        $this->label(ucfirst($this->relation->getRelation()));
        $this->fieldName($this->relation->getForeignKey());
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        if (method_exists($this->relationModel, 'translations')) {
            return $this->relationModel::get()->pluck($this->getTitle(), 'id')->toArray();
        }

        return $this->relationModel::pluck($this->getTitle(), 'id')->toArray();
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
}