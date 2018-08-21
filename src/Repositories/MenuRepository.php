<?php
namespace Netcore\Aven\Repositories;
use Exception;
use Illuminate\Support\Collection;
use Netcore\Aven\Models\Menu;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuRepository
{
    /**
     * Menu key.
     *
     * @var string
     */
    protected $key;
    /**
     * Cached menus collection.
     *
     * @var \Illuminate\Support\Collection
     */
    protected $cachedMenus;
    /**
     * MenuRepository constructor.
     *
     * @return void
     */
    public function __construct()
    {
        $loadWith = ['translations', 'items' => function(HasMany $hasMany) {
            return $hasMany->active()->with('translations')->orderBy('sequence_no');
        }];
        try {
            $this->cachedMenus = cache()->rememberForever(Menu::$cacheKey, function () use ($loadWith) {
                return Menu::with($loadWith)->get();
            });
        } catch (Exception $e) {
            $this->cachedMenus = Menu::with($loadWith)->get();
        }
        $this->key = '';
    }
    /**
     * Set the menu key.
     *
     * @param $key
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
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
        return $this->cachedMenus->where('key', $key)->first();
    }
    /**
     * Get all menus.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getAll(): Collection
    {
        return $this->cachedMenus;
    }
    /**
     * Render menu.
     *
     * @return string
     */
    public function render(): string
    {
        if ($this->key) {
            logger()->warning('Couldn\'t render the menu, because the menu ' . $this->key . ' doesn\'t exist');
        } else {
            logger()->warning('Couldn\'t render the menu without a key');
        }
        return '';
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
            $m = \Netcore\Aven\Models\Menu::create([
                'key' => str_slug($name, '_')
            ]);

            foreach (translate()->languages() as $language) {
                $m->translations()->firstOrCreate([
                    'locale' => $language['iso_code'],
                    'name'   => $name
                ]);
            }

            foreach ($menuItems as $item) {
                $menuItem = $m->items()->create(array_except($item, 'translations'));

                foreach (translate()->languages() as $language) {
                    $translations = $item['translations'];
                    $translations['locale'] = $language['iso_code'];

                    $menuItem->translations()->firstOrCreate($translations);
                }
            }
        }
    }
}