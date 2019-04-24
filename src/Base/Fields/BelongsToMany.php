<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Base\FieldSet;

class BelongsToMany extends Field
{

    /**
     * @var string
     */
    private $relationName;

    /**
     * @var FieldSet
     */
    private $fieldSet;

    /**
     * @var string
     */
    private $title = 'name';

    /**
     * @var
     */
    private $items;

    /**
     * @var array
     */
    protected $fieldCol = [
        'size' => 2,
        'type' => 'md'
    ];

    /**
     * @var
     */
    protected $where;

    /**
     * bool
     */
    private $renderAsTags = false;

    /**
     * BelongsToMany constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->relationName = array_first($parameters);
        $this->fieldSet = new FieldSet;
    }

    /**
     * @param array $attributes
     * @return Field
     */
    public function build($attributes = [])
    {
        parent::build($attributes);

        $model = $this->getModel();
        $relationModel = $model->{$this->relationName}()->getModel();
        $items = $relationModel;

        if ($where = $this->getWhere()) {
            $items = $items->where($where);
        }

        $items = $items->get();

        $this->items = $items->map(function ($item) use ($model) {
            $isChecked = false;
            if ($pivot = $model->{$this->relationName}->where('id', $item->id)->first()) {
                $isChecked = true;
            }

            return [
                'id'         => $item->id,
                'name'       => $item->{$this->title},
                'is_checked' => $isChecked,
                'fields'     => $this->getTemplateData($pivot, $item->id)['fields']
            ];
        });

        $this->validationRules(array_merge($this->getValidationRules(), $this->getTemplateData()['validation_rules']));

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['value'] = get_class($this);
        $data['items'] = $this->items;
        $data['options'] = $this->getOptions();
        $data['selected'] = $this->getSelected();
        $data['config']['field_col'] = $this->fieldCol;
        $data['config']['render_as_tags'] = $this->renderAsTags;

        return $data;
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
     * BelongsToMany
     */
    public function tags($renderAsTags = true)
    {
        $this->renderAsTags = $renderAsTags;

        return $this;
    }

    /**
     * Collection
     */
    public function getOptions()
    {
        return $this->items->map(function ($item) {
            return [
                'id'       => $item['id'],
                'text'     => $item['name'],
                'selected' => $item['is_checked'],
            ];
        });
    }

    /**
     * Collection
     */
    public function getSelected()
    {
        return $this->items->filter(function ($item) {
            return $item['is_checked'] === true;
        })->pluck('id');
    }

    /**
     * @param int $size
     * @param string $type
     * @return $this
     */
    public function fieldCol($size = 2, $type = 'md')
    {
        $this->fieldCol = compact('size', 'type');

        return $this;
    }

    /**
     * @param $closure
     * @return $this
     */
    public function fields($closure)
    {
        $fieldSet = $this->fieldSet;
        $fieldSet->model($this->getModel());
        $closure($fieldSet);

        return $this;
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
     * @param null $model
     * @param null $id
     * @return array
     */
    private function getTemplateData($model = null, $id = null)
    {
        $fields = [];
        $validationRules = [];

        foreach ($this->fieldSet->fields as $temporaryField) {
            $field = clone $temporaryField;

            $field->model($model)
                ->value($model ? $model->pivot->{$field->getFieldName()} : '')
                ->build(array_merge($this->getAttributes(), ['pivot', $id]));

            if ($field->getRules()) {
                $validationRules[$field->getValidationKey()] = $field->getRules();
            }

            $fields[] = $field->formattedResponse();
        }

        return [
            'fields'           => $fields,
            'validation_rules' => $validationRules
        ];
    }
}