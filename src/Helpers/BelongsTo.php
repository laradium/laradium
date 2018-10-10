<?php

namespace Laradium\Laradium\Helpers;

class BelongsTo
{
    /**
     * @var \Illuminate\Config\Repository|mixed
     */
    protected $config;

    /**
     * @var
     */
    protected $class;

    /**
     * @var
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $relation;

    /**
     * BelongsTo constructor.
     */
    public function __construct()
    {
        $this->config = config('laradium.belongsTo', '');
        $this->class = class_exists($this->config) ? (new $this->config) : '';
        $this->tableName = $this->class ? $this->class->getTable() : '';
        $this->foreignKey = str_singular($this->tableName) . '_id';
        $this->label = str_singular(ucfirst($this->tableName));
        $this->relation = str_singular($this->tableName);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return !!class_exists($this->config);
    }

    /**
     * @return mixed
     */
    public function getTable()
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getForeignKey()
    {
        return $this->foreignKey;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return (new \ReflectionClass($this->class))->getShortName();
    }

    /**
     * @return mixed
     */
    public function getFullClass()
    {
        return (new \ReflectionClass($this->class))->getName();
    }

    /**
     * @return string
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * @param \Laradium\Laradium\Base\FieldSet $set
     * @param array $onChange
     * @param bool $languages
     */
    public function getSelect(\Laradium\Laradium\Base\FieldSet $set, $onChange = [], $languages = false, $global = false)
    {
        $options = $this->getOptions($global);

        $select = $set->select($this->foreignKey)
            ->rules('required')
            ->label($this->label)
            ->options($options)
            ->default($global ? array_keys($options)[1] : array_first(array_keys($options)))
            ->hideIf(auth()->user()->{$this->foreignKey});

        if (!auth()->user()->{$this->foreignKey}) {
            if ($onChange && !$languages) {
                $select->onChange($onChange);
            } else if ($onChange && $languages) {
                $array = [];

                foreach ($this->class::all() as $row) {
                    $array[$row->id] = $row->languages ?? [];
                }

                $select->onChange($onChange, $array);
            } else if (!$onChange && $languages) {
                $array = [];

                foreach ($this->class::all() as $row) {
                    $array[$row->id] = $row->languages ?? [];
                }

                $select->onChange([], $array);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getOptions($global = false)
    {
        $options = $this->class::pluck('name', 'id')->toArray();

        return $global ? array_prepend($options, 'Global') : $options;
    }

    /**
     * @return mixed
     */
    public function getLanguages()
    {
        return auth()->user()->{$this->relation}->languages ?? translate()->languages()->where($this->foreignKey, '!=', null);
    }
}