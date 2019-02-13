<?php

namespace Laradium\Laradium\ViewComposers;

use Illuminate\View\View;
use Laradium\Laradium\Services\Layout;

class ResourceComposer
{
    /**
     * Bind data to the view.
     *
     * @param  View $view
     * @return void
     */
    public function compose(View $view)
    {
        $view->with([
            'layout' => new Layout
        ]);
    }
}