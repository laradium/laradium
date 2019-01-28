<?php

namespace Laradium\Laradium\Base\Fields;

use Laradium\Laradium\Base\Field;
use Laradium\Laradium\Base\FieldSet;

class Boolean extends Field
{

    /**
     * @var FieldSet
     */
    private $fieldSet;

    /**
     * @var array
     */
    private $templateData = [];

    /**
     * BelongsToMany constructor.
     * @param $parameters
     * @param Model $model
     */
    public function __construct($parameters, $model)
    {
        parent::__construct($parameters, $model);

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
    public function formattedResponse(): array
    {
        $data = parent::formattedResponse();
        $data['checked'] = $this->getValue() == 1;
        $data['fields'] = $this->templateData['fields'];

        if (isset($data['translations'])) {
            foreach ($data['translations'] as $key => $translation) {
                $data['translations'][$key]['checked'] = $translation['value'] == 1;
            }
        }

        return $data;
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
     * @return array
     */
    private function getTemplateData()
    {
        $fields = [];
        $validationRules = [];

        foreach ($this->fieldSet->fields as $temporaryField) {
            $field = clone $temporaryField;
            $attributes = $this->getAttributes();
            $lastAttribute = array_pop($attributes);

            $field->model($this->getModel())
                ->build($attributes);

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