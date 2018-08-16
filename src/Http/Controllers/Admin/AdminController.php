<?php

namespace Netcore\Aven\Http\Controllers\Admin;

use Illuminate\Http\Request;

class AdminController
{

    public function index()
    {
        return redirect()->to('/admin/dashboard');
    }

    /**
     * @return mixed
     */
    public function dashboard()
    {
        return view(config('aven.dashboard-view'));
    }
}
