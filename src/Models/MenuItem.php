<?php

namespace Laradium\Laradium\Models;

use Dimsav\Translatable\Translatable;

class MenuItem extends \Baum\Node
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
        try {
            return new $this->resource;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * @return string
     */
    private function getUrlFromResource()
    {
        try {
            if (!$this->resource) {
                return '';
            }

            $resource = new $this->resource;
            $slug = $resource->getBaseResource()->getSlug();

            return $resource->isShared() ? route($slug . '.index') : route('admin.' . $slug . '.index');
        } catch (\Exception $e) {
            return '';
        }
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
