<?php

namespace Laradium\Laradium\ViewComposers;

use Illuminate\View\View;
use Laradium\Laradium\Models\Menu;
use Laradium\Laradium\Services\Layout;

class MenuComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
       $menuTree = Menu::where('key', 'admin_menu')->first();

        $view->with([
            'items' => $menuTree->getDataForAdminMenu()
        ]);
    }
}