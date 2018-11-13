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
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $foreignKey;

    /**
     * @var string
     */
    protected $name;

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
        $this->name = str_singular(ucfirst($this->tableName));
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
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null|int $id
     * @return mixed
     */
    public function set($id): ?int
    {
        session([$this->getRelation() => $id]);

        return $id;
    }

    /**
     * @return mixed
     */
    public function getCurrent()
    {
        return session()->get($this->getRelation(), null);
    }

    /**
     * @return mixed
     */
    public function getCurrentObject()
    {
        if ($this->getCurrent()) {
            return $this->class::where('id', $this->getCurrent())->first();
        }

        return new $this->class([
            'id'   => null,
            'key'  => 'global',
            'name' => 'Global'
        ]);
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        $global = new $this->class([
            'id'   => null,
            'key'  => 'global',
            'name' => 'Global'
        ]);

        $items = $this->class::all()->prepend($global);

        return $items->reject(function ($item) {
            $userForeignKey = auth()->user()->{$this->getForeignKey()};
            if (is_null($userForeignKey)) {
                return false;
            }

            return $item->id !== $userForeignKey;
        });
    }

    /**
     * @return mixed
     */
    public function getLanguages()
    {
        return auth()->user()->{$this->relation}->languages ?? translate()->languages()->where($this->foreignKey, null);
    }
}