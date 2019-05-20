<?php

namespace Laradium\Laradium\Base;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\Fields\Tab;

class InterfaceBuilder
{

    /**
     * @var Collection
     */
    private $fieldSetFields;

    /**
     * @var Collection
     */
    private $fields;

    /**
     * @var array
     */
    private $data;

    /**
     * InterfaceBuilder constructor.
     */
    public function __construct()
    {
        $this->fields = collect([]);
    }

    /**
     * @return $this
     */
    public function build()
    {
        foreach ($this->getFieldSetFields() as $field) {
            $field->build();

            $this->fields->push($field);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function dataAsJson(): string
    {
        return json_encode($this->data);
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $this->build()->buildData();

        return view('laradium::admin._partials.interface-builder', [
            'builder' => $this
        ])->render();
    }
    
    /**
     * @return array
     */
    public function getData(): array
    {
        $this->build()->buildData();
            
        return $this->data;
    }

    /**
     * @return InterfaceBuilder
     */
    public function buildData(): InterfaceBuilder
    {
        $this->data = [
            'state' => 'success',
            'data'  => [
                'languages'        => translate()->languagesForForm(),
                'fields'           => $this->getFieldsWithFormattedResponse(),
                'is_translatable'  => true,
                'default_language' => translate()->getLanguage()->iso_code,
            ]
        ];

        return $this;
    }

    /**
     * @return array
     */
    private function getFieldsWithFormattedResponse(): array
    {
        $fieldList = [];

        foreach ($this->fields as $field) {
            $response = $field->formattedResponse($field);
            $fieldList[] = $response;
            if ($field instanceof Tab && $response['config']['is_translatable']) {
                $this->isTranslatable = true;
            }
        }

        return $fieldList;
    }

    public function components(Closure $closure)
    {
        $fieldSet = new FieldSet;
        $closure($fieldSet);

        $this->fieldSetFields = $fieldSet->fields();

        return $this;
    }

    /**
     * @return Collection
     */
    private function getFieldSetFields()
    {
        return $this->fieldSetFields;
    }
}