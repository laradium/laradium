<?php

namespace Netcore\Aven\Aven\Fields;

use Illuminate\Database\Eloquent\Model;
use Netcore\Aven\Aven\FieldSet;

class Tab
{

    /**
     * @var
     */
    protected $closure;

    /**
     * @var
     */
    protected $fieldSet;

    /**
     * @var mixed
     */
    protected $name;

    /**
     * Tab constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = array_first($name);
    }

    /**
     * @param $closure
     * @return $this
     */
    public function fields($closure)
    {
        $this->closure = $closure;

        return $this;
    }

    /**
     * @param FieldSet $set
     * @return $this
     */
    public function setFieldSet(FieldSet $set)
    {
        $this->fieldSet = $set;

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function build()
    {
        $closure = $this->closure;
        $fieldSet = $this->fieldSet;
        $fieldSet->addTab($this->name);
        $tabFieldSet = new FieldSet();
        $tabFieldSet->setModel($fieldSet->model());
        $closure($tabFieldSet);
        $fields = $tabFieldSet->fields();

        foreach ($fields as $field) {
            $field->setTab($this->name);
            $this->fieldSet->fields->push($field);
        }

        return $tabFieldSet->fields();
    }
}