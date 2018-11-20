<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Traits\Relation;
use Laradium\Laradium\Traits\Sortable;

class HasMany extends Field
{

    use Sortable, Relation;

    /**
     * @var
     */
    private $fields;

    /**
     * @var
     */
    private $fieldName;

    /**
     * @var
     */
    private $actions = ['create', 'delete'];

    /**
     * @var FieldSet
     */
    private $fieldSet;

    /**
     * @var array
     */
    private $templateData = [];

    /**
     * @var bool
     */
    private $isCollapsed = true;

    /**
     * @var string
     */
    private $entryLabel = 'name';

    /**
     * HasMany constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, Model $model)
    {
        parent::__construct($parameters, $model);

        $this->relationName = array_first($parameters);
        $this->fieldName = array_first($parameters);
        $this->fieldSet = new FieldSet;
    }

    /**
     * @param array $attributes
     * @return $this|Field
     */
    public function build($attributes = [])
    {
        parent::build($attributes);

        $this->templateData = $this->getTemplateData();
        $this->validationRules($this->templateData['validation_rules']);

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse()
    {
        $data = parent::formattedResponse();
        $data['value'] = get_class($this);

        $data['entries'] = $this->getEntries();
        $data['template_data'] = $this->templateData;
        $data['config']['is_sortable'] = $this->isSortable();
        $data['config']['actions'] = $this->getActions();

        return $data;
    }

    /**
     * @return array
     */
    private function getTemplateData()
    {
        $fields = [];
        $validationRules = [];
        $this->addReplacementAttribute();
        $lastReplacementAttribute = [array_last($this->getReplacementAttributes())];

        if ($this->isSortable()) {
            $fields[] = (new Hidden('sequence_no', $this->getRelationBaseModel()))
                ->replacementAttributes($this->getReplacementAttributes())
                ->build(array_merge($this->getAttributes(), $lastReplacementAttribute))
                ->value($this->getRelationCollection()->count())
                ->formattedResponse(); // Add hidden sortable field
        }

        foreach ($this->fieldSet->fields as $temporaryField) {
            $field = clone $temporaryField;

            $field->model($this->getRelationBaseModel())
                ->replacementAttributes($this->getReplacementAttributes())
                ->build(array_merge($this->getAttributes(), $lastReplacementAttribute));

            if ($field->getRules()) {
                $validationRules[$field->getValidationKey()] = $field->getRules();
            }

            $fields[] = $field->formattedResponse();
        }

        return [
            'label'            => 'Entry',
            'fields'           => $fields,
            'replacement_ids'  => $this->getReplacementAttributes(),
            'validation_rules' => $validationRules
        ];
    }

    /**
     * @return array
     */
    private function getEntries()
    {
        $entries = [];

        foreach ($this->getRelationCollection()->sortBy($this->getSortableColumn()) as $item) {
            $entry = [
                'label'  => $this->getEntryLabel($item),
                'fields' => [],
                'config' => [
                    'is_deleted'   => false,
                    'is_collapsed' => $this->isCollapsed(),
                ],
                'id'     => $item->id,
            ];

            $entry['fields'][] = (new Hidden('id', $item))
                ->build(array_merge($this->getAttributes(), [$item->id]))
                ->formattedResponse(); // Add hidden ID field

            if ($this->isSortable()) {
                $entry['fields'][] = $this->sortableField($item); // Add hidden sortable field
            }

            foreach ($this->fieldSet->fields as $temporaryField) {
                $field = clone $temporaryField;

                $entry['fields'][] = $field->model($item)
                    ->build(array_merge($this->getAttributes(), [$item->id]))
                    ->formattedResponse();
            }

            $entries[] = $entry;
        }


        return $entries;
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
     * @param $value
     * @return $this
     */
    public function actions($value)
    {
        $this->actions = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param $value
     * @return $this
     */
    public function collapse($value)
    {
        $this->isCollapsed = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCollapsed()
    {
        return $this->isCollapsed;
    }

    /**
     * @param $value
     * @return $this
     */
    public function entryLabel($value)
    {
        $this->entryLabel = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getEntryLabel(Model $model)
    {
        if(!is_string($this->entryLabel)) {
            $closure = $this->entryLabel;
            $value = $closure($model);
        } else {
            $value = $model->{$this->entryLabel};
        }
        return $value;
    }

}