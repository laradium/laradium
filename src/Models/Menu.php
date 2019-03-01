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
    public static $types = [
        'url'      => 'URL',
        'route'    => 'Route',
        'resource' => 'Resource'
    ];

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

    /**
     * @return array
     */
    public function getTree()
    {
        return $this->buildTree($this->items);
    }

    /**
     * @return string
     */
    public function getDataForAdminMenu()
    {
        $items = [];
        foreach ($this->items()->orderBy('sequence_no')->get() as $item) {
            $items[] = [
                'id'     => $item->id,
                'parent' => $item->parent_id ?: '#',
                'data'   => [
                    'name'           => $item->name,
                    'url'            => $item->url,
                    'icon'           => $item->icon,
                    'has_permission' => laradium()->hasPermissionTo(auth()->user(), $item->resource),
                ]
            ];
        }

        return json_encode($items);
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
