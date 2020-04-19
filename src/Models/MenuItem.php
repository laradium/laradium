<?php

namespace Laradium\Laradium\Models;

use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class MenuItem extends \Baum\Node implements TranslatableContract
{
    use Translatable;

    /**
     *
     * @var array
     */
    protected $fillable = [
        'is_active',
        'target',
        'sequence_no',
        'icon',
        'type',
        'route',
        'resource',
        'parent_id',
    ];

    /**
     * @var array
     */
    protected $translatedAttributes = [
        'name',
        'url',
        'locale',
    ];

    /**
     * @var array
     */
    protected $with = ['translations'];

    /**
     * Get the "default" left column name.
     *
     * @return string
     */
    public function getDefaultLeftColumnName()
    {
        return 'lft';
    }

    /**
     * Get the "default" right column name.
     *
     * @return string
     */
    public function getDefaultRightColumnName()
    {
        return 'rgt';
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->whereIsActive(true);
    }

    /**
     * @return string
     */
    public function getUrlAttribute()
    {
        if ($this->type === 'resource') {
            return $this->getUrlFromResource();
        }

        if ($this->type === 'route') {
            return $this->getUrlFromRoute();
        }

        return $this->translateOrNew(app()->getLocale())->url ?? '';
    }

    /**
     * @return mixed
     */
    public function getResource()
    {
        if (!$this->resource) {
            return null;
        }

        if (!class_exists($this->resource)) {
            return null;
        }

        return new $this->resource;
    }

    /**
     * @return bool
     */
    public function hasPermission(): bool
    {
        return ($resource = $this->getResource()) ? $resource->hasPermission('view') : true;
    }

    /**
     * @return bool
     */
    public function childrenHasPermissions(): bool
    {
        return (bool)collect($this->children)->filter->hasPermission()->count();
    }

    /**
     * @return string
     */
    private function getUrlFromResource()
    {
        if (!$this->resource) {
            return '';
        }

        if (!class_exists($this->resource)) {
            return '';
        }

        $resource = new $this->resource;
        $slug = str_replace('/', '.', $resource->getBaseResource()->getSlug());

        return $resource->isShared() ? route($slug . '.index') : route('admin.' . $slug . '.index');
    }

    /**
     * @return string
     */
    private function getUrlFromRoute()
    {
        try {
            return route($this->route);
        } catch (\Exception $e) {
            return '';
        }
    }
}
