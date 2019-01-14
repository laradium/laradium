<?php

namespace Laradium\Laradium\Repositories;

use Exception;
use Illuminate\Support\Collection;
use Laradium\Laradium\Models\Menu;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuRepository
{

    /**
     * @var string
     */
    private $menu;

    /**
     * MenuRepository constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $this->menu = config('laradium.menu_class', \Laradium\Laradium\Models\Menu::Class);
        cache()->rememberForever($this->menu::$cacheKey,
            function () {
                return $this->menu::with([
                    'translations',
                    'items',
                    'items.translations',
                ])->get();
            });
    }

    /**
     * Get the menu with given key.
     *
     * @param string|null $key
     * @return \Illuminate\Support\Collection|\Modules\Admin\Models\Menu
     */
    public function get($key = null)
    {
        if (!$key) {
            return $this->getAll();
        }

        return $this->getAll()->where('key', $key)->first();
    }

    /**
     * Get all menus.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll(): Collection
    {
        return cache()->get($this->menu::$cacheKey);
    }

    /**
     * Seed page menus.
     *
     * @param $menus
     * @return void
     * @throws \Exception
     */
    public function seed($menus): void
    {
        if (!is_iterable($menus)) {
            throw new Exception('Invalid data given!');
        }
        foreach ($menus as $name => $menuItems) {
            $m = $this->menu::firstOrCreate([
                'key' => str_slug($name, '_')
            ]);

            foreach (translate()->languages() as $language) {
                $m->translations()->firstOrCreate([
                    'locale' => $language->iso_code,
                    'name' => $name
                ]);
            }

            foreach ($menuItems as $item) {
                $menuItem = $m->items()->create(array_except($item, 'translations'));

                foreach (translate()->languages() as $language) {
                    $translations = $item['translations'];
                    $translations['locale'] = $language->iso_code;

                    $menuItem->translations()->firstOrCreate($translations);
                }
            }
        }

        cache()->forget($this->menu::$cacheKey);
    }
}