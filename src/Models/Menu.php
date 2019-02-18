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
        foreach ($this->items as $item) {
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

    /**
     * @return array
     */
    public static function getRoutes($type = 'admin')
    {
        $routes = ['' => '- Select -'];

        foreach (\Route::getRoutes() as $route) {
            if (!$route->getName() || !in_array('GET', $route->methods())) {
                continue;
            }

            $action = array_last(explode('.', $route->getName()));
            $name = str_replace(['.', 'admin', 'index'], ' ', $route->getName());
            if ($type === 'admin' &&
                in_array('laradium', $route->middleware()) &&
                in_array($action, ['index', 'create', 'dashboard']) &&
                $route->getName() !== 'admin.index'
            ) {
                $routes[$route->getName()] = ucfirst(trim($name));
            } else if ($type === 'public' &&
                !in_array('laradium', $route->middleware()) &&
                !in_array($action, ['data-table']) &&
                !count($route->parameterNames()) &&
                !str_contains($route->getName(), 'admin.')
            ) {
                $routes[$route->getName()] = ucfirst(trim($name));
            }
        }

        asort($routes);

        return $routes;
    }
}
