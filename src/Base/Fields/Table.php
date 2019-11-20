<?php

namespace Laradium\Laradium\Base\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Laradium\Laradium\Base\Form;

class Table
{

    /**
     * @var \Laradium\Laradium\Base\Table
     */
    private $table;

    /**
     * @var bool
     */
    private $shared;

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
        return $this;
    }

    /**
     * @return array
     */
    public function formattedResponse(): array
    {
        return [
            'type'   => 'table',
            'table'  => $this->table->getTableConfig(),
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
        return [];
    }

    /**
     * @return array
     */
    public function getValidationKeyAttributes(): array
    {
        return [];
    }

    /**
     * @return Collection
     */
    public function getFields(): Collection
    {
        return collect([]);
    }

    /**
     * @return bool
     */
    public function isTranslatable(): bool
    {
        return false;
    }

    /**
     * @param $value
     * @return Table
     */
    public function shared($value): self
    {
        $this->shared = $value;

        return $this;
    }
}
