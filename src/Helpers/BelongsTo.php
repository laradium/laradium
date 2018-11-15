<?php

namespace Laradium\Laradium\Helpers;

use Laradium\Laradium\Base\AbstractResource;

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
     * @param null|int $value
     * @return mixed
     */
    public function set($value): ?int
    {
        session([$this->getRelation() => $value]);
        $_SESSION[$this->getRelation()] = $value;

        return $value;
    }

    /**
     * @param null $user
     * @return mixed
     */
    public function getCurrent($user = null)
    {
        $user = $user ?? auth()->user();
        $value = $_SESSION[$this->getRelation()] ?? session()->get($this->getRelation(), null);

        // If admin belongs to domain/region etc.,
        // but session value is null,
        // set it to user's domain/region value
        $belongsToKey = $user->{$this->getForeignKey()} ?? null;
        if ($belongsToKey && !$value || ($belongsToKey && $belongsToKey !== $value)) {
            $value = $belongsToKey;
            $this->set($value);
        }

        return $value;
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
            if ($userForeignKey === null) {
                return false;
            }

            return $item->id !== $userForeignKey;
        });
    }

    /**
     * @return mixed
     */
    public function getAll($global = false)
    {
        if ($global) {
            return $this->class::all()->prepend(new $this->class([
                'id'   => null,
                'key'  => 'global',
                'name' => 'Global'
            ]));
        }

        return $this->class::all();
    }

    /**
     * @return mixed
     */
    public function getLanguages()
    {
        return auth()->user()->{$this->relation}->languages ?? translate()->languages()->where($this->foreignKey, null);
    }

    /**
     * @param $resource
     * @return bool
     */
    public function hasAccess($resource): bool
    {
        $resource = $resource instanceof AbstractResource ? $resource : (class_exists($resource) ? (new $resource) : null);
        if (!$resource) {
            return true;
        }

        $actions = $resource->getGlobalActions();
        if ($actions === 'all') {
            return true;
        }

        return $actions === 'global' && !$this->getCurrent() || $actions === 'belongsto' && $this->getCurrent();
    }
}