<?php

namespace Laradium\Laradium\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use Translatable;

    /**
     * @var array
     */
    protected $fillable = [
        'is_active',
        'target',
        'sequence_no',
        'icon',
        'resource'
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
     * @return mixed|string
     */
    public function getUrlAttribute()
    {
        if ($this->resource !== '' && class_exists($this->resource)) {
            $url = route('admin.' . (new $this->resource)->getSlug() . '.index');
        } else {
            $url = $this->translateOrNew(session('locale', config('app.locale')))->url;
        }

        return $url ?? '#';
    }
}
