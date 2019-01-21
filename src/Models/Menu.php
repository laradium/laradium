<?php

namespace Laradium\Laradium\Models;

use Dimsav\Translatable\Translatable;
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
    protected $translationModel = MenuTranslation::class;

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

    /**
     * @return array
     */
    public function getTree()
    {
        return $this->buildTree($this->items);
    }

    /**
     * @param $elements
     * @param int $parentId
     * @return array
     */
    public function buildTree($elements, $parentId = 0)
    {
        $branch = [];

        foreach ($elements as $element) {
            if ($element->parent_id == $parentId) {
                $children = $this->buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }

        return $branch;
    }
}
