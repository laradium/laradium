<?php

namespace Laradium\Laradium\Base\Fields;

use Closure;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\Element;
use Laradium\Laradium\Base\FieldSet;

class Tabs extends Element
{

    /**
     * @var Collection
     */
    private $tabs;

    /**
     * Block constructor.
     * @param $parameters
     * @param $model
     */
    public function __construct($parameters, $model)
    {
        $this->tabs = collect([]);
        $this->setName('tabs');

        parent::__construct($parameters, $model);
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'name'   => $this->getName(),
            'type'   => 'tabs',
            'tabs'   => $this->getTabs(),
            'config' => [
                'col' => $this->getName(),
            ],
            'attr'   => $this->getAttributes()
        ];
    }

    /**
     * @param string $name
     * @param Closure $closure
     * @return $this
     */
    public function add(string $name, Closure $closure): self
    {
        $fieldSet = new FieldSet;
        $closure($fieldSet);

        $this->tabs->push([
            'name'   => $name,
            'fields' => $this->getTabFields($fieldSet),
            'slug'   => $this->getSlug($name)
        ]);

        return $this;
    }

    /**
     * @param string $name
     * @return string
     */
    private function getSlug(string $name): string
    {
        return str_slug($name);
    }

    /**
     * @param FieldSet $fieldSet
     * @return array
     */
    public function getTabFields(FieldSet $fieldSet): array
    {

        $fields = [];
        foreach ($fieldSet->fields() as $field) {

            $field->build();

            if ($field->isTranslatable()) {
                $this->isTranslatable = true;
            }

            $this->validationRules = array_merge($this->validationRules, $field->getValidationRules());

            $fields[] = $field->formattedResponse();
        }

        return $fields;
    }

    /**
     * @return Collection
     */
    private function getTabs(): Collection
    {
        return $this->tabs;
    }
}
