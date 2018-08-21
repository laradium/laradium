<?php

namespace Netcore\Aven\Models;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{

    use Translatable;

    /**
     * @var string
     */
    public static $cacheKey = 'aven::menus';

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'is_active',
    ];

    /**
     * @var array
     */
    protected $translatedAttributes = [
        'name',
        'locale',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items()
    {
        return $this->hasMany(MenuItem::class);
    }
}
