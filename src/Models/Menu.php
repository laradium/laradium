<?php

namespace Laradium\Laradium\Models;

use Dimsav\Translatable\Translatable;
use Laradium\Laradium\Models\MenuTranslation;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use Translatable;

    /**
     * @var string
     */
    public static $cacheKey = 'laradium::menus';

    /**
     * @var array
     */
    protected $fillable = [
        'key',
        'is_active',
    ];

    /**
     * @var string
     */
    public $translationModel = MenuTranslation::class;

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
