<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\BackendBaseController;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class DashboardController extends BackendBaseController
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        return view('backend.dashboard.index');
    }
}
