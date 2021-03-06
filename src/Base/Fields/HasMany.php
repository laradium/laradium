<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Laradium\Laradium\Base\Element;
use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Base\FieldSet;
use Laradium\Laradium\Models\Menu;
use Laradium\Laradium\Models\MenuItem;
use Laradium\Laradium\Traits\Nestable;
use Laradium\Laradium\Traits\Relation;
use Laradium\Laradium\Traits\Sortable;

class HasMany extends Field
{

    use Sortable, Relation, Nestable;

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
     * @var array
     */
    private $letters = [];

    /**
     * @var bool
     */
    private $renderAsTable = false;

    /**
     * HasMany constructor.
     * @param $parameters
     * @param null|Model $model
     */
    public function __construct($parameters, Model $model = null)
    {
        parent::__construct($parameters, $model);

        $this->relationName = array_first($parameters);
        $this->fieldName = array_first($parameters);
        $this->fieldSet = new FieldSet;
        $this->letters = array_combine(range(0, 25), range('a', 'z'));
    }

    /**
     * @param array $attributes
     * @return $this|Field
     */
    public function build($attributes = [])
    {
        parent::build($attributes);

        if ($this->getModel() && get_class($this->getModel()) === config('laradium.menu_class', Menu::Class)) {
            config('laradium.menu_item_class', MenuItem::class)::rebuild();
        }

        $this->templateData = $this->getTemplateData();
        $this->validationRules($this->templateData['validation_rules']);
        $this->validationKeyAttributes($this->templateData['validation_attributes']);

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['value'] = get_class($this);
        $data['entries'] = $this->getEntries();
        $data['template_data'] = $this->templateData;
        $data['config']['is_sortable'] = $this->isSortable();
        $data['config']['actions'] = $this->getActions();
        $data['config']['render_as_table'] = $this->renderAsTable;

        return $data;
    }

    /**
     * @return array
     */
    private function getTemplateData(): array
    {
        $fields = [];
        $validationRules = [];
        $validationKeyAttributes = [];
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

            if ($field instanceof Element) {
                foreach ($field->getValidationRules() as $key => $rules) {
                    $validationRules[$key] = $rules;
                }
            } else {
                if ($field->getRules()) {
                    $validationRules[$field->getValidationKey()] = $field->getRules();
                }

                $validationKeyAttributes[$field->getValidationKey()] = $field->getLabel();
            }

            $fields[] = $field->formattedResponse();
        }

        return [
            'label'                 => 'Entry',
            'fields'                => $fields,
            'replacement_ids'       => $this->getReplacementAttributes(),
            'validation_rules'      => $validationRules,
            'validation_attributes' => $validationKeyAttributes,
        ];
    }

    /**
     * @return array
     */
    private function getEntries(): array
    {
        $entries = [];
        $collection = $this->getRelationCollection()->sortBy($this->getSortableColumn());

        foreach ($collection as $item) {
            $entries[] = $this->formattedEntry($item);
        }

        return $entries;
    }

    /**
     * @param $item
     * @return array
     */
    private function formattedEntry($item): array
    {
        $entry = [
            'label'  => $this->getEntryLabel($item),
            'fields' => [],
            'config' => [
                'is_deleted'   => false,
                'is_collapsed' => $this->isCollapsed(),
            ],
            'id'     => $item->id,
        ];
        if ($this->isNestable()) {
            $entry['fields'][] = (new Hidden('parent_id', $item))
                ->build(array_merge($this->getAttributes(), [$item->id]))
                ->formattedResponse(); // Add hidden ID field
        }

        $entry['fields'][] = (new Hidden('id', $item))
            ->build(array_merge($this->getAttributes(), [$item->id]))
            ->formattedResponse(); // Add hidden ID field

        if ($this->isSortable()) {
            $entry['fields'][] = $this->sortableField($item); // Add hidden sortable field
        }

        foreach ($this->fieldSet->fields as $temporaryField) {
            $field = clone $temporaryField;

            $entry['fields'][] = $field->model($item)
                ->shared($this->isShared())
                ->build(array_merge($this->getAttributes(), [$item->id]))
                ->formattedResponse();
        }

        if ($this->isNestable()) {
            $tree = [
                'id'       => (string)$item->id,
                'text'     => $item->name,
                'parent'   => $item->parent_id ? (string)$item->parent_id : '#',
                'children' => [],
            ];

            if (get_class($item) === config('laradium.menu_item_class', MenuItem::class)) {
                $tree['data'] = [
                    'name'           => $item->name,
                    'url'            => $item->url,
                    'icon'           => $item->icon,
                    'has_permission' => ($resource = $item->getResource()) ? $resource->hasPermission('view') : true,
                ];
            }

            $entry['tree'] = $tree;
        }

        return $entry;
    }

    /**
     * @param $closure
     * @return $this
     */
    public function fields($closure): self
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
    public function actions($value): self
    {
        $this->actions = $value;

        return $this;
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param $value
     * @return $this
     */
    public function collapse($value): self
    {
        $this->isCollapsed = $value;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCollapsed(): bool
    {
        return $this->isCollapsed;
    }

    /**
     * @param $value
     * @return $this
     */
    public function entryLabel($value): self
    {
        $this->entryLabel = $value;

        return $this;
    }

    /**
     * @param Model $model
     * @return string
     */
    public function getEntryLabel(Model $model): string
    {
        if (!is_string($this->entryLabel)) {
            $closure = $this->entryLabel;

            return $closure($model);
        }

        return $model->{$this->entryLabel} ?? 'Entry';
    }

    /**
     * @param bool $value
     * @return HasMany
     */
    public function renderAsTable($value = true): self
    {
        $this->renderAsTable = $value;

        return $this;
    }
}
