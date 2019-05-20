<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\FormNew;

class Table
{

    private $table;

    /**
     * CrudForm constructor.
     * @param $parameters
     */
    public function __construct($parameters)
    {
        $this->table = array_first($parameters);
    }

    /**
     * @return $this
     */
    public function build(): self
    {
        $this->form->build();

        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'type'   => 'crud',
            'fields' => $this->form->getFormattedFieldResponse(),
            'url'    => $this->form->getUrl(),
            'method' => $this->form->getMethod(),
            'name'   => $this->form->getName(),
            'config' => [
                'is_translatable' => $this->isTranslatable(),
                'col'             => 'col-md-12',
            ],
        ];
    }

    /**
     * @return array
     */
    public function getValidationRules(): array
    {
        return $this->form->getValidationRules();
    }

    /**
     * @return Collection
     */
    public function getFields(): Collection
    {
        return $this->form->getFields();
    }

    /**
     * @return bool
     */
    public function isTranslatable(): bool
    {
        return $this->form->isTranslatable();
    }
}