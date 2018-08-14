<?php

namespace Netcore\Aven\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AdminController
{

    /**
     * @return mixed
     */
    public function index()
    {
        return view(config('aven.dashboard-view'));
    }
}
