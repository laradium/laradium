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
        'icon'
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
}
